<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Profession;
use App\Models\Sphere;

class UpdateProfessionsWithSpheres extends Command
{
    protected $signature = 'professions:update-spheres';
    protected $description = 'Update professions with sphere assignments and activate them';

    public function handle()
    {
        $this->info('Updating professions with sphere assignments...');

        // Получаем сферы
        $spheres = Sphere::all()->keyBy('name');

        // Маппинг профессий к сферам
        $professionSphereMapping = [
            'Менеджер проектов' => 'Бизнес и управление',
            'Разработчик ПО' => 'Информационные технологии',
            'Менеджер по продажам' => 'Бизнес и управление',
            'HR-специалист' => 'Бизнес и управление',
            'Маркетолог' => 'Бизнес и управление',
            'Финансовый аналитик' => 'Финансы и экономика',
            'Дизайнер UX/UI' => 'Творчество и искусство',
            'Консультант' => 'Бизнес и управление',
            'Руководитель команды' => 'Бизнес и управление',
            'Аналитик данных' => 'Информационные технологии',
            'Копирайтер' => 'Творчество и искусство',
            'Предприниматель' => 'Бизнес и управление',
            'Психолог' => 'Здравоохранение и медицина',
            'Учитель' => 'Образование и наука',
            'Журналист' => 'Творчество и искусство',
        ];

        $updated = 0;
        
        foreach ($professionSphereMapping as $professionName => $sphereName) {
            $profession = Profession::where('name', $professionName)->first();
            $sphere = $spheres->get($sphereName);
            
            if ($profession && $sphere) {
                $profession->update([
                    'sphere_id' => $sphere->id,
                    'is_active' => true
                ]);
                
                $this->info("Updated: {$professionName} -> {$sphereName}");
                $updated++;
            } else {
                $this->warn("Not found: Profession '{$professionName}' or Sphere '{$sphereName}'");
            }
        }

        $this->info("Updated {$updated} professions successfully!");
        
        // Показываем статистику
        $this->info("\nCurrent statistics:");
        $this->table(
            ['Sphere', 'Active Professions'],
            Sphere::with(['professions' => function($q) {
                $q->where('is_active', true);
            }])
            ->get()
            ->map(function($sphere) {
                return [
                    $sphere->name,
                    $sphere->professions->count()
                ];
            })
        );

        return Command::SUCCESS;
    }
}
