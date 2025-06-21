<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Answer;
use App\Models\Talent;
use App\Models\UserAnswer;
use App\Models\TestSession;
use Illuminate\Support\Facades\Auth;

class TalentTestResults extends Component
{
    public $userResults = [];
    public $domains = [];
    public $domainScores = [];
    public $topStrengths = [];
    public $averageResponseTime = 0;
    public $testSession = null;
    public $testSessionId = null;
    public $totalTimeSpent = 0;
    public $testDate = null;
    public $answersCount = 0;
    
    public function mount($sessionId = null)
    {
        // Получаем session_id из параметра URL или из сессии
        $sessionIdToUse = $sessionId ?? session('last_test_session_id');
        
        if ($sessionIdToUse) {
            // Найдем TestSession по session_id
            $this->testSession = TestSession::where('session_id', $sessionIdToUse)
                ->where('user_id', Auth::id())
                ->with(['userAnswers' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->first();
        }
        
        // Если не найдена сессия, попробуем найти последнюю завершенную сессию пользователя
        if (!$this->testSession) {
            $this->testSession = TestSession::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->with(['userAnswers' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->latest('completed_at')
                ->first();
        }
        
        if (!$this->testSession || $this->testSession->userAnswers->isEmpty()) {
            // Если нет данных, показываем сообщение об отсутствии результатов
            $this->userResults = [];
            $this->domainScores = [];
            $this->topStrengths = [];
            return;
        }
        
        // Обновляем временные метрики сессии
        $this->testSession->updateTimeMetrics();
        
        // Устанавливаем данные сессии
        $this->testSessionId = $this->testSession->session_id;
        $this->averageResponseTime = $this->testSession->average_response_time ?? 0;
        $this->totalTimeSpent = $this->testSession->total_time_spent ?? 0;
        $this->testDate = $this->testSession->completed_at ?? $this->testSession->created_at;
        $this->answersCount = $this->testSession->userAnswers->count();
        
        $this->calculateResults();
    }
    private function calculateResults()
    {
        $userAnswers = $this->testSession->userAnswers;
        
        // Get all talents with their domains, ordered by ID for consistency
        $talents = Talent::with('domain')->orderBy('id')->get();
        
        // Get all answers with their talent relationships
        $answers = Answer::with('talent.domain')->orderBy('id')->get();
        
        // Define the domains with exact names from database
        $this->domains = [
            'executing' => 'EXECUTING',
            'influencing' => 'INFLUENCING', 
            'relationship' => 'RELATIONSHIP BUILDING',
            'strategic' => 'STRATEGIC THINKING'
        ];
        
        // Initialize domain scores
        foreach ($this->domains as $key => $domain) {
            $this->domainScores[$key] = 0;
        }
        
        // Group answers by question_id and sum values (in case of duplicates)
        $questionScores = [];
        foreach ($userAnswers as $userAnswer) {
            $questionId = $userAnswer->question_id;
            if (!isset($questionScores[$questionId])) {
                $questionScores[$questionId] = 0;
            }
            $questionScores[$questionId] += $userAnswer->answer_value;
        }
        
        // Initialize talent scores
        $talentScores = [];
        foreach ($talents as $talent) {
            $talentScores[$talent->id] = 0;
        }
        
        // Calculate scores for each talent by grouping answers to each talent
        foreach ($questionScores as $questionId => $score) {
            $questionIndex = $questionId - 1; // Convert to 0-based index (1-87 -> 0-86)
            if ($questionIndex >= 0 && $questionIndex < count($answers)) {
                $answer = $answers[$questionIndex];
                if ($answer->talent) {
                    // Add the score to the corresponding talent
                    $talentScores[$answer->talent->id] += $score;
                }
            }
        }
        
        // Build results and calculate domain scores
        foreach ($talents as $talent) {
            $score = $talentScores[$talent->id] ?? 0;
            $domainName = $talent->domain ? $talent->domain->name : 'executing';
            
            $this->userResults[] = [
                'id' => $talent->id,
                'name' => $talent->name,
                'domain' => $domainName,
                'score' => $score,
                'rank' => 0 // Will be set later
            ];
            
            // Add talent score to corresponding domain
            if (isset($this->domainScores[$domainName])) {
                $this->domainScores[$domainName] += $score;
            }
        }
        
        // Sort results by score (descending)
        usort($this->userResults, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Assign ranks
        for ($i = 0; $i < count($this->userResults); $i++) {
            $this->userResults[$i]['rank'] = $i + 1;
        }
        
        // Get top strengths (up to 5 or whatever is available)
        $this->topStrengths = array_slice($this->userResults, 0, min(5, count($this->userResults)));
    }
    
    public function getFormattedTimeSpentProperty()
    {
        if (!$this->totalTimeSpent || $this->totalTimeSpent <= 0) {
            return 'Нет данных';
        }

        $hours = floor($this->totalTimeSpent / 3600);
        $minutes = floor(($this->totalTimeSpent % 3600) / 60);
        $seconds = $this->totalTimeSpent % 60;

        if ($hours > 0) {
            return sprintf('%dч %dм %dс', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dм %dс', $minutes, $seconds);
        } else {
            return sprintf('%dс', $seconds);
        }
    }
    
    public function getTestStatusProperty()
    {
        if (!$this->testSession) {
            return 'Демо данные';
        }
        
        return $this->testSession->status === 'completed' ? 'Завершен' : 'В процессе';
    }
    
    public function getPaymentStatusProperty()
    {
        if (!$this->testSession) {
            return null;
        }
        
        return $this->testSession->payment_status;
    }
    
    public function getDomainPercentagesProperty()
    {
        $totalScore = array_sum($this->domainScores);
        $percentages = [];
        
        if ($totalScore > 0) {
            foreach ($this->domainScores as $domain => $score) {
                $percentages[$domain] = round(($score / $totalScore) * 100, 1);
            }
        } else {
            foreach ($this->domainScores as $domain => $score) {
                $percentages[$domain] = 0;
            }
        }
        
        return $percentages;
    }
    
    public function getTopSpheres()
    {
        // Создаем массив с очками пользователя по талантам
        $userTalentScores = [];
        foreach ($this->userResults as $result) {
            $userTalentScores[$result['id']] = $result['score'];
        }
        
        // Находим максимальный возможный балл для нормализации
        $maxUserScore = collect($this->userResults)->max('score');
        $maxUserScore = max($maxUserScore, 1); // Избегаем деления на 0
        
        // Получаем все сферы с профессиями и талантами
        $allSpheres = \App\Models\Sphere::with(['professions.talents'])->get();
        $spheresData = collect();
        
        foreach ($allSpheres as $sphere) {
            $compatibilityPercentage = 0;
            
            // Собираем все уникальные таланты сферы через профессии
            $sphereTalents = collect();
            foreach ($sphere->professions as $profession) {
                foreach ($profession->talents as $talent) {
                    // Избегаем дубликатов, но сохраняем самый высокий коэффициент
                    $existingTalent = $sphereTalents->firstWhere('id', $talent->id);
                    if (!$existingTalent || $talent->pivot->coefficient > $existingTalent->coefficient) {
                        $sphereTalents = $sphereTalents->reject(function($t) use ($talent) {
                            return $t->id === $talent->id;
                        });
                        $sphereTalents->push((object)[
                            'id' => $talent->id,
                            'name' => $talent->name,
                            'coefficient' => $talent->pivot->coefficient
                        ]);
                    }
                }
            }
            
            // Вычисляем процент совместимости на основе талантов сферы
            if ($sphereTalents->count() > 0) {
                $totalWeightedScore = 0;
                $totalWeight = 0;
                $matchingTalentsCount = 0;
                
                foreach ($sphereTalents as $talent) {
                    $userScore = $userTalentScores[$talent->id] ?? 0;
                    $coefficient = $talent->coefficient ?? 0.5;
                    
                    // Нормализуем очки пользователя относительно максимального балла
                    $normalizedScore = $userScore / $maxUserScore;
                    
                    // Взвешиваем по коэффициенту важности таланта для сферы
                    $weightedScore = $normalizedScore * $coefficient;
                    
                    $totalWeightedScore += $weightedScore;
                    $totalWeight += $coefficient;
                    
                    // Считаем количество "совпадающих" талантов (где есть хоть какие-то очки)
                    if ($userScore > 0) {
                        $matchingTalentsCount++;
                    }
                }
                
                // Базовый процент совместимости
                if ($totalWeight > 0) {
                    $baseCompatibility = ($totalWeightedScore / $totalWeight) * 100;
                    
                    // Бонус за покрытие талантов (чем больше талантов сферы у пользователя, тем лучше)
                    $coverageBonus = ($matchingTalentsCount / $sphereTalents->count()) * 10; // до 10% бонуса
                    
                    $compatibilityPercentage = min($baseCompatibility + $coverageBonus, 100); // Ограничиваем 100%
                }
            }
            
            $spheresData->push([
                'id' => $sphere->id,
                'name' => $sphere->name,
                'description' => $sphere->description ?? '',
                'is_top' => false, // Будет установлено после сортировки
                'relevance_score' => 999,
                'compatibility_percentage' => round($compatibilityPercentage, 1)
            ]);
        }
        
        // Сортируем: сначала по проценту совместимости (убывание)
        $sortedSpheres = $spheresData->sortByDesc('compatibility_percentage');
        
        // Помечаем первые 8 как топовые
        $topSpheres = $sortedSpheres->map(function($sphere, $index) {
            $sphere['is_top'] = $index < 8; // Только первые 8 сфер топовые
            return $sphere;
        });
        
        return $topSpheres;
    }
    
    
    public function getTopProfessions()
    {
        // Создаем массив с очками пользователя по талантам
        $userTalentScores = [];
        foreach ($this->userResults as $result) {
            $userTalentScores[$result['id']] = $result['score'];
        }
        
        // Находим максимальный возможный балл для нормализации
        $maxUserScore = collect($this->userResults)->max('score');
        $maxUserScore = max($maxUserScore, 1); // Избегаем деления на 0
        
        // Получаем все профессии с их талантами
        $allProfessions = \App\Models\Profession::with(['talents', 'sphere'])->get();
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
                    
                    // Нормализуем очки пользователя относительно максимального балла
                    $normalizedScore = $userScore / $maxUserScore;
                    
                    // Взвешиваем по коэффициенту важности таланта для профессии
                    $weightedScore = $normalizedScore * $coefficient;
                    
                    $totalWeightedScore += $weightedScore;
                    $totalWeight += $coefficient;
                    
                    // Считаем количество "совпадающих" талантов (где есть хоть какие-то очки)
                    if ($userScore > 0) {
                        $matchingTalentsCount++;
                    }
                }
                
                // Базовый процент совместимости
                if ($totalWeight > 0) {
                    $baseCompatibility = ($totalWeightedScore / $totalWeight) * 100;
                    
                    // Бонус за покрытие талантов (чем больше талантов профессии у пользователя, тем лучше)
                    $coverageBonus = ($matchingTalentsCount / $profession->talents->count()) * 10; // до 10% бонуса
                    
                    $compatibilityPercentage = min($baseCompatibility + $coverageBonus, 100); // Ограничиваем 100%
                }
            }
            
            $professionsData->push([
                'id' => $profession->id,
                'name' => $profession->name,
                'description' => $profession->description ?? '',
                'sphere_name' => $profession->sphere ? $profession->sphere->name : '',
                'is_top' => false, // Будет установлено после сортировки
                'relevance_score' => 999,
                'compatibility_percentage' => round($compatibilityPercentage, 1)
            ]);
        }
        
        // Сортируем: сначала по проценту совместимости (убывание), затем по имени (по возрастанию)
        $sortedProfessions = $professionsData
            ->sortByDesc('compatibility_percentage')
            ->sortBy('name')
            ->sortByDesc('compatibility_percentage') // Повторная сортировка по проценту для финального порядка
            ->values();
        
        // Помечаем первые 8 как топовые
        $topProfessions = $sortedProfessions->map(function($profession, $index) {
            $profession['is_top'] = $index < 8; // Только первые 8 профессий топовые
            return $profession;
        });
        
        return $topProfessions;
    }
    
    /**
     * Проверяет, доступна ли вкладка "Топ сферы" для текущего тарифа
     */
    public function getCanViewSpheresTabProperty()
    {
        if (!$this->testSession) {
            return false; // По умолчанию не показываем если нет сессии
        }
        
        // Проверяем оплачена ли сессия
        if (!$this->testSession->isPaid()) {
            return false;
        }
        
        // Проверяем тарифный план
        $allowedPlans = ['talents_spheres', 'talents_spheres_professions'];
        return in_array($this->testSession->selected_plan, $allowedPlans);
    }
    
    /**
     * Проверяет, доступна ли вкладка "Топ профессии" для текущего тарифа
     */
    public function getCanViewProfessionsTabProperty()
    {
        if (!$this->testSession) {
            return false; // По умолчанию не показываем если нет сессии
        }
        
        // Проверяем оплачена ли сессия
        if (!$this->testSession->isPaid()) {
            return false;
        }
        
        // Проверяем тарифный план - только полный план
        return $this->testSession->selected_plan === 'talents_spheres_professions';
    }
    
    
    public function render()
    {
        $spheres = $this->getTopSpheres();
        $topSphereIds = $spheres->where('is_top', true)->pluck('id')->toArray();
        
        $professions = $this->getTopProfessions();
        $topProfessionIds = $professions->where('is_top', true)->pluck('id')->toArray();
        
        return view('livewire.pages.talent-test-results', [
            'userResults' => $this->userResults,
            'domains' => $this->domains,
            'domainScores' => $this->domainScores,
            'topStrengths' => $this->topStrengths,
            'domainPercentages' => $this->getDomainPercentagesProperty(),
            'topSpheres' => $spheres,
            'topSphereIds' => $topSphereIds,
            'topProfessions' => $professions,
            'topProfessionIds' => $topProfessionIds
        ]);
    }
}
