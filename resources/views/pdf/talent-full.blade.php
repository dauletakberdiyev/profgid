<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Полный отчет по талантам</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body style="font-family: sans-serif; margin: 0; padding: 0; color: #333; background-color: #fff;">

<header class="bg-white fixed top-0 left-0 right-0">
    <div class="flex w-full justify-between items-center pb-2">

        <h1>PROFGID</h1>

        <h1><b>{{ auth()->user()->name }}</b> | 12324</h1>

    </div>

    <hr>
</header>

<div id="talents-section">
    <!-- Domain Bar Chart -->
    <div class="mb-4 md:mb-6 mt-20" id="talents-section-p$df">
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

            // Sort domains by score (descending)
            $sortedDomainScores = $domainScores;
            arsort($sortedDomainScores);

            // Calculate percentages directly here
            $domainPercentages = [];
            foreach ($sortedDomainScores as $domain => $score) {
                $domainPercentages[$domain] = round(($score / $totalScore) * 100);
            }
        @endphp

        <!-- Single horizontal bar -->
        <div class="flex gap-1 w-full h-6 md:h-8 overflow-hidden mb-3 md:mb-4">
            @foreach ($sortedDomainScores as $domain => $score)
                @php
                    $percentage = ($score / $totalScore) * 100;
                @endphp
                @if ($score > 0)
                    <div class="{{ $domainBgColors[$domain] ?? 'bg-gray-400' }} flex items-center justify-center text-white font-bold text-xs md:text-sm"
                        style="width: {{ $percentage }}%">

                    </div>
                @endif
            @endforeach
        </div>

        <!-- Domain labels with scores and percentages -->
        <div class="flex w-full">
            @foreach ($sortedDomainScores as $domain => $score)
                @php
                    $percentage = ($score / $totalScore) * 100;
                @endphp
                @if ($score > 0)
                    <div class="text-left" style="width: {{ $percentage }}%">
                        <div class="text-xs md:text-sm font-medium text-gray-700">{{ $domains[$domain] }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <p class="text-xs md:text-sm text-gray-600 mb-6 md:mb-8 leading-relaxed">
        Вы умеете брать на себя инициативу, уверенно выражать свои мысли и вдохновлять других на действия.
        Ваши таланты из блока
        Влияние помогают мотивировать окружающих, убеждать их и добиваться значимых изменений
    </p>

    <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Ваши таланты по доменам</h2>

    <div class="grid grid-cols-4 gap-3 md:gap-1 mb-6 md:mb-8">
        @foreach ($domains as $domain => $name)
            <div class="mb-4 md:mb-0">
                <div class="text-center font-semibold uppercase text-sm mb-2 md:mb-3 pb-1 md:pb-2 text-gray-800 p-1 md:p-2 border-b-4 md:border-b-8"
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

                    $topTalentsByScore = collect($userResults)->sortByDesc('score')->take(10);
                    $topTalentIds = $topTalentsByScore->pluck('id')->toArray();
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
                            $bgColor = in_array($talent['id'], $topTalentIds)
                                ? $domainBgColors[$domain] ?? 'bg-gray-400'
                                : $mutedBgColors[$domain] ?? 'bg-gray-200';
                            $textColor = in_array($talent['id'], $topTalentIds)
                                ? 'text-white'
                                : 'text-black';
                        @endphp
                        <div
                            class="{{ $bgColor }} {{ $textColor }} text-center aspect-square flex flex-col items-center justify-center p-1">
                            <div class="text-lg md:text-xl font-bold">{{ $talent['rank'] }}</div>
                            <div class="text-xs md:text-xs px-1 text-center leading-tight mt-1">{{ $talent['name'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="space-y-5">
        <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Цифры в профиле показывают, какие таланты у
            тебя выражены сильнее всего:</h2>

        <span class="block">
            Топ-8 талантов: Они выделены ярким цветом, чтобы показать их важность. Это твои главные сильные
            стороны, которые ты чаще
            всего используешь
        </span>

        <span class="block">
            9–16: Эти таланты тоже заметны, но немного меньше.
        </span>

        <span class="block">
            17–24: Эти таланты менее выражены, но это не слабости, просто ты используешь их реже.
        </span>
    </div>

    <div style="page-break-before: always;"></div>

    <!-- Таланты блок -->
    <div class="mt-20 space-y-6" x-data="{ expandedTalents: [] }">
        <h2 class="text-lg md:text-xl font-bold text-center">Описание ваших талантов</h2>

        @php
            $topTenTalents = collect($userResults)->take(10)->toArray();
            $remainingTalents = collect($userResults)->skip(10)->toArray();

            // Вычисляем максимальный балл для расчета процентов
            $maxScore = collect($userResults)->max('score');
            $maxScore = $maxScore > 0 ? $maxScore : 1; // Избегаем деления на 0
        @endphp

        <div class="grid grid-cols-2 gap-6">
            <!-- Левая колонка - Топ 10 талантов -->
            <div>
                <div class="space-y-1">
                    @foreach ($topTenTalents as $index => $talent)
                        @php
                            // Получаем цвет домена для таланта
                            $talentDomainColor = $domainColors[$talent['domain']] ?? '#6B7280';
                            // Вычисляем процент
                            $percentage = round(($talent['score'] / $maxScore) * 100);
                            $talentId = 'talent_' . $talent['id'];
                        @endphp

                        <!-- Все топ 10 талантов с аккордеоном -->
                        <div class="border-gray-200 p-1.5 transition-all hover:bg-blue-100 bg-blue-50"
                            style="border-left: 4px solid {{ $talentDomainColor }}">
                            <!-- Заголовок таланта -->
                            <div class="flex items-center justify-between cursor-pointer"
                                @click="expandedTalents.includes('{{ $talentId }}') ? expandedTalents.splice(expandedTalents.indexOf('{{ $talentId }}'), 1) : expandedTalents.push('{{ $talentId }}')">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                        style="background-color: {{ $talentDomainColor }}">
                                        {{ $talent['rank'] }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 truncate">{{ $talent['name'] }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Аккордеон для обзора и советов -->
                            <div class="overflow-hidden mt-2"
                                x-show="expandedTalents.includes('{{ $talentId }}')"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-96"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 max-h-96"
                                x-transition:leave-end="opacity-0 max-h-0">

                                <!-- Краткое описание - показывается в аккордеоне -->
                                @if (!empty($talent['short_description']))
                                    <div class="mb-1">
                                        <!-- <p class="text-xs text-gray-700 leading-tight">{{ $talent['short_description'] }}</p> -->
                                        <p class="text-xs text-gray-700 leading-tight">
                                        Lorem ipsum dolor sit amet consectetur, adipisicing elit. Aliquam officia dicta eaque atque quam. Blanditiis voluptas molestiae dolores, nostrum voluptatibus sequi, quibusdam ipsa sapiente minima architecto soluta iste dicta ipsum?
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Правая колонка - Остальные таланты -->
            <div>
                <div class="space-y-1">
                    @foreach ($remainingTalents as $talent)
                        @php
                            // Получаем цвет домена для таланта
                            $talentDomainColor = $domainColors[$talent['domain']] ?? '#6B7280';
                            // Вычисляем процент
                            $percentage = round(($talent['score'] / $maxScore) * 100);
                            $remainingTalentId = 'remaining_talent_' . $talent['id'];
                        @endphp

                        <!-- Остальные таланты - с аккордеоном для краткого описания -->
                        <div class="bg-gray-50 p-1.5 transition-all hover:bg-gray-100"
                            style="border-left: 4px solid {{ $talentDomainColor }}">

                            <!-- Заголовок таланта с аккордеоном -->
                            <div class="flex items-center justify-between cursor-pointer"
                                @click="expandedTalents.includes('{{ $remainingTalentId }}') ? expandedTalents.splice(expandedTalents.indexOf('{{ $remainingTalentId }}'), 1) : expandedTalents.push('{{ $remainingTalentId }}')">
                                <div class="flex items-center flex-1 min-w-0">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                        style="background-color: {{ $talentDomainColor }}">
                                        {{ $talent['rank'] }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs font-bold text-gray-900 truncate">{{ $talent['name'] }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Аккордеон для краткого описания -->
                            @if (!empty($talent['short_description']))
                                <div class="overflow-hidden mt-2"
                                    x-show="expandedTalents.includes('{{ $remainingTalentId }}')"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-96"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 max-h-96"
                                    x-transition:leave-end="opacity-0 max-h-0">

                                    <!-- Краткое описание для остальных талантов -->
                                    <div class="mb-1">
                                        <!-- <p class="text-xs text-gray-700 leading-tight">{{ $talent['short_description'] }}</p> -->
                                        <p class="text-xs text-gray-700 leading-tight">
                                        Lorem ipsum dolor, sit amet consectetur adipisicing elit. Animi excepturi nulla voluptas tempora tenetur, rem laborum quisquam quia doloremque eligendi sunt id repudiandae, consequatur quis delectus dolores molestiae repellat! Et.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Важно блок -->
    <div class="mt-8 text-left">
        <p class="text-sm text-gray-600">
            <span class="font-bold">*Важно</span><br>
            Ваши результаты уникальны и не подлежат сравнению с другими. Они отражают ваши сильные стороны и
            помогают раскрыть ваш путь к успеху.
        </p>
    </div>

</div>

<footer class="bg-white fixed bottom-0 left-0 right-0">
    <div class="flex justify-between items-center px-4 py-2">
        <p class="text-xs text-gray-600">© 2025 Profgid</p>
        <p class="text-xs text-gray-600">Все права защищены</p>
    </div>
</footer>
</body>
</html>
