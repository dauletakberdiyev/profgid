<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Полный отчет по сферам</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f9fafb;
            color: #22223b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem 2.5rem 2rem;
        }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-lg { font-size: 1.125rem; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .font-extrabold { font-weight: 800; }
        .text-gray-900 { color: #111827; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-700 { color: #374151; }
        .text-gray-500 { color: #6b7280; }
        .text-white { color: #ffffff; }
        .text-black { color: #000000; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .rounded-lg { border-radius: 0.75rem; }
        .rounded-xl { border-radius: 1.25rem; }
        .rounded-full { border-radius: 9999px; }
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .px-1 { padding-left: 0.25rem; padding-right: 0.25rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .bg-white { background: #fff; }
        .bg-gray-50 { background: #f9fafb; }
        .bg-gray-100 { background: #f3f4f6; }
        .bg-gray-200 { background: #e5e7eb; }
        .bg-gray-400 { background: #9ca3af; }
        .bg-blue-50 { background: #eff6ff; }
        .bg-blue-100 { background: #dbeafe; }
        .border-b-4 { border-bottom: 4px solid; }
        .border-b-8 { border-bottom: 8px solid; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-100 { border-color: #f3f4f6; }
        .border-t { border-top: 1px solid; }
        .border-l-4 { border-left: 4px solid; }
        .uppercase { text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .w-full { width: 100%; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .h-8 { height: 2rem; }
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .gap-1 { gap: 0.25rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-6 { gap: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .space-y-1 > * + * { margin-top: 0.25rem; }
        .space-y-5 > * + * { margin-top: 1.25rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .mt-8 { margin-top: 2rem; }
        .mt-20 { margin-top: 5rem; }
        .mr-2 { margin-right: 0.5rem; }
        .leading-relaxed { line-height: 1.625; }
        .leading-tight { line-height: 1.25; }
        .border { border: 1px solid #e5e7eb; }
        .border-l-4 { border-left: 4px solid; }
        .bg-yellow-50 { background: #fef3c7; }
        .text-indigo-600 { color: #4f46e5; }
        .text-green-600 { color: #16a34a; }
        .text-blue-600 { color: #2563eb; }
        .text-purple-700 { color: #6d28d9; }
        .text-orange-600 { color: #ea580c; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .border-b { border-bottom: 1px solid #e5e7eb; }
        .border-b-4 { border-bottom: 4px solid; }
        .border-b-8 { border-bottom: 8px solid; }
        .pb-1 { padding-bottom: 0.25rem; }
        .pb-2 { padding-bottom: 0.5rem; }
        .pt-2 { padding-top: 0.5rem; }
        .font-semibold { font-weight: 600; }
        .font-normal { font-weight: 400; }
        .aspect-square { aspect-ratio: 1 / 1; }
        .overflow-hidden { overflow: hidden; }
        .min-w-0 { min-width: 0; }
        .flex-1 { flex: 1 1 0%; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .block { display: block; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Ваши сферы и профессии — подробный отчет</h1>
    <p class="text-sm text-gray-600 mb-6">Дата теста: <b>{{ $testDate }}</b></p>

    <!-- Top Spheres -->
    <div class="bg-white rounded-xl p-4 mb-6">
        <h2 class="text-lg font-bold mb-4">Топ сферы</h2>
        
        <div class="space-y-6">
            @foreach($topSpheres as $sphere)
                <div class="border rounded-lg overflow-hidden {{ $sphere['is_top'] ? 'border-blue-600 border-2' : '' }}">
                    <div class="flex items-center justify-between p-3 bg-gray-50 border-b">
                        <h3 class="font-bold text-gray-900">{{ $sphere['name'] }}</h3>
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-600 font-bold rounded-full px-4 py-2">
                                {{ $sphere['compatibility_percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <p class="text-gray-600">{{ $sphere['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

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
