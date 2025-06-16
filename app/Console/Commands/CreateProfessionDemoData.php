<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profession;
use App\Models\Talent;

class CreateProfessionDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-professions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create demo profession data with talent coefficients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating profession demo data...');

        // Получаем все таланты
        $talents = Talent::all();
        
        if ($talents->isEmpty()) {
            $this->error('No talents found! Please create talents first.');
            return 1;
        }

        // Создаем профессии с описаниями
        $professions = [
            [
                'name' => 'Менеджер проектов',
                'description' => 'Специалист по управлению проектами, координации команд и достижению целей в установленные сроки.',
                'talents' => [
                    'Achiever' => 0.95,
                    'Arranger' => 0.90,
                    'Focus' => 0.88,
                    'Responsibility' => 0.85,
                    'Deliberative' => 0.80,
                    'Command' => 0.75,
                    'Activator' => 0.72,
                    'Maximizer' => 0.70
                ]
            ],
            [
                'name' => 'Разработчик ПО',
                'description' => 'Программист, создающий и поддерживающий программное обеспечение, решающий технические задачи.',
                'talents' => [
                    'Analytical' => 0.95,
                    'Focus' => 0.90,
                    'Input' => 0.88,
                    'Intellection' => 0.85,
                    'Learner' => 0.82,
                    'Deliberative' => 0.78,
                    'Discipline' => 0.75,
                    'Restorative' => 0.70
                ]
            ],
            [
                'name' => 'Менеджер по продажам',
                'description' => 'Специалист по продвижению товаров и услуг, работе с клиентами и увеличению продаж.',
                'talents' => [
                    'Woo' => 0.95,
                    'Positivity' => 0.90,
                    'Communication' => 0.88,
                    'Activator' => 0.85,
                    'Competition' => 0.82,
                    'Self-Assurance' => 0.78,
                    'Maximizer' => 0.75,
                    'Relator' => 0.70
                ]
            ],
            [
                'name' => 'HR-специалист',
                'description' => 'Эксперт по работе с персоналом, подбору кадров и развитию человеческих ресурсов.',
                'talents' => [
                    'Empathy' => 0.95,
                    'Developer' => 0.90,
                    'Individualization' => 0.88,
                    'Relator' => 0.85,
                    'Harmony' => 0.82,
                    'Communication' => 0.78,
                    'Positivity' => 0.75,
                    'Includer' => 0.70
                ]
            ],
            [
                'name' => 'Маркетолог',
                'description' => 'Специалист по продвижению брендов, анализу рынка и созданию маркетинговых стратегий.',
                'talents' => [
                    'Strategic' => 0.95,
                    'Ideation' => 0.90,
                    'Input' => 0.88,
                    'Communication' => 0.85,
                    'Maximizer' => 0.82,
                    'Futuristic' => 0.78,
                    'Analytical' => 0.75,
                    'Adaptability' => 0.70
                ]
            ],
            [
                'name' => 'Финансовый аналитик',
                'description' => 'Эксперт по анализу финансовых данных, оценке рисков и инвестиционным решениям.',
                'talents' => [
                    'Analytical' => 0.95,
                    'Deliberative' => 0.90,
                    'Discipline' => 0.88,
                    'Focus' => 0.85,
                    'Consistency' => 0.82,
                    'Input' => 0.78,
                    'Intellection' => 0.75,
                    'Context' => 0.70
                ]
            ],
            [
                'name' => 'Дизайнер UX/UI',
                'description' => 'Создатель пользовательских интерфейсов и опыта взаимодействия с цифровыми продуктами.',
                'talents' => [
                    'Ideation' => 0.95,
                    'Maximizer' => 0.90,
                    'Individualization' => 0.88,
                    'Input' => 0.85,
                    'Adaptability' => 0.82,
                    'Empathy' => 0.78,
                    'Strategic' => 0.75,
                    'Learner' => 0.70
                ]
            ],
            [
                'name' => 'Консультант',
                'description' => 'Эксперт, предоставляющий профессиональные советы и решения для бизнес-задач клиентов.',
                'talents' => [
                    'Strategic' => 0.95,
                    'Intellection' => 0.90,
                    'Maximizer' => 0.88,
                    'Individualization' => 0.85,
                    'Communication' => 0.82,
                    'Analytical' => 0.78,
                    'Input' => 0.75,
                    'Self-Assurance' => 0.70
                ]
            ],
            [
                'name' => 'Руководитель команды',
                'description' => 'Лидер, управляющий командой и обеспечивающий эффективную работу коллектива.',
                'talents' => [
                    'Command' => 0.95,
                    'Maximizer' => 0.90,
                    'Activator' => 0.88,
                    'Developer' => 0.85,
                    'Arranger' => 0.82,
                    'Self-Assurance' => 0.78,
                    'Communication' => 0.75,
                    'Responsibility' => 0.70
                ]
            ],
            [
                'name' => 'Аналитик данных',
                'description' => 'Специалист по сбору, обработке и интерпретации больших объемов данных.',
                'talents' => [
                    'Analytical' => 0.95,
                    'Input' => 0.90,
                    'Intellection' => 0.88,
                    'Focus' => 0.85,
                    'Context' => 0.82,
                    'Learner' => 0.78,
                    'Discipline' => 0.75,
                    'Deliberative' => 0.70
                ]
            ],
            [
                'name' => 'Копирайтер',
                'description' => 'Автор текстов для рекламы, маркетинга и корпоративных коммуникаций.',
                'talents' => [
                    'Communication' => 0.95,
                    'Ideation' => 0.90,
                    'Input' => 0.88,
                    'Individualization' => 0.85,
                    'Adaptability' => 0.82,
                    'Learner' => 0.78,
                    'Empathy' => 0.75,
                    'Strategic' => 0.70
                ]
            ],
            [
                'name' => 'Предприниматель',
                'description' => 'Создатель и владелец собственного бизнеса, принимающий стратегические решения.',
                'talents' => [
                    'Activator' => 0.95,
                    'Strategic' => 0.90,
                    'Self-Assurance' => 0.88,
                    'Maximizer' => 0.85,
                    'Futuristic' => 0.82,
                    'Competition' => 0.78,
                    'Command' => 0.75,
                    'Achiever' => 0.70
                ]
            ],
            [
                'name' => 'Психолог',
                'description' => 'Специалист по изучению психики и поведения людей, оказывающий психологическую помощь.',
                'talents' => [
                    'Empathy' => 0.95,
                    'Individualization' => 0.90,
                    'Developer' => 0.88,
                    'Harmony' => 0.85,
                    'Relator' => 0.82,
                    'Input' => 0.78,
                    'Intellection' => 0.75,
                    'Positivity' => 0.70
                ]
            ],
            [
                'name' => 'Учитель',
                'description' => 'Педагог, передающий знания и развивающий способности учеников.',
                'talents' => [
                    'Developer' => 0.95,
                    'Learner' => 0.90,
                    'Communication' => 0.88,
                    'Empathy' => 0.85,
                    'Maximizer' => 0.82,
                    'Positivity' => 0.78,
                    'Individualization' => 0.75,
                    'Includer' => 0.70
                ]
            ],
            [
                'name' => 'Журналист',
                'description' => 'Сборщик и распространитель новостей, создатель информационного контента.',
                'talents' => [
                    'Communication' => 0.95,
                    'Input' => 0.90,
                    'Activator' => 0.88,
                    'Connectedness' => 0.85,
                    'Context' => 0.82,
                    'Learner' => 0.78,
                    'Adaptability' => 0.75,
                    'Woo' => 0.70
                ]
            ]
        ];

        foreach ($professions as $professionData) {
            // Создаем профессию
            $profession = Profession::updateOrCreate(
                ['name' => $professionData['name']],
                ['description' => $professionData['description']]
            );

            $this->info("Created/Updated profession: {$profession->name}");

            // Присваиваем коэффициенты только указанным талантам
            $talentCoefficients = [];
            
            foreach ($professionData['talents'] as $talentName => $coefficient) {
                $talent = $talents->where('name', $talentName)->first();
                if ($talent) {
                    $talentCoefficients[$talent->id] = ['coefficient' => $coefficient];
                } else {
                    $this->warn("Talent '{$talentName}' not found for profession '{$profession->name}'");
                }
            }

            // Синхронизируем только выбранные таланты с коэффициентами
            $profession->talents()->sync($talentCoefficients);
            
            $this->info("  - Assigned coefficients to " . count($talentCoefficients) . " talents");
        }

        $this->info('Demo profession data created successfully!');
        $this->info('Total professions: ' . Profession::count());
        
        return 0;
    }
}
