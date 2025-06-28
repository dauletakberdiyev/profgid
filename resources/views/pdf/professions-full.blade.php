<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Полный отчет по профессиям</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<div class="max-w-7xl mx-auto bg-white rounded-xl p-4 md:p-8 my-4 md:my-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Ваши профессии — подробный отчет</h1>
    <p class="text-sm text-gray-600 mb-6">Дата теста: <b>{{ $testDate }}</b></p>

    <!-- Top Professions -->
    <div class="bg-white rounded-xl p-4 mb-6">
        <h2 class="text-lg font-bold mb-4">Топ профессии</h2>
        
        <div class="space-y-6">
            @foreach($topProfessions as $profession)
                <div class="border rounded-lg overflow-hidden {{ $profession['is_top'] ? 'border-blue-600 border-2' : '' }}">
                    <div class="flex items-center justify-between p-3 bg-gray-50 border-b">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $profession['name'] }}</h3>
                            <p class="text-sm text-blue-600">{{ $profession['sphere_name'] }}</p>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 font-bold rounded-full px-4 py-2">
                                {{ $profession['compatibility_percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <p class="text-gray-600">{{ $profession['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-yellow-50 border-l-4 border-orange-600 p-4 mt-8 text-sm">
        <b>*Важно:</b> Ваши результаты уникальны и не подлежат сравнению с другими. Они отражают ваши сильные стороны и помогают раскрыть ваш путь к успеху.
    </div>
</div>
</body>
</html>
