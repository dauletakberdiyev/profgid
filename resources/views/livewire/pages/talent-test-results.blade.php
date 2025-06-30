<!-- HTML2Canvas CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div class="min-h-screen bg-gray-50 py-4 md:py-8 px-4" x-data="{
    activeTab: 'talents',
    canViewSpheresTab: {{ $this->canViewSpheresTab ? 'true' : 'false' }},
    canViewProfessionsTab: {{ $this->canViewProfessionsTab ? 'true' : 'false' }},
    modalSphere: null,
    modalProfession: null,
    expandedSpheres: [],
    isExporting: false,

    setActiveTab(tab) {
        // Проверяем права доступа к вкладкам
        if (tab === 'spheres' && !this.canViewSpheresTab) {
            return; // Не переключаем вкладку если нет доступ��
        }
        if (tab === 'professions' && !this.canViewProfessionsTab) {
            return; // Не переключаем вкладку если нет доступа
        }
        this.activeTab = tab;
    },

    toggleSphere(sphereId) {
        if (this.expandedSpheres.includes(sphereId)) {
            this.expandedSpheres = this.expandedSpheres.filter(id => id !== sphereId);
        } else {
            this.expandedSpheres.push(sphereId);
        }
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
    },

    async exportSection(sectionId, filename = 'talent-results') {
        this.isExporting = true;

        // Проверяем готовность библиотек
        if (!this.checkLibraryReadiness()) {
            this.isExporting = false;
            return;
        }

        // Логируем диагностическую информацию
        console.log('Export diagnostic info:', this.getDiagnosticInfo());

        try {
            const element = document.getElementById(sectionId);
            if (!element) {
                this.showNotification('Секция для экспорта не найдена', 'error');
                return;
            }

            // Показываем уведомление о начале экспорта
            this.showNotification('Подготовка к экспорту...', 'info');

            // Ждем следующий кадр для плавности UI
            await new Promise(resolve => requestAnimationFrame(resolve));

            // Добавляем индикатор прогресса
            let progressNotification = null;
            const showProgress = (message) => {
                if (progressNotification) {
                    progressNotification.textContent = message;
                } else {
                    progressNotification = document.createElement('div');
                    progressNotification.className = 'fixed top-16 right-4 z-50 px-4 py-2 rounded-lg shadow-lg bg-blue-500 text-white';
                    progressNotification.textContent = message;
                    document.body.appendChild(progressNotification);
                }
            };

            let dataUrl;

            // Проверяем доступность библиотек
            const hasDomToImage = typeof domtoimage !== 'undefined';
            const hasHtml2Canvas = typeof html2canvas !== 'undefined';

            if (!hasDomToImage && !hasHtml2Canvas) {
                throw new Error('Библиотеки для экспорта не загружены');
            }

            // Сначала пробуем быстрый метод с dom-to-image
            if (hasDomToImage) {
                try {
                    showProgress('Экспорт (быстрый метод)...');
                    dataUrl = await Promise.race([
                        domtoimage.toJpeg(element, {
                            quality: 0.85,
                            bgcolor: '#ffffff',
                            style: {
                                transform: 'none',
                                transition: 'none',
                                animation: 'none'
                            }
                        }),
                        new Promise((_, reject) =>
                            setTimeout(() => reject(new Error('dom-to-image timeout')), 8000)
                        )
                    ]);
                } catch (domError) {
                    console.warn('dom-to-image failed:', domError);
                    if (!hasHtml2Canvas) {
                        throw domError;
                    }
                    // Продолжаем к html2canvas
                }
            }

            // Fallback к html2canvas если dom-to-image не сработал или недоступен
            if (!dataUrl && hasHtml2Canvas) {
                showProgress('Экспорт через HTML2Canvas...');

                // Подготавливаем элемент для лучшего рендеринга
                const originalStyle = {
                    position: element.style.position,
                    left: element.style.left,
                    top: element.style.top
                };

                element.style.position = 'relative';
                element.style.left = '0';
                element.style.top = '0';

                try {
                    const canvas = await Promise.race([
                        html2canvas(element, {
                            scale: 1.2,
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: '#ffffff',
                            logging: false,
                            letterRendering: true,
                            removeContainer: true,
                            imageTimeout: 5000,
                            width: element.scrollWidth,
                            height: element.scrollHeight,
                            scrollX: 0,
                            scrollY: 0,
                            windowWidth: window.innerWidth,
                            windowHeight: window.innerHeight,
                            onclone: function(clonedDoc) {
                                const clonedElement = clonedDoc.getElementById(sectionId);
                                if (clonedElement) {
                                    // Оптимизируем клонированный элемент
                                    clonedElement.style.transform = 'none';
                                    clonedElement.style.transition = 'none';
                                    clonedElement.style.animation = 'none';
                                    clonedElement.style.position = 'relative';

                                    // Улучшаем видимость текста
                                    const textElements = clonedElement.querySelectorAll('*');
                                    textElements.forEach(el => {
                                        if (el.style) {
                                            el.style.textShadow = 'none';
                                            el.style.webkitFontSmoothing = 'antialiased';
                                        }
                                    });
                                }
                            }
                        }),
                        new Promise((_, reject) =>
                            setTimeout(() => reject(new Error('HTML2Canvas timeout')), 15000)
                        )
                    ]);

                    // Восстанавливаем исходные стили
                    Object.assign(element.style, originalStyle);

                    dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                } catch (canvasError) {
                    // Восстанавливаем исходные стили в случае ошибки
                    Object.assign(element.style, originalStyle);
                    throw canvasError;
                }
            }

            if (!dataUrl) {
                throw new Error('Не удалось создать изображение');
            }

            showProgress('Подготовка к скачиванию...');

            // Создаем ссылку для скачивания
            const link = document.createElement('a');
            link.download = `${filename}-${new Date().toISOString().split('T')[0]}.jpg`;
            link.href = dataUrl;

            // Скачиваем файл
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            this.showNotification('Изображение успешно экспортировано!', 'success');

        } catch (error) {
            console.error('Ошибка при экспорте:', error);

            let errorMessage = 'Произошла ошибка при экспорте изображения';

            if (error.message.includes('timeout')) {
                errorMessage = 'Экспорт занял слишком много времени. Попробуйте снова.';
            } else if (error.message.includes('библиотеки')) {
                errorMessage = 'Библиотеки для экспорта не загружены. Обновите страницу.';
            } else if (error.message.includes('не найдена')) {
                errorMessage = 'Секция для экспорта не найдена';
            } else if (error.message.includes('создать изображение')) {
                errorMessage = 'Не удалось создать изображение. Попробуйте другой браузер.';
            }

            this.showNotification(errorMessage, 'error');
        } finally {
            // Очищаем индикатор прогресса
            if (progressNotification) {
                document.body.removeChild(progressNotification);
                progressNotification = null;
            }
            this.isExporting = false;
        }
    },

    // Проверка готовности библиотек
    checkLibraryReadiness() {
        const libraries = {
            'dom-to-image': typeof domtoimage !== 'undefined',
            'html2canvas': typeof html2canvas !== 'undefined',
            'html2pdf': typeof html2pdf !== 'undefined'
        };

        console.log('Library readiness check:', libraries);

        if (!libraries['dom-to-image'] && !libraries['html2canvas']) {
            this.showNotification('Библиотеки экспорта не загружены', 'error');
            return false;
        }

        return true;
    },

    // Диагностическая информация
    getDiagnosticInfo() {
        return {
            userAgent: navigator.userAgent,
            screenSize: `${screen.width}x${screen.height}`,
            viewportSize: `${window.innerWidth}x${window.innerHeight}`,
            devicePixelRatio: window.devicePixelRatio,
            libraries: {
                domtoimage: typeof domtoimage !== 'undefined',
                html2canvas: typeof html2canvas !== 'undefined',
                html2pdf: typeof html2pdf !== 'undefined'
            },
            performance: {
                memory: navigator.memory ? {
                    used: Math.round(navigator.memory.usedJSHeapSize / 1024 / 1024) + 'MB',
                    total: Math.round(navigator.memory.totalJSHeapSize / 1024 / 1024) + 'MB',
                    limit: Math.round(navigator.memory.jsHeapSizeLimit / 1024 / 1024) + 'MB'
                } : 'Not available'
            }
        };
    },

    showNotification(message, type = 'info') {
        // Создаем уведомление
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Удаляем уведомление через 3 секунды
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}">
    <div class="max-w-7xl mx-auto bg-white rounded-xl p-4 md:p-8 my-4 md:my-8" id="main-results-container">
        @if (count($userResults) > 0)
            <!-- Tabs Navigation -->
            <div class="mb-6 md:mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <!-- Вкладка "Таланты" - всегда доступна -->
                        <button @click="setActiveTab('talents')"
                            :class="activeTab === 'talents' ? 'border-blue-600 text-blue-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            Топ Таланты
                        </button>

                        <!-- Вкладка "Сферы" - доступна для средний и премиум тарифов -->
                        @if ($this->canViewSpheresTab)
                            <button @click="setActiveTab('spheres')"
                                :class="activeTab === 'spheres' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                                Топ Сферы
                            </button>
                        @endif

                        <!-- Вкладка "Профессии" - доступна только для премиум тарифа -->
                        @if ($this->canViewProfessionsTab)
                            <button @click="setActiveTab('professions')"
                                :class="activeTab === 'professions' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
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

                <!-- Export and PDF Download Buttons -->
                <div class="flex items-center space-x-2">
                    <!-- Image Export Button -->
                    <!-- <button
                        @click="exportSection()"
                        class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-green-100 hover:bg-green-200 disabled:bg-gray-100 disabled:cursor-not-allowed rounded-lg transition-colors group self-start"
                        title="Экспорт в изображение"
                    >
                        <svg x-show="!isExporting" class="w-5 h-5 md:w-6 md:h-6 text-green-600 group-hover:text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <svg x-show="isExporting" class="w-5 h-5 md:w-6 md:h-6 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    <a href="{{ route('talent.pdf.download', ['session_id' => "cac85329-bd7a-40a5-95f6-894fdd660bdc"]) }}">
                        Export pdf
                    </a> -->

                    <!-- PDF Download Button -->
                    <a
                        :href="
                            activeTab === 'talents'
                                ? '{{ route('talent.pdf.download', ['session_id' => $testSessionId ?? request()->get('session_id'), 'tab' => 'talents']) }}'
                            : activeTab === 'spheres'
                                ? '{{ route('talent.pdf.download', ['session_id' => $testSessionId ?? request()->get('session_id'), 'tab' => 'spheres']) }}'
                            : '{{ route('talent.pdf.download', ['session_id' => $testSessionId ?? request()->get('session_id'), 'tab' => 'professions']) }}'
                        "
                        class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors group self-start"
                        title="Скачать результаты в PDF"
                        target="_blank"
                    >
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-600 group-hover:text-gray-800" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Tab Content -->
            <div id="talents-section" x-show="activeTab === 'talents'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <!-- Domain Bar Chart -->
                <div class="mb-4 md:mb-6" id="talents-section-pdf">
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

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-1 mb-6 md:mb-8">
                    @foreach ($domains as $domain => $name)
                        <div class="mb-4 md:mb-0">
                            <div class="text-center font-bold uppercase text-xs md:text-sm mb-2 md:mb-3 pb-1 md:pb-2 text-gray-800 p-1 md:p-2 border-b-4 md:border-b-8"
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

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                    <div class="border-gray-200 p-3 transition-all hover:bg-blue-100 bg-blue-50"
                                        style="border-left: 4px solid {{ $talentDomainColor }}">
                                        <!-- Заголовок таланта -->
                                        <div class="flex items-center justify-between cursor-pointer"
                                            @click="expandedTalents.includes('{{ $talentId }}') ? expandedTalents.splice(expandedTalents.indexOf('{{ $talentId }}'), 1) : expandedTalents.push('{{ $talentId }}')">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                                    style="background-color: {{ $talentDomainColor }}">
                                                    {{ $talent['rank'] }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-sm font-bold text-gray-900 truncate">{{ $talent['name'] }}</h3>
                                                    <span class="text-xs text-gray-500 truncate block">{{ $domains[$talent['domain']] ?? '' }}</span>
                                                </div>
                                            </div>
                                            <!-- Стрелка аккордеона -->
                                            <div class="text-gray-400 transition-transform duration-200"
                                                :class="expandedTalents.includes('{{ $talentId }}') ? 'rotate-180' : ''">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
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
                                                <div class="mb-3">
                                                    <h4 class="text-xs font-semibold text-gray-900 mb-1">Краткое описание</h4>
                                                    <p class="text-xs text-gray-700 leading-tight">{{ $talent['short_description'] }}</p>
                                                </div>
                                            @endif

                                            <!-- Обзор таланта (только для полного тарифа) -->
                                            @if (!empty($talent['description']) && $this->isFullPlan)
                                                <div class="mb-3">
                                                    <h4 class="text-xs font-semibold text-gray-900 mb-1">Обзор</h4>
                                                    <p class="text-xs text-gray-700 leading-tight">{{ $talent['description'] }}</p>
                                                </div>
                                            @endif

                                            <!-- Советы (только для полного тарифа) -->
                                            @if ($this->isFullPlan)
                                                <div class="pt-2 border-t border-gray-100">
                                                    <h4 class="text-xs font-semibold text-gray-900 mb-1">Советы</h4>
                                                    <div class="text-xs text-gray-700 leading-tight">
                                                        {!! $this->getTalentAdvice($talent['name']) !!}
                                                    </div>
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
                                    <div class="bg-gray-50 p-3 transition-all hover:bg-gray-100"
                                        style="border-left: 4px solid {{ $talentDomainColor }}">

                                        <!-- Заголовок таланта с аккордеоном -->
                                        <div class="flex items-center justify-between cursor-pointer"
                                            @click="expandedTalents.includes('{{ $remainingTalentId }}') ? expandedTalents.splice(expandedTalents.indexOf('{{ $remainingTalentId }}'), 1) : expandedTalents.push('{{ $remainingTalentId }}')">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2"
                                                    style="background-color: {{ $talentDomainColor }}">
                                                    {{ $talent['rank'] }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="text-sm font-bold text-gray-900 truncate">{{ $talent['name'] }}</h3>
                                                    <span class="text-xs text-gray-500 truncate block">{{ $domains[$talent['domain']] ?? '' }}</span>
                                                </div>
                                            </div>
                                            <!-- Стрелка аккордеона -->
                                            <div class="text-gray-400 transition-transform duration-200"
                                                :class="expandedTalents.includes('{{ $remainingTalentId }}') ? 'rotate-180' : ''">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
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
                                                <div class="mb-3">
                                                    <h4 class="text-xs font-semibold text-gray-900 mb-1">Краткое описание</h4>
                                                    <p class="text-xs text-gray-700 leading-tight">{{ $talent['short_description'] }}</p>
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

            <div id="spheres-section" x-show="activeTab === 'spheres'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h2 class="text-lg md:text-xl font-bold mb-3 md:mb-4">Рекомендации по сферам деятельности</h2>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed">
                    Сферы деятельности, которые лучше всего подходят вашим талантам и где вы сможете реализовать себя
                    наиболее эффективно.
                </p>

                @php
                    $topTenSpheres = collect($topSpheres)->take(10)->toArray();
                    $remainingSpheres = collect($topSpheres)->skip(10)->toArray();
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Левая колонка - Топ 10 сфер -->
                    <div>
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

                                <div class="bg-blue-50 border-l-2 p-2 md:p-3 hover:bg-gray-100 transition-colors opacity-80 lg:h-11"
                                    style="border-left-color: {{ $sphereColor }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 md:space-x-3">
                                                <span class="text-xs md:text-sm font-medium text-gray-600"
                                                    style="color: {{ $sphereColor }}">{{ $topTenSpheresIndex = $topTenSpheresIndex + 1 }}</span>
                                                <h4 class="text-xs font-medium text-gray-900 break-words leading-snug truncate">
                                                    {{ $sphere['name'] }}</h4>
                                            </div>
                                        </div>


                                        <div class="flex items-center space-x-2">
                                            @if ($this->isFullPlan)
                                                <!-- Progress Bar для полного тарифа - скрыт на мобильных -->
                                                <div class="hidden md:flex flex-1 min-w-[80px]">
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
                                            <button @click="openSphereModal({{ json_encode($sphere) }})"
                                                class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                                title="Показать описание">
                                                <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="space-y-1">
                            @php
                                $remainingSpheresIndex = 10;
                            @endphp
                            @foreach ($remainingSpheres as $index => $sphere)
                                @php
                                    $sphereColor = '#316EC6';
                                @endphp

                                <div class="bg-gray-50 border-l-2 p-2 md:p-3 hover:bg-gray-100 transition-colors opacity-80 lg:h-11"
                                    style="border-left-color: {{ $sphereColor }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 md:space-x-3">
                                                <span class="text-xs md:text-sm font-medium text-gray-600"
                                                    style="color: {{ $sphereColor }}">{{ $remainingSpheresIndex = $remainingSpheresIndex + 1 }}</span>
                                                <h4 class="text-xs font-medium text-gray-900 break-words leading-snug truncate">
                                                    {{ $sphere['name'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if ($this->isFullPlan)
                                                <!-- Progress Bar для полного тарифа - скрыт на мобильных -->
                                                <div class="hidden md:flex flex-1 min-w-[80px]">
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
                                            <button @click="openSphereModal({{ json_encode($sphere) }})"
                                                class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                                title="Показать описание">
                                                <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Modal for Sphere Description -->
                <div x-show="modalSphere" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background-color: rgba(0, 0, 0, 0.5);" @click="closeSphereModal()">
                    <div @click.stop x-transition:enter="transition ease-out duration-300"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed"
                            x-text="modalSphere?.description || 'Описание отсутствует'"></p>
                    </div>
                </div>

                <!-- Modal for Profession Description -->
                <div x-show="modalProfession" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background-color: rgba(0, 0, 0, 0.5);" @click="closeProfessionModal()">
                    <div @click.stop x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="modalProfession?.name"></h3>
                            <button @click="closeProfessionModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed"
                            x-text="modalProfession?.description || 'Описание отсутствует'"></p>
                    </div>
                </div>

                @if ($this->isFullPlan)
                    <!-- Профессии из ваших топ-10 сфер блок -->
                    <div class="mt-16 space-y-4">
                        <h2 class="text-lg font-bold text-center">Профессии из ваших топ-10 сфер</h2>
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
                        <div class="bg-white rounded-lg overflow-hidden">
                            <!-- Desktop Table View -->
                            <div class="hidden md:block">
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
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-2">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center flex-1 min-w-0 cursor-pointer"
                                                            @click="toggleSphere({{ $sphereId }})">
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
                                                                <div class="text-gray-400 transition-transform duration-200 mr-2"
                                                                    :class="expandedSpheres.includes({{ $sphereId }}) ? 'rotate-90' : ''">
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
                                                            <button
                                                                @click.stop="openSphereModal({{ json_encode($sphere) }})"
                                                                class="p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0"
                                                                title="Показать информацию о сфере">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Раскрывающийся список профессий (Desktop) -->
                                            <tr x-show="expandedSpheres.includes({{ $sphereId }})"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 max-h-0"
                                                x-transition:enter-end="opacity-100 max-h-96"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 max-h-96"
                                                x-transition:leave-end="opacity-0 max-h-0" style="display: none;">
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
                                                                                        <div class="w-12 bg-gray-200 rounded-full h-1">
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
                                                                                <button
                                                                                    @click.stop="openProfessionModal({{ json_encode($profession) }})"
                                                                                    class="ml-2 p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0"
                                                                                    title="Показать описание профессии">
                                                                                    <svg class="w-4 h-4" fill="none"
                                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                    </svg>
                                                                                </button>
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
                            <div class="md:hidden space-y-1">
                                @foreach ($professionsGroupedBySphere as $index => $sphereData)
                                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                        <!-- Заголовок сферы для мобильной версии -->
                                        <div class="p-4 cursor-pointer"
                                            @click="toggleSphere({{ $sphereData['sphere']['id'] }})">
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
                                                    <button
                                                        @click.stop="openSphereModal({{ json_encode($sphereData['sphere']) }})"
                                                        class="p-1 text-gray-400 hover:text-blue-500 transition-colors"
                                                        title="Показать информацию о сфере">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>

                                                    <!-- Accordion Arrow -->
                                                    @if (count($sphereData['professions']) > 0)
                                                        <div class="text-gray-400 transition-transform duration-200"
                                                            :class="expandedSpheres.includes({{ $sphereData['sphere']['id']}}) ? 'rotate-90' : ''">
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
                                        <div x-show="expandedSpheres.includes({{ $sphereData['sphere']['id'] }})"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 max-h-0"
                                            x-transition:enter-end="opacity-100 max-h-96"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 max-h-96"
                                            x-transition:leave-end="opacity-0 max-h-0"
                                            class="px-4 pb-4 bg-gray-50"
                                            style="display: none;">
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
                                                                @if($this->isFullPlan && isset($profession['compatibility_percentage']))
                                                                        <div class="flex items-center space-x-2">
                                                                            <span class="text-xs text-gray-500">
                                                                                {{ round($profession['compatibility_percentage']) }}%
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                @if (isset($profession['description']) && $profession['description'])
                                                                    <button
                                                                        @click.stop="openProfessionModal({{ json_encode($profession) }})"
                                                                        class="ml-2 p-1 text-gray-400 hover:text-blue-500 transition-colors flex-shrink-0"
                                                                        title="Показать описание профессии">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </button>
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

            <div id="professions-section" x-show="activeTab === 'professions'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <h2 class="text-lg md:text-xl font-bold mb-4">Все профессии</h2>
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
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Топ рекомендуемые профессии</h3>
                        </div>
                        <!-- Desktop Table View -->
                        <div class="hidden md:block space-y-2">
                            @foreach ($topThirtyProfessions as $index => $profession)
                                <div class="bg-white px-4 py-1 transition-all border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900">{{ $profession['name'] }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-12 bg-gray-200 rounded-full h-1">
                                                <div class="h-1 rounded-full transition-all duration-500 bg-blue-500"
                                                    style="width: {{ round($profession['compatibility_percentage']) }}%"></div>
                                            </div>
                                            <span class="text-xs text-blue-600 min-w-[30px]">{{ round($profession['compatibility_percentage']) }}%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-2">
                            @foreach ($topThirtyProfessions as $index => $profession)
                                <div class="bg-white px-3 py-2 transition-all border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-medium text-gray-900 inline-block">{{ $index + 1 }}</span>
                                        <span class="text-xs font-medium text-gray-900 inline-block leading-none">{{ $profession['name'] }}</span>
                                        </div>
                                        <span class="text-xs text-blue-600 font-semibold">{{ round($profession['compatibility_percentage']) }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Следующие 10 профессий (31-40) - отдельный блок с предупреждением -->
                @if (count($nextTenProfessions) > 0)
                    <div class="mt-12">
                        <div class="flex items-center mb-4">
                            <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-lg font-semibold text-black">Профессии, которые меньше всего соответствуют
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
                                <div class="bg-white border border-orange-200 p-2 rounded opacity-60">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-medium text-orange-600">{{ $professionIndex = $professionIndex + 1 }}</span>
                                                <h4 class="text-xs text-gray-700 truncate">{{ $profession['name'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <!-- Прогресс-бар только для десктопа -->
                                            <div class="hidden lg:block flex-1 min-w-[60px]">
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
                                            <!-- Кнопка информации -->
                                            <button @click="openProfessionModal({{ json_encode($profession) }})"
                                                class="text-orange-400 hover:text-orange-600 transition-colors duration-200 p-1"
                                                title="Показать описание">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Modal for Profession Description -->
                <div x-show="modalProfession" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 flex items-center justify-center z-50 p-4"
                    style="background-color: rgba(0, 0, 0, 0.5);" @click="closeProfessionModal()">
                    <div @click.stop x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900" x-text="modalProfession?.name"></h3>
                                <div class="text-sm text-blue-600 mt-1" x-text="modalProfession?.sphere_name || ''">
                                </div>
                            </div>
                            <button @click="closeProfessionModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed"
                            x-text="modalProfession?.description || 'Описание отсутствует'"></p>
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
