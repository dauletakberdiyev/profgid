<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sphere;
use App\Models\Profession;

class CreateSphereDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-spheres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание демо данных для сфер профессий';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Создание сфер профессий...');

        $spheres = [
            [
                'name' => 'Информационные технологии',
                'name_kz' => 'Ақпараттық технологиялар',
                'name_en' => 'Information Technology',
                'description' => 'Сфера разработки программного обеспечения, системного администрирования, кибербезопасности и цифровых технологий',
                'description_kz' => 'Бағдарламалық қамтамасыз етуді әзірлеу, жүйелік әкімшілік ету, киберқауіпсіздік және цифрлық технологиялар саласы',
                'description_en' => 'Field of software development, system administration, cybersecurity and digital technologies',
                'color' => '#3B82F6',
                'icon' => 'heroicon-o-computer-desktop',
                'sort_order' => 1,
            ],
            [
                'name' => 'Бизнес и управление',
                'name_kz' => 'Бизнес және басқару',
                'name_en' => 'Business and Management',
                'description' => 'Сфера предпринимательства, корпоративного управления, консалтинга и бизнес-развития',
                'description_kz' => 'Кәсіпкерлік, корпоративтік басқару, консалтинг және бизнес-даму саласы',
                'description_en' => 'Field of entrepreneurship, corporate management, consulting and business development',
                'color' => '#10B981',
                'icon' => 'heroicon-o-briefcase',
                'sort_order' => 2,
            ],
            [
                'name' => 'Здравоохранение и медицина',
                'name_kz' => 'Денсаулық сақтау және медицина',
                'name_en' => 'Healthcare and Medicine',
                'description' => 'Сфера медицинского обслуживания, лечения, профилактики и медицинских исследований',
                'description_kz' => 'Медициналық қызмет көрсету, емдеу, алдын алу және медициналық зерттеулер саласы',
                'description_en' => 'Field of medical services, treatment, prevention and medical research',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-heart',
                'sort_order' => 3,
            ],
            [
                'name' => 'Образование и наука',
                'name_kz' => 'Білім және ғылым',
                'name_en' => 'Education and Science',
                'description' => 'Сфера образовательных услуг, научных исследований и академической деятельности',
                'description_kz' => 'Білім беру қызметтері, ғылыми зерттеулер және академиялық қызмет саласы',
                'description_en' => 'Field of educational services, scientific research and academic activities',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-academic-cap',
                'sort_order' => 4,
            ],
            [
                'name' => 'Творчество и искусство',
                'name_kz' => 'Шығармашылық және өнер',
                'name_en' => 'Creativity and Arts',
                'description' => 'Сфера творческой деятельности, дизайна, культуры и развлечений',
                'description_kz' => 'Шығармашылық қызмет, дизайн, мәдениет және ойын-сауық саласы',
                'description_en' => 'Field of creative activities, design, culture and entertainment',
                'color' => '#F59E0B',
                'icon' => 'heroicon-o-paint-brush',
                'sort_order' => 5,
            ],
            [
                'name' => 'Инженерия и техника',
                'name_kz' => 'Инженерия және техника',
                'name_en' => 'Engineering and Technology',
                'description' => 'Сфера технических специальностей, строительства и промышленного производства',
                'description_kz' => 'Техникалық мамандықтар, құрылыс және өнеркәсіптік өндіріс саласы',
                'description_en' => 'Field of technical specialties, construction and industrial production',
                'color' => '#6B7280',
                'icon' => 'heroicon-o-cog-6-tooth',
                'sort_order' => 6,
            ],
            [
                'name' => 'Финансы и экономика',
                'name_kz' => 'Қаржы және экономика',
                'name_en' => 'Finance and Economics',
                'description' => 'Сфера финансовых услуг, банковского дела, инвестиций и экономического анализа',
                'description_kz' => 'Қаржылық қызметтер, банк ісі, инвестициялар және экономикалық талдау саласы',
                'description_en' => 'Field of financial services, banking, investments and economic analysis',
                'color' => '#059669',
                'icon' => 'heroicon-o-banknotes',
                'sort_order' => 7,
            ],
        ];

        foreach ($spheres as $sphereData) {
            $sphere = Sphere::updateOrCreate(
                ['name' => $sphereData['name']],
                $sphereData
            );
            
            $this->line("✓ Создана сфера: {$sphere->name}");
        }

        $this->info('Обновление существующих профессий...');
        
        // Назначаем сферы для существующих профессий
        $professionSphereMap = [
            'Программист' => 'Информационные технологии',
            'Веб-разработчик' => 'Информационные технологии',
            'Системный администратор' => 'Информационные технологии',
            'DevOps инженер' => 'Информационные технологии',
            'Менеджер проектов' => 'Бизнес и управление',
            'Продукт-менеджер' => 'Бизнес и управление',
            'Маркетолог' => 'Бизнес и управление',
            'HR-менеджер' => 'Бизнес и управление',
            'Дизайнер' => 'Творчество и искусство',
            'Психолог' => 'Здравоохранение и медицина',
            'Учитель' => 'Образование и наука',
            'Врач' => 'Здравоохранение и медицина',
            'Инженер' => 'Инженерия и техника',
            'Финансовый аналитик' => 'Финансы и экономика',
            'Бухгалтер' => 'Финансы и экономика',
        ];

        foreach ($professionSphereMap as $professionName => $sphereName) {
            $profession = Profession::where('name', $professionName)->first();
            $sphere = Sphere::where('name', $sphereName)->first();
            
            if ($profession && $sphere) {
                $profession->update(['sphere_id' => $sphere->id]);
                $this->line("✓ Профессия '{$professionName}' назначена в сферу '{$sphereName}'");
            }
        }

        $this->info("\n🎉 Демо данные сфер успешно созданы!");
        $this->info("Создано сфер: " . count($spheres));
        $this->info("Обновлено профессий: " . count($professionSphereMap));
    }
}
