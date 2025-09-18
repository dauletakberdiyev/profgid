<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\Sphere;
use Illuminate\Http\Request;
use App\Models\TestSession;
use App\Models\Talent;
use App\Models\Answer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class TalentPdfController extends Controller
{
    private array $userTalentScores = [];
    /**
     * @throws CouldNotTakeBrowsershot
     */
    public function download(Request $request)
    {
        ini_set('memory_limit', '1G');
        $sessionId = $request->get("session_id");
        $plan = $request->get("plan"); // Получаем тарифный план
        /** @var TestSession $testSession */
        $testSession = TestSession::query()->where("session_id", $sessionId)
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
        $domainScores = [
            "executing" => 0,
            "influencing" => 0,
            "relationship" => 0,
            "strategic" => 0,
        ];

        $domainsDescription = [
            'ru' => [
                "executing" => "Вы умеете доводить дела до конца, брать на себя ответственность и организовывать процесс.
                    Ваши таланты из блока Исполнение помогают превращать планы в конкретные результаты и достигать поставленных целей.",
                "influencing" => "Вы умеете брать на себя инициативу, уверенно выражать свои мысли и вдохновлять других на действия.
                    Ваши таланты из блока Влияние помогают мотивировать окружающих, убеждать их и добиваться значимых изменений.",
                "relationship" => "Вы умеете строить доверие, ценить искренние связи и объединять людей вокруг себя.
                    Ваши таланты из блока Отношения помогают укреплять командный дух, создавать атмосферу поддержки и формировать прочные связи.",
                "strategic" => "Вы умеете анализировать информацию, видеть картину целиком и предлагать нестандартные решения.
                    Ваши таланты из блока Мышление помогают находить стратегии, строить прогнозы и двигаться к будущему с ясным видением.",
            ],
            'kk' => [
                "executing" => "Сіз істерді соңына дейін жеткізіп, жауапкершілікті өз мойныңызға алып, процесті ұйымдастыра аласыз.
                    Сіздің Орындау блогындағы таланттарыңыз жоспарларды нақты нәтижеге айналдыруға және қойылған мақсаттарға жетуге көмектеседі.",
                "influencing" => "Сіз бастамашыл болып, ойыңызды нық жеткізіп, өзгелерді әрекетке шабыттандыра аласыз.
                    Сіздің Ықпал ету блогындағы таланттарыңыз айналаңыздағы адамдарды жігерлендіруге, оларды сендіруге және маңызды өзгерістерге қол жеткізуге көмектеседі.",
                "relationship" => "Сіз сенім орнатып, шынайы қарым-қатынастарды бағалап, адамдарды өзіңізге топтастыра аласыз.
                    Сіздің Қарым-қатынас блогындағы таланттарыңыз командалық рухты нығайтуға, қолдау атмосферасын қалыптастыруға және берік байланыстар орнатуға көмектеседі.",
                "strategic" => "Сіз ақпаратты талдап, жалпы көріністі көре аласыз және өзгеше шешімдер ұсына аласыз.
                    Сіздің Ойлау блогындағы таланттарыңыз стратегияларды табуға, болашақты болжауға және айқын көзқараспен алға жылжуға көмектеседі.",
            ]
        ];

        $localizedDomains = [
            'ru' => [
                "executing" => "ИСПОЛНЕНИЕ",
                "influencing" => "ВЛИЯНИЕ",
                "relationship" => "ОТНОШЕНИЯ",
                "strategic" => "МЫШЛЕНИЕ",
            ],
            'kk' => [
                "executing" => "ОРЫНДАУ",
                "influencing" => "ЫҚПАЛ ЕТУ",
                "relationship" => "ҚАРЫМ-ҚАТЫНАС",
                "strategic" => "СТРАТЕГИЯЛЫҚ ОЙЛАУ",
            ]
        ];

        // Переопределяем домены для правильного отображения всех 4 доменов
        $allDomains = [
            'executing' => 'ИСПОЛНЕНИЕ',
            'influencing' => 'ВЛИЯНИЕ',
            'relationship' => 'ОТНОШЕНИЯ',
            'strategic' => 'МЫШЛЕНИЕ'
        ];

        /** @var Talent[] $talents */
        $talents = Talent::with("domain")->orderBy("id")->get();
        $answers = Answer::with("talent.domain")->orderBy("id")->get();


        // Убираем расчёты очков и процентов для доменов
        $domainScores = [];
        foreach ($allDomains as $domainKey => $domainName) {
            $domainScores[$domainKey] = 0;
        }

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
            // Находим ответ (вопрос) по его ID
            $answer = $answers->firstWhere('id', $questionId);
            if ($answer && $answer->talent) {
                // Add the score to the corresponding talent
                $talentScores[$answer->talent->id] += $score;
            }
        }

        foreach ($talents as $talent) {
            $score = $talentScores[$talent->id] ?? 0;
            $domainName = $talent->domain ? $talent->domain->name : "executing";

            $domainKey = $this->mapDomainNameToKey($domainName);

            $userResults[] = [
                "id" => $talent->id,
                "name" => $talent->localizedName,
                "description" => $talent->localizedDescription ?? "",
                "short_description" => $talent->localizedShortDescription ?? "",
                "advice" => $talent->advice ?? "",
                "domain" => $domainKey,
                "score" => $score,
                "rank" => 0,
            ];

            if (isset($domainScores[$domainKey])) {
                $domainScores[$domainKey] += $score;
            }
        }

        usort($userResults, function ($a, $b) {
            return $b["score"] <=> $a["score"];
        });

        for ($i = 0; $i < count($userResults); $i++) {
            $userResults[$i]["rank"] = $i + 1;
        }

        $topTenTalents = array_slice(
            array_filter($userResults, function ($talent) {
                return !empty($talent["description"]);
            }),
            0,
            10
        );

        $talentAdvice = [];
        foreach ($topTenTalents as $talent) {
            // Get advice directly from the Talent model instead of using Livewire component
            $talentModel = Talent::query()->where('id', $talent["id"])->first();
            $talentAdvice[$talent["name"]] = $talentModel && !empty($talentModel->advice)
                ? $talentModel->advice
                : null;
        }

        $domainColors = [
            "executing" => "#702B7C",
            "relationship" => "#316EC6",
            "strategic" => "#429162",
            "influencing" => "#DA782D",
        ];

        $topDomain = array_keys($domainScores, max($domainScores))[0];
        arsort($domainScores);
        $testDate = $testSession->completed_at->format('Y-m-d') ?? $testSession->created_at;
        $userName = Auth::user()->name ?? "User";

        // Генерация PDF в зависимости от тарифного плана
        if ($plan === "talents") {
            $html = view("pdf.talent-full", [
                "userResults" => $userResults,
                "domains" => $allDomains,
                "domainScores" => $domainScores,
                "testDate" => $testDate,
                "talentAdvice" => $talentAdvice,
                "domainColors" => $domainColors,
                "plan" => $plan,
                "localizedDomains" => $localizedDomains,
                "topDomain" => $topDomain,
                "domainsDescription" => $domainsDescription
            ])->render();

            $pdf = Browsershot::html($html)
                ->setNodeBinary(env('NODE_PATH'))
                ->setNpmBinary(env('NPM_PATH'))
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
                    'attachment; filename="' .
                    $userName .
                    "_" .
                    now()->format("Y-m-d") .
                    '.pdf"'
                );
        } elseif ($plan === "talents_spheres") {
            $talentsHtml = view("pdf.talent-full", [
                "userResults" => $userResults,
                "domains" => $allDomains,
                "domainScores" => $domainScores,
                "testDate" => $testDate,
                "talentAdvice" => $talentAdvice,
                "domainColors" => $domainColors,
                "plan" => $plan,
                "localizedDomains" => $localizedDomains,
                "topDomain" => $topDomain,
                "domainsDescription" => $domainsDescription
            ])->render();

            foreach ($userResults as $result) {
                $this->userTalentScores[$result["id"]] = $result["score"];
            }

            $maxUserScore = max(array_column($userResults, "score"));
            $maxUserScore = max($maxUserScore, 1);

            // Затем логика для сфер
            $topSpheres = $this->getTopSpheres($userResults, $maxUserScore);
            $sortedProfessions = $this->getTopSphereProfessions($topSpheres, $maxUserScore);

            // Генерируем HTML для сфер
            $spheresHtml = view("pdf.spheres-full", [
                "topSpheres" => $topSpheres,
                "topProfessions" => $sortedProfessions,
                "testDate" => $testDate,
                "isFullPlan" => true,
            ])->render();

            // Объединяем HTML талантов и сфер
            $html = $talentsHtml . '<div style="page-break-before: always;"></div>' . $spheresHtml;

            $pdf = Browsershot::html($html)
                ->setNodeBinary(env('NODE_PATH'))
                ->setNpmBinary(env('NPM_PATH'))
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
                    'attachment; filename="'.
                    $userName .
                    "_" .
                    now()->format("Y-m-d") .
                    '.pdf"'
                );
        } elseif ($plan === "talents_spheres_professions") {
            // Генерируем PDF с талантами + сферами + профессиями
            // Сначала генерируем HTML для талантов
            $talentsHtml = view("pdf.talent-full", [
                "userResults" => $userResults,
                "domains" => $allDomains,
                "domainScores" => $domainScores,
                "testDate" => $testDate,
                "talentAdvice" => $talentAdvice,
                "domainColors" => $domainColors,
                "plan" => $plan,
                "localizedDomains" => $localizedDomains,
                "topDomain" => $topDomain,
                "domainsDescription" => $domainsDescription
            ])->render();

            // Логика для сфер (копируем из talents_spheres)
            foreach ($userResults as $result) {
                $this->userTalentScores[$result["id"]] = $result["score"];
            }

            $maxUserScore = max(array_column($userResults, "score"));
            $maxUserScore = max($maxUserScore, 1);

            $topSpheres = $this->getTopSpheres($userResults, $maxUserScore);
            $sortedProfessions = $this->getTopSphereProfessions($topSpheres, $maxUserScore);

            // Генерируем HTML для сфер
            $spheresHtml = view("pdf.spheres-full", [
                "topSpheres" => $topSpheres,
                "topProfessions" => $sortedProfessions,
                "testDate" => $testDate,
            ])->render();

            // Генерируем HTML для профессий
            $professionsHtml = view("pdf.professions-full", [
                "topProfessions" => $sortedProfessions,
                "testDate" => $testDate,
            ])->render();

            // Объединяем HTML всех трех разделов: Таланты + Сферы + Профессии
            $html = $talentsHtml .
                '<div style="page-break-before: always;"></div>' .
                $spheresHtml .
                '<div style="page-break-before: always;"></div>' .
                $professionsHtml;

            $pdf = Browsershot::html($html)
                ->setNodeBinary(env('NODE_PATH'))
                ->setNpmBinary(env('NPM_PATH'))
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
                    'attachment; filename="' .
                    $userName .
                    "_" .
                    now()->format("Y-m-d") .
                    '.pdf"'
                );
        } else {
            abort(400, "Unknown plan or plan not provided");
        }
    }

    private function mapDomainNameToKey($domainName)
    {
        // Сопоставление различных вариантов названий доменов
        $mapping = [
            'executing' => 'executing',
            'EXECUTING' => 'executing',
            'Executing' => 'executing',
            'ИСПОЛНЕНИЕ' => 'executing',
            'исполнение' => 'executing',
            'Исполнение' => 'executing',

            'influencing' => 'influencing',
            'INFLUENCING' => 'influencing',
            'Influencing' => 'influencing',
            'ВЛИЯНИЕ' => 'influencing',
            'влияние' => 'influencing',
            'Влияние' => 'influencing',

            'relationship' => 'relationship',
            'RELATIONSHIP' => 'relationship',
            'Relationship' => 'relationship',
            'RELATIONSHIP BUILDING' => 'relationship',
            'relationship building' => 'relationship',
            'Relationship Building' => 'relationship',
            'ОТНОШЕНИЯ' => 'relationship',
            'отношения' => 'relationship',
            'Отношения' => 'relationship',

            'strategic' => 'strategic',
            'STRATEGIC' => 'strategic',
            'Strategic' => 'strategic',
            'STRATEGIC THINKING' => 'strategic',
            'strategic thinking' => 'strategic',
            'Strategic Thinking' => 'strategic',
            'МЫШЛЕНИЕ' => 'strategic',
            'мышление' => 'strategic',
            'Мышление' => 'strategic',
        ];

        return $mapping[$domainName] ?? 'executing'; // По умолчанию executing
    }

    private function getTopSpheres(array $userResults, $maxUserScore): Collection
    {
        /** @var Sphere[] $allSpheres */
        $allSpheres = Sphere::with([
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
                                "coefficient" => $talent->pivot->coefficient,
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
                    $userScore = $this->userTalentScores[$talent->id] ?? 0;
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
                        ($matchingTalentsCount / $sphereTalents->count()) * 10;

                    $compatibilityPercentage = min(
                        $baseCompatibility + $coverageBonus,
                        100
                    );
                }
            }
            $spheresData->push([
                "id" => $sphere->id,
                "name" => $sphere->localizedName,
                "description" => $sphere->localizedDescription ?? "",
                "is_top" => false,
                "relevance_score" => 999,
                "compatibility_percentage" => round(
                    $compatibilityPercentage,
                    1
                ),
            ]);
        }
        $sortedSpheres = $spheresData->sortByDesc("compatibility_percentage");

        return $sortedSpheres->map(function ($sphere, $index) {
            $sphere["is_top"] = $index < 8;
            return $sphere;
        });
    }

    private function getTopSphereProfessions(Collection $topSpheres, $maxUserScore): Collection
    {
        /** @var Profession[] $allProfessions */
        $allProfessions = Profession::with([
            "talents",
            "sphere",
        ])
//            ->whereIn('sphere_id', $topSpheres->take(10)->pluck('id')->toArray())
            ->get();
        $professionsData = collect();

        foreach ($allProfessions as $profession) {
            $compatibilityPercentage = 0;

            if ($profession->talents && $profession->talents->count() > 0) {
                $totalWeightedScore = 0;
                $totalWeight = 0;
                $matchingTalentsCount = 0;

                foreach ($profession->talents as $talent) {
                    $userScore = $this->userTalentScores[$talent->id] ?? 0;
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
                "name" => $profession->localizedName,
                "description" => $profession->localizedDescription ?? "",
                "sphere_id" => $profession->sphere
                    ? $profession->sphere->id
                    : null,
                "sphere_name" => $profession->sphere
                    ? $profession->sphere->localizedName
                    : "",
                "is_top" => false,
                "relevance_score" => 999,
                "compatibility_percentage" => round(
                    $compatibilityPercentage,
                    1
                ),
            ]);
        }

        return $professionsData
            ->sortByDesc("compatibility_percentage")
            ->sortBy("name")
            ->sortByDesc("compatibility_percentage")
            ->values();
    }
}
