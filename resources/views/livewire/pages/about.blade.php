<div>
    {{-- Hero Section --}}
    <section class="py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                О нас
            </h1>
            <p class="text-xl md:text-2xl text-gray-600 leading-relaxed">
                Помогаем найти профессию, которая подходит именно вам
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4">
            <div class="prose prose-lg mx-auto text-gray-700">
                <p class="text-xl leading-relaxed mb-8">
                    ProfGid — это команда профессионалов, помогающая людям определить свои сильные стороны, найти подходящую профессию и построить успешную карьеру.
                </p>

                <div class="space-y-8">
                    <div>

                        <p class="text-lg leading-relaxed">
                            Мы верим, что у каждого есть уникальные таланты и способности, которые можно раскрыть и направить на достижение целей. Наша миссия — помочь людям лучше понять себя и выбрать путь, который принесёт удовлетворение и успех.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Чем мы занимаемся?</h2>
                        <ul class="list-disc list-inside">
                            <li class="text-lg leading-relaxed">Профориентация: Индивидуальные консультации для школьников, студентов и взрослых.</li>
                            <li class="text-lg leading-relaxed">Анализ талантов: Используем современные методики, такие как тест Gallup CliftonStrengths, MBTI, и другие международно признанные инструменты.</li>
                            <li class="text-lg leading-relaxed">
                                Рекомендации: Подбор профессий и сфер деятельности, соответствующих вашим интересам и сильным сторонам.
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Наши ценности</h2>
                        <ul class="list-disc list-inside">
                            <li class="text-lg leading-relaxed">Индивидуальный подход: Каждый человек уникален, и мы учитываем это в своей работе.</li>
                            <li class="text-lg leading-relaxed">Достоверность: Все наши рекомендации основаны на научных методах и глубоких анализах.</li>
                            <li class="text-lg leading-relaxed">Постоянное развитие: Мы следим за новыми тенденциями и совершенствуем наши услуги.</li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-lg leading-relaxed">
                            С ProfGid вы получаете не просто советы, а чёткий план действий для достижения ваших карьерных целей. Мы — ваш проводник в мире профессий и возможностей!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">
                Есть вопросы?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Свяжитесь с нами, и мы поможем вам разобраться
            </p>

            <div class="space-y-4 text-lg">
                <div>
                    <a href="mailto:zhangeldi.turarbek@gmail.com" class="text-blue-600 hover:text-blue-700">
                        zhangeldi.turarbek@gmail.com
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-blue-50">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">
                Готовы найти свою профессию?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Пройдите тест и получите персональные рекомендации
            </p>
            <a href="{{ route('test-preparation') }}"
               class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition-colors">
                Пройти тест
            </a>
        </div>
    </section>
</div>
