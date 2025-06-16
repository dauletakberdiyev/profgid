<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Talent;
use App\Models\TalentDomain;

class TalentsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get talent domains
        $executing = TalentDomain::where('name', 'executing')->first();
        $influencing = TalentDomain::where('name', 'influencing')->first();
        $relationship = TalentDomain::where('name', 'relationship')->first();
        $strategic = TalentDomain::where('name', 'strategic')->first();

        $talents = [
            // EXECUTING Domain
            [
                'name' => 'Achiever',
                'description' => 'People exceptionally talented in the Achiever theme work hard and possess a great deal of stamina. They take immense satisfaction in being busy and productive.',
                'icon' => 'heroicon-o-trophy',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Arranger',
                'description' => 'People exceptionally talented in the Arranger theme can organize, but they also have a flexibility that complements this ability. They like to determine how all of the pieces and resources can be arranged for maximum productivity.',
                'icon' => 'heroicon-o-squares-2x2',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Belief',
                'description' => 'People exceptionally talented in the Belief theme have certain core values that are unchanging. Out of these values emerges a defined purpose for their lives.',
                'icon' => 'heroicon-o-heart',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Consistency',
                'description' => 'People exceptionally talented in the Consistency theme are keenly aware of the need to treat people the same. They try to treat everyone with equality by setting up clear rules and adhering to them.',
                'icon' => 'heroicon-o-scale',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Deliberative',
                'description' => 'People exceptionally talented in the Deliberative theme are best described by the serious care they take in making decisions or choices. They anticipate obstacles.',
                'icon' => 'heroicon-o-shield-check',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Discipline',
                'description' => 'People exceptionally talented in the Discipline theme enjoy routine and structure. Their world is best described by the order they create.',
                'icon' => 'heroicon-o-clipboard-document-list',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Focus',
                'description' => 'People exceptionally talented in the Focus theme can take a direction, follow through, and make the corrections necessary to stay on track. They prioritize, then act.',
                'icon' => 'heroicon-o-target',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Responsibility',
                'description' => 'People exceptionally talented in the Responsibility theme take psychological ownership of what they say they will do. They are committed to stable values such as honesty and loyalty.',
                'icon' => 'heroicon-o-hand-raised',
                'talent_domain_id' => $executing->id
            ],
            [
                'name' => 'Restorative',
                'description' => 'People exceptionally talented in the Restorative theme are adept at dealing with problems. They are good at figuring out what is wrong and resolving it.',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'talent_domain_id' => $executing->id
            ],

            // INFLUENCING Domain
            [
                'name' => 'Activator',
                'description' => 'People exceptionally talented in the Activator theme can make things happen by turning thoughts into action. They want to do things now, rather than simply talk about them.',
                'icon' => 'heroicon-o-bolt',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Command',
                'description' => 'People exceptionally talented in the Command theme have presence. They can take control of a situation and make decisions.',
                'icon' => 'heroicon-o-megaphone',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Communication',
                'description' => 'People exceptionally talented in the Communication theme generally find it easy to put their thoughts into words. They are good conversationalists and presenters.',
                'icon' => 'heroicon-o-chat-bubble-left-right',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Competition',
                'description' => 'People exceptionally talented in the Competition theme measure their progress against the performance of others. They strive to win first place and revel in contests.',
                'icon' => 'heroicon-o-trophy',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Maximizer',
                'description' => 'People exceptionally talented in the Maximizer theme focus on strengths as a way to stimulate personal and group excellence. They seek to transform something strong into something superb.',
                'icon' => 'heroicon-o-arrow-trending-up',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Self-Assurance',
                'description' => 'People exceptionally talented in the Self-Assurance theme feel confident in their ability to take risks and manage their own lives. They have an inner compass that gives them certainty in their decisions.',
                'icon' => 'heroicon-o-user-circle',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Significance',
                'description' => 'People exceptionally talented in the Significance theme want to make a big impact. They are independent and prioritize projects based on how much influence they will have on their organization or people around them.',
                'icon' => 'heroicon-o-star',
                'talent_domain_id' => $influencing->id
            ],
            [
                'name' => 'Woo',
                'description' => 'People exceptionally talented in the Woo theme love the challenge of meeting new people and winning them over. They derive satisfaction from breaking the ice and making a connection with someone.',
                'icon' => 'heroicon-o-face-smile',
                'talent_domain_id' => $influencing->id
            ],

            // RELATIONSHIP BUILDING Domain
            [
                'name' => 'Adaptability',
                'description' => 'People exceptionally talented in the Adaptability theme prefer to go with the flow. They tend to be "now" people who take things as they come and discover the future one day at a time.',
                'icon' => 'heroicon-o-arrows-right-left',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Connectedness',
                'description' => 'People exceptionally talented in the Connectedness theme have faith in the links among all things. They believe there are few coincidences and that almost every event has meaning.',
                'icon' => 'heroicon-o-link',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Developer',
                'description' => 'People exceptionally talented in the Developer theme recognize and cultivate the potential in others. They spot the signs of each small improvement and derive satisfaction from evidence of progress.',
                'icon' => 'heroicon-o-academic-cap',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Empathy',
                'description' => 'People exceptionally talented in the Empathy theme can sense other people\'s emotions by imagining themselves in others\' lives or situations.',
                'icon' => 'heroicon-o-heart',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Harmony',
                'description' => 'People exceptionally talented in the Harmony theme look for consensus. They don\'t enjoy conflict; rather, they seek areas of agreement.',
                'icon' => 'heroicon-o-balance-scale',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Includer',
                'description' => 'People exceptionally talented in the Includer theme want to include others and make them feel part of the group. They are accepting of others and show awareness of those who feel left out.',
                'icon' => 'heroicon-o-user-group',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Individualization',
                'description' => 'People exceptionally talented in the Individualization theme are intrigued with the unique qualities of each person. They have a gift for figuring out how different people can work together productively.',
                'icon' => 'heroicon-o-user',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Positivity',
                'description' => 'People exceptionally talented in the Positivity theme have contagious enthusiasm. They are upbeat and can get others excited about what they are going to do.',
                'icon' => 'heroicon-o-sun',
                'talent_domain_id' => $relationship->id
            ],
            [
                'name' => 'Relator',
                'description' => 'People exceptionally talented in the Relator theme enjoy close relationships with others. They find deep satisfaction in working hard with friends to achieve a goal.',
                'icon' => 'heroicon-o-users',
                'talent_domain_id' => $relationship->id
            ],

            // STRATEGIC THINKING Domain
            [
                'name' => 'Analytical',
                'description' => 'People exceptionally talented in the Analytical theme search for reasons and causes. They have the ability to think about all of the factors that might affect a situation.',
                'icon' => 'heroicon-o-magnifying-glass',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Context',
                'description' => 'People exceptionally talented in the Context theme enjoy thinking about the past. They understand the present by researching its history.',
                'icon' => 'heroicon-o-clock',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Futuristic',
                'description' => 'People exceptionally talented in the Futuristic theme are inspired by the future and what could be. They energize others with their visions of the future.',
                'icon' => 'heroicon-o-eye',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Ideation',
                'description' => 'People exceptionally talented in the Ideation theme are fascinated by ideas. They are able to find connections between seemingly disparate phenomena.',
                'icon' => 'heroicon-o-light-bulb',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Input',
                'description' => 'People exceptionally talented in the Input theme have a need to collect and archive. They may accumulate information, ideas, artifacts or even relationships.',
                'icon' => 'heroicon-o-inbox',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Intellection',
                'description' => 'People exceptionally talented in the Intellection theme are characterized by their intellectual activity. They are introspective and appreciate intellectual discussions.',
                'icon' => 'heroicon-o-brain',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Learner',
                'description' => 'People exceptionally talented in the Learner theme have a great desire to learn and want to continuously improve. The process of learning, rather than the outcome, excites them.',
                'icon' => 'heroicon-o-book-open',
                'talent_domain_id' => $strategic->id
            ],
            [
                'name' => 'Strategic',
                'description' => 'People exceptionally talented in the Strategic theme create alternative ways to proceed. Faced with any given scenario, they can quickly spot the relevant patterns and issues.',
                'icon' => 'heroicon-o-map',
                'talent_domain_id' => $strategic->id
            ]
        ];

        foreach ($talents as $talent) {
            Talent::create($talent);
        }
    }
}
