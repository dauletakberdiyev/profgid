<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TalentDomain;

class TalentDomainsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $domains = [
            [
                'name' => 'executing',
                'description' => 'People with dominant Executing themes know how to make things happen.'
            ],
            [
                'name' => 'influencing',
                'description' => 'People with dominant Influencing themes know how to take charge, speak up and make sure others are heard.'
            ],
            [
                'name' => 'relationship',
                'description' => 'People with dominant Relationship Building themes have the unique ability to create strong relationships that hold a team together.'
            ],
            [
                'name' => 'strategic',
                'description' => 'People with dominant Strategic Thinking themes absorb and analyze information that informs better decisions.'
            ]
        ];

        foreach ($domains as $domain) {
            TalentDomain::create($domain);
        }
    }
}
