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
    public $activeTab = 'talents'; // Добавляем управление табами
    public $expandedSpheres = []; // Управление раскрытием сфер
    public $expandedProfessions = []; // Управление раскрытием профессий
    
    public function setActiveTab($tab)
    {
        // Проверяем права доступа к вкладкам
        if ($tab === 'spheres' && !$this->canViewSpheresTab) {
            return; // Не переключаем вкладку если нет доступа
        }
        
        if ($tab === 'professions' && !$this->canViewProfessionsTab) {
            return; // Не переключаем вкладку если нет доступа
        }
        
        $this->activeTab = $tab;
    }
    
    public function toggleSphereExpanded($sphereId)
    {
        if (in_array($sphereId, $this->expandedSpheres)) {
            $this->expandedSpheres = array_filter($this->expandedSpheres, function($id) use ($sphereId) {
                return $id !== $sphereId;
            });
        } else {
            $this->expandedSpheres[] = $sphereId;
        }
    }
    
    public function toggleProfessionExpanded($professionId)
    {
        if (in_array($professionId, $this->expandedProfessions)) {
            $this->expandedProfessions = array_filter($this->expandedProfessions, function($id) use ($professionId) {
                return $id !== $professionId;
            });
        } else {
            $this->expandedProfessions[] = $professionId;
        }
    }
    
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
        
        // Проверяем и корректируем активную вкладку в зависимости от доступа
        $this->validateActiveTab();
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
        // Получаем топ 8 талантов с наименьшими очками (самые слабые)
        $topTalentsByScore = collect($this->userResults)->sortBy('score')->take(8);
        $topTalentIds = $topTalentsByScore->pluck('id')->toArray();
        
        // Получаем все сферы
        $allSpheres = \App\Models\Sphere::all();
        $spheresData = collect();
        
        foreach ($allSpheres as $sphere) {
            // Проверяем, связана ли сфера с топ 8 талантами через профессии
            $isTopSphere = false;
            $relevanceScore = 999; // Большое число для не-топ сфер
            
            foreach ($topTalentIds as $talentId) {
                $talentModel = \App\Models\Talent::find($talentId);
                if ($talentModel && $talentModel->professions) {
                    foreach ($talentModel->professions as $profession) {
                        if ($profession->sphere && $profession->sphere->id === $sphere->id) {
                            $isTopSphere = true;
                            // Находим лучший (наименьший) ранг среди связанных талантов
                            $talentRank = collect($this->userResults)->where('id', $talentId)->first()['rank'] ?? 999;
                            $relevanceScore = min($relevanceScore, $talentRank);
                        }
                    }
                }
            }
            
            $spheresData->push([
                'id' => $sphere->id,
                'name' => $sphere->name,
                'description' => $sphere->description ?? '',
                'is_top' => $isTopSphere,
                'relevance_score' => $relevanceScore
            ]);
        }
        
        // Сортируем: сначала топ сферы по релевантности, потом остальные
        return $spheresData->sortBy(function($sphere) {
            return $sphere['is_top'] ? $sphere['relevance_score'] : (1000 + $sphere['relevance_score']);
        });
    }
    
    public function getTopProfessions()
    {
        // Получаем топ 8 талантов с наименьшими очками (самые слабые)
        $topTalentsByScore = collect($this->userResults)->sortBy('score')->take(8);
        $topTalentIds = $topTalentsByScore->pluck('id')->toArray();
        
        $professionsData = collect();
        
        foreach ($topTalentIds as $talentId) {
            $talentModel = \App\Models\Talent::find($talentId);
            if ($talentModel && $talentModel->professions) {
                foreach ($talentModel->professions as $profession) {
                    // Находим ранг таланта для сортировки
                    $talentRank = collect($this->userResults)->where('id', $talentId)->first()['rank'] ?? 999;
                    
                    $professionsData->push([
                        'id' => $profession->id,
                        'name' => $profession->name,
                        'description' => $profession->description ?? '',
                        'sphere_name' => $profession->sphere ? $profession->sphere->name : '',
                        'is_top' => true, // Все профессии топовые, так как показываем только топ
                        'relevance_score' => $talentRank
                    ]);
                }
            }
        }
        
        // Убираем дубликаты и сортируем по релевантности
        return $professionsData->unique('id')->sortBy('relevance_score');
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
    
    /**
     * Валидирует и корректирует активную вкладку в зависимости от доступа
     */
    private function validateActiveTab()
    {
        // Если активная вкладка "spheres", но нет доступа - переключаем на "talents"
        if ($this->activeTab === 'spheres' && !$this->canViewSpheresTab) {
            $this->activeTab = 'talents';
        }
        
        // Если активная вкладка "professions", но нет доступа - переключаем на доступную
        if ($this->activeTab === 'professions' && !$this->canViewProfessionsTab) {
            $this->activeTab = $this->canViewSpheresTab ? 'spheres' : 'talents';
        }
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
