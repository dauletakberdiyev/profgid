<div class="min-h-screen bg-gray-50 py-4 md:py-8 px-4" x-data="{
    activeTab: 'talents',
    canViewSpheresTab: {{ $this->canViewSpheresTab ? 'true' : 'false' }},
    canViewProfessionsTab: {{ $this->canViewProfessionsTab ? 'true' : 'false' }},
    modalSphere: null,
    modalProfession: null,
    
    setActiveTab(tab) {
        // Проверяем права доступа к вкладкам
        if (tab === 'spheres' && !this.canViewSpheresTab) {
            return; // Не переключаем вкладку если нет доступа
        }
        if (tab === 'professions' && !this.canViewProfessionsTab) {
            return; // Не переключаем вкладку если нет доступа
        }
        this.activeTab = tab;
    },
    
    openSphereModal(sphere) {
        this.modalSphere = sphere;
    },
    
    closeSphereModal() {
        this.modalSphere = null;
    },
    
    openProfessionModal(profession) {
        this.modalProfession = profession;
    },
    
    closeProfessionModal() {
        this.modalProfession = null;
    }
}">
    <div class="max-w-7xl mx-auto bg-white rounded-xl p-4 md:p-8 my-4 md:my-8">
        @if (count($userResults) > 0)
            <!-- Tabs Navigation -->
            <div class="mb-6 md:mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <!-- Вкладка "Таланты" - всегда доступна -->
                        <button @click="setActiveTab('talents')" 
                            :class="activeTab === 'talents' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            Топ Таланты
                        </button>
                        
                        <!-- Вкладка "Сферы" - доступна для средний и премиум тарифов -->
                        @if($this->canViewSpheresTab)
                            <button @click="setActiveTab('spheres')" 
                                :class="activeTab === 'spheres' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                Топ Сферы
                            </button>
                        @endif
                        
                        <!-- Вкладка "Профессии" - доступна только для премиум тарифа -->
                        @if($this->canViewProfessionsTab)
                            <button @click="setActiveTab('professions')" 
                                :class="activeTab === 'professions' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                Топ Профессии
                            </button>
                        @endif
                    </nav>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row md:justify-between md:items-start mb-4 md:mb-6">
                <div class="mb-4 md:mb-0">
                    @php
                        // Найдем домен с максимальным счетом
                        $topDomain = array_keys($domainScores, max($domainScores))[0] ?? 'executing';
                        $topDomainName = $domains[$topDomain] ?? 'EXECUTING';
                    @endphp
                    <h1 class="text-xl md:text-2xl font-medium text-gray-900 mb-2 md:mb-4 leading-relaxed">
                        Вы лидируете с <span class="font-extrabold">{{ $topDomainName }}</span> темами.
                    </h1>
                </div>
                
                <!-- PDF Download Button -->
                <button class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors group self-start" 
                        title="Скачать результаты в PDF">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-600 group-hover:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Tab Content -->
            <div x-show="activeTab === 'talents'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <!-- Domain Bar Chart -->
                <div class="mb-4 md:mb-6">
                    @php
                        $domainColors = [
                            'executing' => '#702B7C',
                            'relationship' => '#316EC6',
                            'strategic' => '#429162',
                            'influencing' => '#DA782D',
                        ];
                        
                        $domainBgColors = [
                            'executing' => 'bg-[#702B7C]',
                            'relationship' => 'bg-[#316EC6]',
                            'strategic' => 'bg-[#429162]',
                            'influencing' => 'bg-[#DA782D]',
                        ];
                        
                        // Calculate total score and percentages
                        $totalScore = array_sum($domainScores);
                        $totalScore = $totalScore > 0 ? $totalScore : 1;
                        
                        // Calculate percentages directly here
                        $domainPercentages = [];
                        foreach ($domainScores as $domain => $score) {
                            $domainPercentages[$domain] = round(($score / $totalScore) * 100, 1);
                        }
                    @endphp

                    <!-- Single horizontal bar -->
                    <div class="flex gap-1 w-full h-6 md:h-8 overflow-hidden mb-3 md:mb-4">
                        @foreach ($domainScores as $domain => $score)
                            @php
                                $percentage = ($score / $totalScore) * 100;
                            @endphp
                            @if($score > 0)
                                <div class="{{ $domainBgColors[$domain] ?? 'bg-gray-400' }} flex items-center justify-center text-white font-bold text-xs md:text-sm" 
                                     style="width: {{ $percentage }}%">
                                    
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Domain labels with scores and percentages -->
                    <div class="flex w-full">
                        @foreach ($domainScores as $domain => $score)
                            @php
                                $percentage = ($score / $totalScore) * 100;
                            @endphp
                            @if($score > 0)
                                <div class="text-left" style="width: {{ $percentage }}%">
                                    <div class="text-xs md:text-sm font-medium text-gray-700">{{ $domains[$domain] }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <p class="text-xs md:text-sm text-gray-600 mb-6 md:mb-8 leading-relaxed">Этот график показывает относительное распределение ваших уникальных
                    результатов по четырем доменам. Эти категории являются хорошей отправной точкой для изучения областей, в
                    которых у вас есть наибольший потенциал для достижения совершенства и того, как вы можете наилучшим
                    образом внести свой вклад в команду.</p>

                <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Ваши таланты по доменам</h2>

                <!-- Talent Grid by Domain -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-1 mb-6 md:mb-8">
                    @foreach ($domains as $domain => $name)
                        <div class="mb-4 md:mb-0">
                            <!-- Domain Header -->
                            <div class="text-center font-bold uppercase text-xs md:text-sm mb-2 md:mb-3 pb-2 text-gray-800 p-2 border-b-8" 
                                 style="border-color: {{ $domainColors[$domain] }}">
                                {{ $name }}
                            </div>
                            
                            @php
                                $domainTalents = array_filter($userResults, function ($talent) use ($domain) {
                                    return $talent['domain'] === $domain;
                                });
                                usort($domainTalents, function ($a, $b) {
                                    return $a['rank'] <=> $b['rank'];
                                });
                                
                                // Находим 8 талантов с наименьшими очками из всех результатов
                                $bottomTalentsByScore = collect($userResults)->sortByDesc('score')->take(8);
                                $bottomTalentIds = $bottomTalentsByScore->pluck('id')->toArray();
                            @endphp

                            @php
                                $mutedBgColors = [
                                    'executing' => 'bg-[#E9DCEB]',
                                    'influencing' => 'bg-[#FDEAD9]',
                                    'relationship' => 'bg-[#E1E8F6]',
                                    'strategic' => 'bg-[#D8EEE4]',
                                ];
                            @endphp

                            <div class="grid grid-cols-2 gap-1">
                                @foreach ($domainTalents as $talent)
                                    @php
                                        $bgColor = in_array($talent['id'], $bottomTalentIds) 
                                            ? ($domainBgColors[$domain] ?? 'bg-gray-400')
                                            : ($mutedBgColors[$domain] ?? 'bg-gray-200');
                                        $textColor = in_array($talent['id'], $bottomTalentIds) ? 'text-white' : 'text-black';
                                    @endphp
                                    <div class="{{ $bgColor }} {{ $textColor }} text-center aspect-square flex flex-col items-center justify-center">
                                        <div class="text-3xl md:text-xl">{{ $talent['rank'] }}</div>
                                        <div class="text-xs px-1 text-center leading-tight mt-3">{{ $talent['name'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div x-show="activeTab === 'spheres'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h2 class="text-lg md:text-xl font-bold mb-4">Все сферы деятельности</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                    На основе ваших топ талантов мы выделили наиболее подходящие сферы деятельности с процентом совместимости.
                </p>
                
                @php
                    $topEightSpheres = collect($topSpheres)->filter(function($sphere) {
                        return $sphere['is_top'];
                    })->toArray();
                    $remainingSpheres = collect($topSpheres)->filter(function($sphere) {
                        return !$sphere['is_top'];
                    })->toArray();
                @endphp
                
                <!-- Топ 8 сфер -->
                @if(count($topEightSpheres) > 0)
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Топ рекомендации для вас</h3>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 space-y-2">
                            @foreach($topEightSpheres as $sphere)
                                <div class="bg-white border border-blue-100 hover:border-blue-300 transition-colors duration-200 px-6 py-4 rounded-lg flex items-center justify-between shadow-sm">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="text-base font-medium text-gray-900">{{ $sphere['name'] }}</h4>
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                                            {{ $sphere['compatibility_percentage'] }}%
                                        </span>
                                    </div>
                                    
                                    <button @click="openSphereModal({{ json_encode($sphere) }})" 
                                            class="text-blue-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                            title="Показать описание">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Остальные сферы -->
                @if(count($remainingSpheres) > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Остальные сферы деятельности</h3>
                        <div class="space-y-2">
                            @foreach($remainingSpheres as $sphere)
                                <div class="border border-gray-200 bg-white hover:bg-gray-50 transition-colors duration-200 px-6 py-3 flex items-center justify-between opacity-70">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="text-base font-medium text-gray-600">{{ $sphere['name'] }}</h4>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $sphere['compatibility_percentage'] }}%
                                        </span>
                                    </div>
                                    
                                    <button @click="openSphereModal({{ json_encode($sphere) }})" 
                                            class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                            title="Показать описание">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Modal for Sphere Description -->
                <div x-show="modalSphere" 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 flex items-center justify-center z-50 p-4"
                     style="background-color: rgba(0, 0, 0, 0.5);"
                     @click="closeSphereModal()">
                    <div @click.stop 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200" 
                         x-transition:leave-start="opacity-100 transform scale-100" 
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="modalSphere?.name"></h3>
                            <button @click="closeSphereModal()" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed" x-text="modalSphere?.description || 'Описание отсутствует'"></p>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'professions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h2 class="text-lg md:text-xl font-bold mb-4">Все профессии</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                    На основе ваших топ талантов мы подобрали наиболее подходящие профессии.
                </p>
                
                @php
                    $topEightProfessions = collect($topProfessions)->take(8)->toArray();
                    $remainingProfessions = collect($topProfessions)->skip(8)->toArray();
                @endphp
                
                <!-- Топ 8 профессий -->
                @if(count($topEightProfessions) > 0)
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Топ рекомендации для вас</h3>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 space-y-2">
                            @foreach($topEightProfessions as $index => $profession)
                                <div class="bg-white border border-blue-100 hover:border-blue-300 transition-colors duration-200 px-6 py-4 rounded-lg flex items-center justify-between shadow-sm">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="text-base font-medium text-gray-900">{{ $profession['name'] }}</h4>
                                        <span class="text-xs bg-blue-600 text-white px-3 py-1 rounded-full font-medium">
                                            {{ $profession['compatibility_percentage'] }}%
                                        </span>
                                    </div>
                                    
                                    <button @click="openProfessionModal({{ json_encode($profession) }})" 
                                            class="text-blue-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                            title="Показать описание">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <!-- Остальные профессии -->
                @if(count($remainingProfessions) > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Остальные профессии</h3>
                        <div class="space-y-2">
                            @foreach($remainingProfessions as $profession)
                                <div class="border border-gray-200 bg-white hover:bg-gray-50 transition-colors duration-200 px-6 py-3 flex items-center justify-between opacity-70">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <h4 class="text-base font-medium text-gray-600">{{ $profession['name'] }}</h4>
                                            @if(!empty($profession['sphere_name']))
                                                <div class="text-xs text-gray-500 mt-1">{{ $profession['sphere_name'] }}</div>
                                            @endif
                                        </div>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                            {{ $profession['compatibility_percentage'] }}%
                                        </span>
                                    </div>
                                    
                                    <button @click="openProfessionModal({{ json_encode($profession) }})" 
                                            class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                            title="Показать описание">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Modal for Profession Description -->
                <div x-show="modalProfession" 
                     x-transition:enter="transition ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 flex items-center justify-center z-50 p-4"
                     style="background-color: rgba(0, 0, 0, 0.5);"
                     @click="closeProfessionModal()">
                    <div @click.stop 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 transform scale-95" 
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200" 
                         x-transition:leave-start="opacity-100 transform scale-100" 
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900" x-text="modalProfession?.name"></h3>
                                <div class="text-sm text-blue-600 mt-1" x-text="modalProfession?.sphere_name || ''"></div>
                            </div>
                            <button @click="closeProfessionModal()" 
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed" x-text="modalProfession?.description || 'Описание отсутствует'"></p>
                    </div>
                </div>
            </div>


        @else
            <div class="text-center py-12">
                <h2 class="text-2xl font-bold mb-4">У вас пока нет результатов</h2>
                <p class="text-gray-600 mb-8">Вы еще не прошли тест или не сохранили ответы.</p>
                <a href="{{ route('test') }}"
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">Пройти
                    тест</a>
            </div>
        @endif
    </div>
</div>
