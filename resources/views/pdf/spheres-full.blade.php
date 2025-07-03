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
        <div class="space-y-4 mt-20">
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
                <div class="block">
                    <div class="w-full">
                        <div class="grid grid-cols-3 gap-5">
                            @foreach (array_slice($professionsGroupedBySphere, 0, 6) as $index => $sphereData)
                                @php
                                    $sphere = $sphereData['sphere'];
                                    $professions = $sphereData['professions'];
                                    $hasProfessions = !empty($professions);
                                    $sphereId = $sphere['id'];
                                    $compatibilityPercentage = round($sphere['compatibility_percentage']);
                                @endphp

                                <div>
                                    <!-- Основная строка сферы -->
                                    <div>
                                        <div class="py-2 px-2 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center flex-1 min-w-0 cursor-pointer">
                                                    <div class="flex items-center space-x-3">
                                                        <span class="text-sm font-medium text-blue-600"
                                                            style="color: #316EC6">{{ $loop->index + 1 }}</span>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $sphere['name'] }}
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Раскрывающийся список профессий (Desktop) -->
                                    <div>
                                        <div class="px-2 py-0">
                                            @if ($hasProfessions)
                                                <div class="grid grid-cols-1 gap-1">
                                                    @foreach ($professions as $profession)
                                                        @php
                                                            $professionCompatibility = isset($profession['compatibility_percentage'])
                                                                ? round($profession['compatibility_percentage'])
                                                                : 0;
                                                            $hasDescription = isset($profession['description']) && $profession['description'];
                                                        @endphp
                                                        <div class="bg-white pb-1 text-sm text-gray-700 transition-colors border-b border-gray-200">
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
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div style="page-break-before: always;"></div>

                        <div class="grid grid-cols-3 gap-5 mt-20">
                            @foreach (array_slice($professionsGroupedBySphere, 6, 12) as $index => $sphereData)
                                @php
                                    $sphere = $sphereData['sphere'];
                                    $professions = $sphereData['professions'];
                                    $hasProfessions = !empty($professions);
                                    $sphereId = $sphere['id'];
                                    $compatibilityPercentage = round($sphere['compatibility_percentage']);
                                @endphp

                                <div>
                                    <!-- Основная строка сферы -->
                                    <div>
                                        <div class="py-2 px-2 bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center flex-1 min-w-0 cursor-pointer">
                                                    <div class="flex items-center space-x-3">
                                                        <span class="text-sm font-medium text-blue-600"
                                                            style="color: #316EC6">{{ $loop->index + 1 }}</span>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $sphere['name'] }}
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Раскрывающийся список профессий (Desktop) -->
                                    <div>
                                        <div class="px-2 py-0">
                                            @if ($hasProfessions)
                                                <div class="grid grid-cols-1 gap-1">
                                                    @foreach ($professions as $profession)
                                                        @php
                                                            $professionCompatibility = isset($profession['compatibility_percentage'])
                                                                ? round($profession['compatibility_percentage'])
                                                                : 0;
                                                            $hasDescription = isset($profession['description']) && $profession['description'];
                                                        @endphp
                                                        <div class="bg-white pb-1 text-sm text-gray-700 transition-colors border-b border-gray-200">
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
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
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
