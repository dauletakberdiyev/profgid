<div class="min-h-screen bg-gray-50 py-4 md:py-8 px-4">
    <div class="max-w-7xl mx-auto bg-white rounded-xl p-4 md:p-8 my-4 md:my-8">
        @if (count($userResults) > 0)
            <!-- Tabs Navigation -->
            <div class="mb-6 md:mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <!-- Вкладка "Таланты" - всегда доступна -->
                        <button wire:click="setActiveTab('talents')" 
                            class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'talents' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Топ Таланты
                        </button>
                        
                        <!-- Вкладка "Сферы" - доступна для средний и премиум тарифов -->
                        @if($this->canViewSpheresTab)
                            <button wire:click="setActiveTab('spheres')" 
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'spheres' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Топ Сферы
                            </button>
                        @endif
                        
                        <!-- Вкладка "Профессии" - доступна только для премиум тарифа -->
                        @if($this->canViewProfessionsTab)
                            <button wire:click="setActiveTab('professions')" 
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'professions' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
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
            @if($activeTab === 'talents')
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
            @endif

            @if($activeTab === 'spheres')
                <h2 class="text-lg md:text-xl font-bold mb-4">Все сферы деятельности</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                    На основе ваших топ талантов мы выделили наиболее подходящие сферы деятельности. Яркие сферы соответствуют вашим сильным сторонам.
                </p>
                
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-6 pr-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                    Сфера деятельности
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($topSpheres as $sphere)
                                <tr class="group hover:bg-gray-50/50 transition-colors duration-200 cursor-pointer {{ !$sphere['is_top'] ? 'opacity-50' : 'bg-blue-50 border border-gray-300' }}" 
                                    wire:click="toggleSphereExpanded({{ $sphere['id'] }})"
                                    title="{{ in_array($sphere['id'], $expandedSpheres) ? 'Скрыть описание' : 'Показать описание' }}">
                                    <td class="py-8 pr-8 align-top">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold {{ $sphere['is_top'] ? 'text-gray-900' : 'text-gray-500' }} mb-1">
                                                    {{ $sphere['name'] }}
                                                    @if($sphere['is_top'])
                                                        <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">ТОП</span>
                                                    @endif
                                                </h3>
                                                @if(in_array($sphere['id'], $expandedSpheres))
                                                    <p class="text-sm {{ $sphere['is_top'] ? 'text-gray-700' : 'text-gray-500' }} leading-relaxed mt-2">
                                                        {{ $sphere['description'] ?: 'Описание отсутствует' }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-gray-400 group-hover:text-blue-600 transition-colors duration-200 p-1 ml-4">
                                                <svg class="w-5 h-5 transform transition-transform duration-200 {{ in_array($sphere['id'], $expandedSpheres) ? 'rotate-180' : '' }}" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($topSpheres as $sphere)
                        <div class="border border-gray-200 bg-white cursor-pointer hover:bg-gray-50 transition-colors duration-200 {{ !$sphere['is_top'] ? 'opacity-50' : '' }}"
                             wire:click="toggleSphereExpanded({{ $sphere['id'] }})"
                             title="{{ in_array($sphere['id'], $expandedSpheres) ? 'Скрыть описание' : 'Показать описание' }}">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide w-1/3">
                                            Сфера
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                                <svg class="w-4 h-4 transform transition-transform duration-200 {{ in_array($sphere['id'], $expandedSpheres) ? 'rotate-180' : '' }}" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <td colspan="2" class="py-4 px-6">
                                            <h3 class="text-lg font-semibold {{ $sphere['is_top'] ? 'text-gray-900' : 'text-gray-500' }}">
                                                {{ $sphere['name'] }}
                                                @if($sphere['is_top'])
                                                    <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">ТОП</span>
                                                @endif
                                            </h3>
                                        </td>
                                    </tr>
                                    @if(in_array($sphere['id'], $expandedSpheres))
                                        <tr>
                                            <td colspan="2" class="py-4 px-6 text-sm {{ $sphere['is_top'] ? 'text-gray-700' : 'text-gray-500' }}">
                                                {{ $sphere['description'] ?: 'Описание отсутствует' }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($activeTab === 'professions')
                <h2 class="text-lg md:text-xl font-bold mb-4">Топ Профессии</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                    На основе ваших топ талантов мы подобрали наиболее подходящие профессии.
                </p>
                
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-6 pr-8 text-sm font-medium text-gray-900 tracking-wider uppercase">
                                    Профессия
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($topProfessions as $profession)
                                <tr class="group hover:bg-gray-50/50 transition-colors duration-200 cursor-pointer"
                                    wire:click="toggleProfessionExpanded({{ $profession['id'] }})"
                                    title="{{ in_array($profession['id'], $expandedProfessions) ? 'Скрыть описание' : 'Показать описание' }}">
                                    <td class="py-8 pr-8 align-top">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                                    {{ $profession['name'] }}
                                                </h3>
                                                @if(!empty($profession['sphere_name']))
                                                    <div class="text-xs text-gray-600 mb-1">{{ $profession['sphere_name'] }}</div>
                                                @endif
                                                @if(in_array($profession['id'], $expandedProfessions))
                                                    <p class="text-sm text-gray-700 leading-relaxed mt-2">
                                                        {{ $profession['description'] ?: 'Описание отсутствует' }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-gray-400 group-hover:text-blue-600 transition-colors duration-200 p-1 ml-4">
                                                <svg class="w-5 h-5 transform transition-transform duration-200 {{ in_array($profession['id'], $expandedProfessions) ? 'rotate-180' : '' }}" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($topProfessions as $profession)
                        <div class="border border-gray-200 bg-white cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                             wire:click="toggleProfessionExpanded({{ $profession['id'] }})"
                             title="{{ in_array($profession['id'], $expandedProfessions) ? 'Скрыть описание' : 'Показать описание' }}">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-4 px-6 text-xs font-medium text-gray-500 uppercase tracking-wide w-1/3">
                                            Профессия
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                                <svg class="w-4 h-4 transform transition-transform duration-200 {{ in_array($profession['id'], $expandedProfessions) ? 'rotate-180' : '' }}" 
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <td colspan="2" class="py-4 px-6">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $profession['name'] }}
                                            </h3>
                                            @if(!empty($profession['sphere_name']))
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mt-1 inline-block">{{ $profession['sphere_name'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if(in_array($profession['id'], $expandedProfessions))
                                        <tr>
                                            <td colspan="2" class="py-4 px-6 text-sm text-gray-700">
                                                {{ $profession['description'] ?: 'Описание отсутствует' }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endif


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
