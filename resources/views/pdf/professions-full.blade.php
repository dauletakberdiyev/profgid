<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Полный отчет по профессиям</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        /* PDF page break optimization */
        .profession-item {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 4px;
        }
        .section-header {
            page-break-after: avoid;
            break-after: avoid;
        }
        .profession-section {
            page-break-before: auto;
            break-before: auto;
            margin-top: 20px;
        }
        .profession-name {
            white-space: normal !important;
            word-wrap: break-word;
            line-height: 1.3;
        }
        .top-professions-section {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .warning-section {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        @media print {
            .hidden {
                display: block !important;
            }
            .truncate {
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

<header class="bg-white fixed top-0 left-0 right-0">
    <div class="flex w-full justify-between items-center pb-2">

        <h1>PROFGID</h1>

        <h1><b>{{ auth()->user()->name }}</b> | 12324</h1>

    </div>

    <hr>
</header>

    <div class="container mx-auto p-4 mt-20">
        <div id="professions-section">
            <h2 class="text-lg md:text-xl font-bold mb-4 section-header">Все профессии</h2>
            <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                На основе ваших топ талантов мы подобрали наиболее подходящие профессии.
            </p>

            @php
                $topThirtyProfessions = collect($topProfessions)->take(30)->toArray();
                $nextTenProfessions = collect($topProfessions)->skip(30)->take(10)->toArray();

                // Фиолетовый цвет для профессий
                $professionColor = '#8B5CF6';
            @endphp

            <!-- Топ 30 профессий на полную ширину -->
            @if (count($topThirtyProfessions) > 0)
                <div class="mb-8 top-professions-section">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 section-header">Топ рекомендуемые профессии</h3>
                    </div>
                    <!-- Desktop Table View -->
                    <div class="block space-y-2">
                        @foreach ($topThirtyProfessions as $index => $profession)
                            <div class="bg-white px-4 py-1 transition-all border border-gray-200 rounded-lg profession-item">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 profession-name">{{ $profession['name'] }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-1">
                                            <div class="h-1 rounded-full transition-all duration-500 bg-blue-500"
                                                style="width: {{ round($profession['compatibility_percentage']) }}%"></div>
                                        </div>
                                        <span class="text-xs text-blue-600 min-w-[30px]">{{ round($profession['compatibility_percentage']) }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="page-break-before: always;"></div>

            <!-- Следующие 10 профессий (31-40) - отдельный блок с предупреждением -->
            @if (count($nextTenProfessions) > 0)
                <div class="mt-12 warning-section">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-lg font-semibold text-black section-header">Профессии, которые меньше всего соответствуют
                            вашим талантам и могут не раскрыть ваш потенциал</h3>
                    </div>
                    <p class="text-sm text-black mb-4">
                        Эти профессии показали низкую совместимость с вашими талантами. Работа в этих сферах может
                        потребовать от вас больше усилий и не принести полного удовлетворения.
                    </p>
                    <div class="space-y-2">

                        @php
                            // Устанавливаем индекс для нумерации профессий
                            $professionIndex = 0; // Начинаем с 31, так как первые 30 уже отображены
                        @endphp

                        @foreach ($nextTenProfessions as $index => $profession)
                            <div class="bg-white border border-orange-200 p-2 rounded opacity-60 profession-item">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium text-orange-600">{{ $professionIndex = $professionIndex + 1 }}</span>
                                            <h4 class="text-xs text-gray-700 profession-name">{{ $profession['name'] }}</h4>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <!-- Прогресс-бар -->
                                        <div class="flex flex-1 min-w-[60px]">
                                            <div class="w-full bg-gray-200 rounded-full h-1">
                                                <div class="h-1 rounded-full transition-all duration-500 bg-orange-400"
                                                    style="width: {{ round($profession['compatibility_percentage']) }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Процент совместимости -->
                                        <span class="text-xs px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded">
                                            {{ round($profession['compatibility_percentage']) }}%
                                        </span>
                                        <!-- Иконка информации -->
                                        <div class="text-orange-400 p-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <footer class="bg-white mt-8 pt-4 border-t fixed bottom-0 left-0 right-0">
        <div class="flex justify-between items-center px-4 py-2">
            <p class="text-xs text-gray-600">© 2025 Profgid</p>
            <p class="text-xs text-gray-600">Все права защищены</p>
        </div>
    </footer>

</body>
</html>
