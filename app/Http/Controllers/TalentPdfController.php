<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade as PDF;
use App\Models\TestSession;
use App\Models\Talent;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class TalentPdfController extends Controller
{
    public function download(Request $request)
    {
        $sessionId = $request->get("session_id");
        $tab = $request->get("tab", "talents");
        $testSession = TestSession::where("session_id", $sessionId)
            ->where("user_id", Auth::id())
            ->with([
                "userAnswers" => function ($query) {
                    $query->orderBy("created_at", "asc");
                },
            ])
            ->first();
        if (!$testSession) {
            abort(404, "Session not found");
        }
        // Общие данные
        $userResults = [];
        $domains = [
            "executing" => "EXECUTING",
            "influencing" => "INFLUENCING",
            "relationship" => "RELATIONSHIP BUILDING",
            "strategic" => "STRATEGIC THINKING",
        ];
        $domainScores = [
            "executing" => 0,
            "influencing" => 0,
            "relationship" => 0,
            "strategic" => 0,
        ];
        $talents = Talent::with("domain")->orderBy("id")->get();
        $answers = Answer::with("talent.domain")->orderBy("id")->get();
        $questionScores = [];
        foreach ($testSession->userAnswers as $userAnswer) {
            $questionId = $userAnswer->question_id;
            if (!isset($questionScores[$questionId])) {
                $questionScores[$questionId] = 0;
            }
            $questionScores[$questionId] += $userAnswer->answer_value;
        }
        $talentScores = [];
        foreach ($talents as $talent) {
            $talentScores[$talent->id] = 0;
        }
        foreach ($questionScores as $questionId => $score) {
            $questionIndex = $questionId - 1;
            if ($questionIndex >= 0 && $questionIndex < count($answers)) {
                $answer = $answers[$questionIndex];
                if ($answer->talent) {
                    $talentScores[$answer->talent->id] += $score;
                }
            }
        }
        foreach ($talents as $talent) {
            $score = $talentScores[$talent->id] ?? 0;
            $domainName = $talent->domain ? $talent->domain->name : "executing";
            $userResults[] = [
                "id" => $talent->id,
                "name" => $talent->name,
                "description" => $talent->description ?? "",
                "short_description" => $talent->short_description ?? "",
                "advice" => $talent->advice ?? "",
                "domain" => $domainName,
                "score" => $score,
                "rank" => 0,
            ];
            if (isset($domainScores[$domainName])) {
                $domainScores[$domainName] += $score;
            }
        }
        usort($userResults, function ($a, $b) {
            return $b["score"] <=> $a["score"];
        });
        for ($i = 0; $i < count($userResults); $i++) {
            $userResults[$i]["rank"] = $i + 1;
        }
        $maxScore = max(array_column($userResults, "score"));
        $topTenTalents = array_slice(
            array_filter($userResults, function ($talent) {
                return !empty($talent["description"]);
            }),
            0,
            10
        );
        $talentAdvice = [];
        foreach ($topTenTalents as $talent) {
            $talentAdvice[$talent["name"]] = app(
                "App\\Livewire\\Pages\\TalentTestResults"
            )->getTalentAdvice($talent["name"]);
        }
        $domainColors = [
            "executing" => "#702B7C",
            "relationship" => "#316EC6",
            "strategic" => "#429162",
            "influencing" => "#DA782D",
        ];
        $testDate = $testSession->completed_at ?? $testSession->created_at;
        $userName = Auth::user()->name ?? "User";
        $fileName =
            "Отчет_" . $userName . "_" . now()->format("Y-m-d") . ".pdf";
        // Данные для разных вкладок
        if ($tab === "talents") {
            $html = view("pdf.talent-full", [
                "userResults" => $userResults,
                "domains" => $domains,
                "domainScores" => $domainScores,
                "testDate" => $testDate,
                "talentAdvice" => $talentAdvice,
                "domainColors" => $domainColors,
            ])->render();

            $header = view("pdf.header", [
                "userName" => $userName,
                "testDate" => $testDate,
            ])->render();

            $footer = view("pdf.footer", [
                "userName" => $userName,
                "testDate" => $testDate,
            ])->render();

            $pdf = Browsershot::html($html)
                ->noSandbox()
                ->showBackground()
                ->format("A4")
                ->scale(0.6)
                ->waitUntilNetworkIdle()
                ->margins(5, 5, 5, 5)
                ->pdf();

            return response($pdf)
                ->header("Content-Type", "application/pdf")
                ->header(
                    "Content-Disposition",
                    'attachment; filename="отчет-' .
                        $userName .
                        "_" .
                        now()->format("Y-m-d") .
                        '.pdf"'
                );
        } elseif ($tab === "spheres") {
            // Логика topSpheres и topProfessions как в компоненте
            // --- topSpheres ---
            $userTalentScores = [];
            foreach ($userResults as $result) {
                $userTalentScores[$result["id"]] = $result["score"];
            }
            $maxUserScore = max(array_column($userResults, "score"));
            $maxUserScore = max($maxUserScore, 1);
            $allSpheres = \App\Models\Sphere::with([
                "professions.talents",
            ])->get();
            $spheresData = collect();
            foreach ($allSpheres as $sphere) {
                $compatibilityPercentage = 0;
                $sphereTalents = collect();
                foreach ($sphere->professions as $profession) {
                    foreach ($profession->talents as $talent) {
                        $existingTalent = $sphereTalents->firstWhere(
                            "id",
                            $talent->id
                        );
                        if (
                            !$existingTalent ||
                            $talent->pivot->coefficient >
                                $existingTalent->coefficient
                        ) {
                            $sphereTalents = $sphereTalents->reject(function (
                                $t
                            ) use ($talent) {
                                return $t->id === $talent->id;
                            });
                            $sphereTalents->push(
                                (object) [
                                    "id" => $talent->id,
                                    "name" => $talent->name,
                                    "coefficient" =>
                                        $talent->pivot->coefficient,
                                ]
                            );
                        }
                    }
                }
                if ($sphereTalents->count() > 0) {
                    $totalWeightedScore = 0;
                    $totalWeight = 0;
                    $matchingTalentsCount = 0;
                    foreach ($sphereTalents as $talent) {
                        $userScore = $userTalentScores[$talent->id] ?? 0;
                        $coefficient = $talent->coefficient ?? 0.5;
                        $normalizedScore = $userScore / $maxUserScore;
                        $weightedScore = $normalizedScore * $coefficient;
                        $totalWeightedScore += $weightedScore;
                        $totalWeight += $coefficient;
                        if ($userScore > 0) {
                            $matchingTalentsCount++;
                        }
                    }
                    if ($totalWeight > 0) {
                        $baseCompatibility =
                            ($totalWeightedScore / $totalWeight) * 100;
                        $coverageBonus =
                            ($matchingTalentsCount / $sphereTalents->count()) *
                            10;
                        $compatibilityPercentage = min(
                            $baseCompatibility + $coverageBonus,
                            100
                        );
                    }
                }
                $spheresData->push([
                    "id" => $sphere->id,
                    "name" => $sphere->name,
                    "description" => $sphere->description ?? "",
                    "is_top" => false,
                    "relevance_score" => 999,
                    "compatibility_percentage" => round(
                        $compatibilityPercentage,
                        1
                    ),
                ]);
            }
            $sortedSpheres = $spheresData->sortByDesc(
                "compatibility_percentage"
            );
            $topSpheres = $sortedSpheres->map(function ($sphere, $index) {
                $sphere["is_top"] = $index < 8;
                return $sphere;
            });
            // --- topProfessions ---
            $allProfessions = \App\Models\Profession::with([
                "talents",
                "sphere",
            ])->get();
            $professionsData = collect();
            foreach ($allProfessions as $profession) {
                $compatibilityPercentage = 0;
                if ($profession->talents && $profession->talents->count() > 0) {
                    $totalWeightedScore = 0;
                    $totalWeight = 0;
                    $matchingTalentsCount = 0;
                    foreach ($profession->talents as $talent) {
                        $userScore = $userTalentScores[$talent->id] ?? 0;
                        $coefficient = $talent->pivot->coefficient ?? 1.0;
                        $normalizedScore = $userScore / $maxUserScore;
                        $weightedScore = $normalizedScore * $coefficient;
                        $totalWeightedScore += $weightedScore;
                        $totalWeight += $coefficient;
                        if ($userScore > 0) {
                            $matchingTalentsCount++;
                        }
                    }
                    if ($totalWeight > 0) {
                        $baseCompatibility =
                            ($totalWeightedScore / $totalWeight) * 100;
                        $coverageBonus =
                            ($matchingTalentsCount /
                                $profession->talents->count()) *
                            10;
                        $compatibilityPercentage = min(
                            $baseCompatibility + $coverageBonus,
                            100
                        );
                    }
                }
                $professionsData->push([
                    "id" => $profession->id,
                    "name" => $profession->name,
                    "description" => $profession->description ?? "",
                    "sphere_id" => $profession->sphere
                        ? $profession->sphere->id
                        : null,
                    "sphere_name" => $profession->sphere
                        ? $profession->sphere->name
                        : "",
                    "is_top" => false,
                    "relevance_score" => 999,
                    "compatibility_percentage" => round(
                        $compatibilityPercentage,
                        1
                    ),
                ]);
            }
            $sortedProfessions = $professionsData
                ->sortByDesc("compatibility_percentage")
                ->sortBy("name")
                ->sortByDesc("compatibility_percentage")
                ->values();
            $topProfessions = $sortedProfessions->map(function (
                $profession,
                $index
            ) {
                $profession["is_top"] = $index < 8;
                return $profession;
            });
            $html = view("pdf.spheres-full", [
                "topSpheres" => $topSpheres,
                "topProfessions" => $topProfessions,
                "testDate" => $testDate,
            ])->render();

            $header = view("pdf.header", [
                "userName" => $userName,
                "testDate" => $testDate,
            ])->render();

            $pdf = Browsershot::html($html)
                ->noSandbox()
                ->showBackground()
                ->scale(0.6)
                ->format("A4")
                ->waitUntilNetworkIdle()
                ->headerHtml($header)
                ->margins(5, 5, 5, 5)
                ->pdf();

            return response($pdf)
                ->header("Content-Type", "application/pdf")
                ->header(
                    "Content-Disposition",
                    'attachment; filename="spheres.pdf"'
                );
        } elseif ($tab === "professions") {
            // topProfessions как в компоненте
            $userTalentScores = [];
            foreach ($userResults as $result) {
                $userTalentScores[$result["id"]] = $result["score"];
            }
            $maxUserScore = max(array_column($userResults, "score"));
            $maxUserScore = max($maxUserScore, 1);
            $allProfessions = \App\Models\Profession::with([
                "talents",
                "sphere",
            ])->get();
            $professionsData = collect();
            foreach ($allProfessions as $profession) {
                $compatibilityPercentage = 0;
                if ($profession->talents && $profession->talents->count() > 0) {
                    $totalWeightedScore = 0;
                    $totalWeight = 0;
                    $matchingTalentsCount = 0;
                    foreach ($profession->talents as $talent) {
                        $userScore = $userTalentScores[$talent->id] ?? 0;
                        $coefficient = $talent->pivot->coefficient ?? 1.0;
                        $normalizedScore = $userScore / $maxUserScore;
                        $weightedScore = $normalizedScore * $coefficient;
                        $totalWeightedScore += $weightedScore;
                        $totalWeight += $coefficient;
                        if ($userScore > 0) {
                            $matchingTalentsCount++;
                        }
                    }
                    if ($totalWeight > 0) {
                        $baseCompatibility =
                            ($totalWeightedScore / $totalWeight) * 100;
                        $coverageBonus =
                            ($matchingTalentsCount /
                                $profession->talents->count()) *
                            10;
                        $compatibilityPercentage = min(
                            $baseCompatibility + $coverageBonus,
                            100
                        );
                    }
                }
                $professionsData->push([
                    "id" => $profession->id,
                    "name" => $profession->name,
                    "description" => $profession->description ?? "",
                    "sphere_id" => $profession->sphere
                        ? $profession->sphere->id
                        : null,
                    "sphere_name" => $profession->sphere
                        ? $profession->sphere->name
                        : "",
                    "is_top" => false,
                    "relevance_score" => 999,
                    "compatibility_percentage" => round(
                        $compatibilityPercentage,
                        1
                    ),
                ]);
            }
            $sortedProfessions = $professionsData
                ->sortByDesc("compatibility_percentage")
                ->sortBy("name")
                ->sortByDesc("compatibility_percentage")
                ->values();
            $topProfessions = $sortedProfessions->map(function (
                $profession,
                $index
            ) {
                $profession["is_top"] = $index < 8;
                return $profession;
            });
            $html = view("pdf.professions-full", [
                "topProfessions" => $topProfessions,
                "testDate" => $testDate,
            ])->render();

            $header = view("pdf.header", [
                "userName" => $userName,
                "testDate" => $testDate,
            ])->render();

            $pdf = Browsershot::html($html)
                ->noSandbox()
                ->showBackground()
                ->format("A4")
                ->scale(0.6)
                ->waitUntilNetworkIdle()
                ->headerHtml($header)
                ->margins(5, 5, 5, 5)
                ->pdf();

            return response($pdf)
                ->header("Content-Type", "application/pdf")
                ->header(
                    "Content-Disposition",
                    'attachment; filename="professions.pdf"'
                );
        } else {
            abort(400, "Unknown tab");
        }
    }
}
