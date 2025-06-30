<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Полный отчет по сферам</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>

    </style>
</head>
<body>
<header class="bg-white fixed top-0 left-0 right-0">
    <div class="flex w-full justify-between items-center pb-2">

        <h1>PROFGID</h1>

        <h1><b>{{ auth()->user()->name }}</b> | 12324</h1>

    </div>

    <hr>
</header>

<div class="mt-20">
<div id="spheres-section">
    <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Рекомендации по сферам деятельности</h2>
    <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
        Сферы деятельности, которые лучше всего подходят вашим талантам и где вы сможете реализовать себя
        наиболее эффективно.
    </p>

    @php
        $isFullPlan = $isFullPlan ?? true; // Default to true for PDF view
        $topTenSpheres = collect($topSpheres)->take(10)->toArray();
        $remainingSpheres = collect($topSpheres)->skip(10)->toArray();
    @endphp

    <div class="grid grid-cols-2 gap-6 sphere-grid">
        <!-- Левая колонка - Топ 10 сфер -->
        <div class="sphere-column">
            <div class="space-y-1">
                @php
                    // Используем синий цвет для всех сфер
                    $topTenSpheresIndex = 0;
                @endphp
                @foreach ($topTenSpheres as $index => $sphere)
                    @php
                        // Используем синий цвет для всех сфер
                        $sphereColor = '#316EC6';
                    @endphp

                    <div class="bg-blue-50 border-l-2 p-2 md:p-3 hover:bg-gray-100 transition-colors opacity-80 lg:h-11 sphere-item"
                        style="border-left-color: {{ $sphereColor }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 md:space-x-3">
                                    <span class="text-xs md:text-sm font-medium text-gray-600"
                                        style="color: {{ $sphereColor }}">{{ $topTenSpheresIndex = $topTenSpheresIndex + 1 }}</span>
                                    <h4 class="text-xs font-medium text-gray-900 break-words leading-snug sphere-name">
                                        {{ $sphere['name'] }}</h4>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                @if ($isFullPlan)
                                    <!-- Progress Bar для полного тарифа - скрыт на мобильных -->
                                    <div class="flex flex-1 min-w-[80px]">
                                        <div class="w-full bg-gray-200 rounded-full h-1">
                                            <div class="h-1 rounded-full transition-all duration-500"
                                                style="width: {{ round($sphere['compatibility_percentage']) }}%; background-color: {{ $sphereColor }}">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs px-1 md:px-2 py-0.5 bg-gray-200 text-gray-600">
                                        {{ round($sphere['compatibility_percentage']) }}%
                                    </span>
                                @endif
                                <div class="text-gray-400 p-1">
                                    <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sphere-column">
            <div class="space-y-1">
                @php
                    $remainingSpheresIndex = 10;
                @endphp
                @foreach ($remainingSpheres as $index => $sphere)
                    @php
                        $sphereColor = '#316EC6';
                    @endphp

                    <div class="bg-gray-50 border-l-2 p-2 md:p-3 hover:bg-gray-100 transition-colors opacity-80 lg:h-11 sphere-item"
                        style="border-left-color: {{ $sphereColor }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 md:space-x-3">
                                    <span class="text-xs md:text-sm font-medium text-gray-600"
                                        style="color: {{ $sphereColor }}">{{ $remainingSpheresIndex = $remainingSpheresIndex + 1 }}</span>
                                    <h4 class="text-xs font-medium text-gray-900 break-words leading-snug sphere-name">
                                        {{ $sphere['name'] }}</h4>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if ($isFullPlan)
                                    <!-- Progress Bar для полного тарифа -->
                                    <div class="flex flex-1 min-w-[80px]">
                                        <div class="w-full bg-gray-200 rounded-full h-1">
                                            <div class="h-1 rounded-full transition-all duration-500"
                                                style="width: {{ round($sphere['compatibility_percentage']) }}%; background-color: {{ $sphereColor }}">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs px-1 md:px-2 py-0.5 bg-gray-200 text-gray-600">
                                        {{ round($sphere['compatibility_percentage']) }}%
                                    </span>
                                @endif
                                <div class="text-gray-400 p-1">
                                    <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="page-break-before: always;"></div>

    @if ($isFullPlan)
        <!-- Профессии из ваших топ-10 сфер блок -->
        <div class="mt-16 space-y-4 mt-20">
            <h2 class="text-lg font-bold text-center section-header">Профессии из ваших топ-10 сфер</h2>
            <p class="text-xs text-gray-600 mb-4 text-center leading-relaxed">
                Этот раздел включает профессии, связанные с вашими топ-10 сферами деятельности.
            </p>

            @php
                // Получаем топ-10 сфер пользователя и сразу ограничиваем до 10 элементов
                $topTenSpheresForTable = collect($topTenSpheres)->take(10);

                // Создаем индексированный массив профессий для быстрого поиска
                $professionsIndexedBySphereId = collect($topProfessions)
                    ->filter(function ($profession) {
                        return isset($profession['sphere_id']);
                    })
                    ->groupBy('sphere_id')
                    ->toArray();

                // Группируем профессии по сферам более эффективно
                $professionsGroupedBySphere = $topTenSpheresForTable->map(function ($sphere) use ($professionsIndexedBySphereId) {
                    return [
                        'sphere' => $sphere,
                        'professions' => $professionsIndexedBySphereId[$sphere['id']] ?? [],
                    ];
                })->toArray();
            @endphp

            <!-- Responsive Accordion Layout -->
            <div class="bg-white rounded-lg overflow-hidden table-section">
                <!-- Desktop Table View -->
                <div class="block">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($professionsGroupedBySphere as $index => $sphereData)
                                @php
                                    $sphere = $sphereData['sphere'];
                                    $professions = $sphereData['professions'];
                                    $hasProfessions = !empty($professions);
                                    $sphereId = $sphere['id'];
                                    $compatibilityPercentage = round($sphere['compatibility_percentage']);
                                @endphp

                                <!-- Основная строка сферы -->
                                <tr class="hover:bg-gray-50 transition-colors sphere-row">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1 min-w-0 cursor-pointer">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-medium text-blue-600"
                                                        style="color: #316EC6">{{ $index + 1 }}</span>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $sphere['name'] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Аккордеон стрелка -->
                                                @if ($hasProfessions)
                                                    <div class="text-gray-400 transition-transform duration-200 mr-2">
                                                        <svg class="w-3 h-3" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="1.5"
                                                                d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Percentage Display -->
                                            <div class="flex items-center space-x-2">
                                                <!-- Progress Bar -->
                                                <div class="flex flex-1 min-w-[80px]">
                                                    <div class="w-full bg-gray-200 rounded-full h-1">
                                                        <div class="h-1 rounded-full transition-all duration-500"
                                                            style="width: {{ $compatibilityPercentage }}%; background-color: #316EC6">
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded">
                                                    {{ $compatibilityPercentage }}%
                                                </span>

                                                <!-- Info Button -->
                                                <div class="p-1 text-gray-400 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Раскрывающийся список профессий (Desktop) -->
                                <tr class="sphere-row">
                                    <td class="px-4 py-0 border-t border-gray-100 bg-gray-50">
                                        @if ($hasProfessions)
                                            <div class="py-2">
                                                <div class="grid grid-cols-1 gap-1">
                                                    @foreach ($professions as $profession)
                                                        @php
                                                            $professionCompatibility = isset($profession['compatibility_percentage'])
                                                                ? round($profession['compatibility_percentage'])
                                                                : 0;
                                                            $hasDescription = isset($profession['description']) && $profession['description'];
                                                        @endphp
                                                        <div class="bg-white border border-gray-200 rounded px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center flex-1 space-x-2">
                                                                    <span class="flex-1">{{ $profession['name'] }}</span>
                                                                    @if(isset($profession['compatibility_percentage']))
                                                                        <div class="flex items-center space-x-2">
                                                                            <!-- Mini progress bar -->
                                                                            <div class="w-16 bg-gray-200 rounded-full h-1">
                                                                                <div class="h-1 rounded-full transition-all duration-500 bg-blue-500"
                                                                                    style="width: {{ $professionCompatibility }}%">
                                                                                </div>
                                                                            </div>
                                                                            <span class="text-xs text-gray-500 min-w-[30px]">
                                                                                {{ $professionCompatibility }}%
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if ($hasDescription)
                                                                    <div class="ml-2 p-1 text-gray-400 flex-shrink-0">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="py-4 text-center">
                                                <p class="text-xs text-gray-500">Профессии для этой сферы пока не добавлены</p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="hidden space-y-1">
                    @foreach ($professionsGroupedBySphere as $index => $sphereData)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mobile-sphere-card">
                            <!-- Заголовок сферы для мобильной версии -->
                            <div class="p-4 cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm font-medium text-blue-600"
                                                style="color: #316EC6">{{ $index + 1 }}</span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $sphereData['sphere']['name'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        <!-- Percentage -->
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded">
                                            {{ round($sphereData['sphere']['compatibility_percentage']) }}%
                                        </span>

                                        <!-- Info Button -->
                                        <div class="p-1 text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>

                                        <!-- Accordion Arrow -->
                                        @if (count($sphereData['professions']) > 0)
                                            <div class="text-gray-400 transition-transform duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Профессии для мобильной версии -->
                            <div class="px-4 pb-4 bg-gray-50">
                                @if (count($sphereData['professions']) > 0)
                                    <div class="space-y-2">
                                        @foreach ($sphereData['professions'] as $profession)
                                            <div class="bg-white border border-gray-200 rounded-lg px-3 py-1 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <h5 class="text-sm font-medium text-gray-900 mb-1">
                                                            {{ $profession['name'] }}
                                                        </h5>
                                                    </div>
                                                    @if($isFullPlan && isset($profession['compatibility_percentage']))
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-500">
                                                                {{ round($profession['compatibility_percentage']) }}%
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if (isset($profession['description']) && $profession['description'])
                                                        <div class="ml-2 p-1 text-gray-400 flex-shrink-0">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-6 text-center">
                                        <p class="text-sm text-gray-500">Профессии для этой сферы пока не добавлены</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (empty($professionsGroupedBySphere))
                    <div class="text-center py-8 md:py-12 px-4">
                        <div class="text-gray-400 mb-3">
                            <svg class="w-12 h-12 md:w-16 md:h-16 mx-auto" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7" />
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-medium text-gray-900 mb-2">Нет доступных сфер</h3>
                        <p class="text-sm md:text-base text-gray-500">Сферы профессий пока не созданы.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
</div>

<footer class="bg-white mt-8 pt-4 border-t">
    <div class="flex justify-between items-center px-4 py-2">
        <p class="text-xs text-gray-600">© 2025 Profgid</p>
        <p class="text-xs text-gray-600">Все права защищены</p>
    </div>
</footer>
</body>
</html>
