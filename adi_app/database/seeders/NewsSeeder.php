<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        $newsData = [
            [
                'title' => 'Laravel 11 Released with Amazing New Features',
                'url' => 'https://laravel.com/docs/11.x',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'PHP 8.3 Performance Improvements Announced',
                'url' => 'https://www.php.net/releases/8.3',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'AI and Machine Learning Trends in 2024',
                'url' => 'Artificial Intelligence continues to revolutionize various industries. This year, we\'ve seen significant advancements in natural language processing, computer vision, and autonomous systems. Companies are increasingly adopting AI solutions to improve efficiency and create innovative products. The integration of machine learning models into everyday applications is becoming more seamless, making technology more accessible to the general public.',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Remote Work Culture Post-Pandemic',
                'url' => 'The shift to remote work has fundamentally changed how organizations operate. Studies show that productivity has remained stable or even increased for many companies. However, challenges around team collaboration, employee engagement, and work-life balance persist. Organizations are now adopting hybrid models that combine the best of both remote and in-office work environments.',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Sustainable Technology Solutions for Climate Change',
                'url' => 'https://example.com/sustainable-tech-2024',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Cryptocurrency Market Analysis Q4 2024',
                'url' => 'The cryptocurrency market has shown remarkable resilience throughout 2024. Bitcoin and Ethereum have maintained their positions as market leaders, while several altcoins have gained significant traction. Regulatory clarity in major markets has boosted investor confidence. DeFi protocols continue to innovate, offering new financial products and services that challenge traditional banking systems.',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Space Exploration Milestones This Year',
                'url' => 'https://nasa.gov/missions/2024',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Healthcare Innovation Through Digital Transformation',
                'url' => 'Digital health solutions are transforming patient care delivery. Telemedicine platforms have become mainstream, providing accessible healthcare to remote areas. Wearable devices now monitor vital signs continuously, enabling preventive care strategies. AI-powered diagnostic tools assist medical professionals in early disease detection, potentially saving millions of lives through timely intervention.',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Education Technology Reshaping Learning',
                'url' => 'https://edtech.org/trends/2024',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Cybersecurity Threats and Defense Strategies',
                'url' => 'Cybersecurity remains a critical concern as digital transformation accelerates. Ransomware attacks have become more sophisticated, targeting critical infrastructure and healthcare systems. Organizations are investing heavily in zero-trust security architectures and employee training programs. The rise of quantum computing also presents new challenges and opportunities for data encryption and security protocols.',
                'content' => 'Lorem ipsum dolor sit amet',
                'created_by' => $users->random()->id,
            ]
        ];

        foreach ($newsData as $index => $data) {
                    News::create([
                        'title' => $data['title'],
                        'url' => 'https://www.youtube.com/watch?v=LXb3EKWsInQ&ab_channel=Jacob%2BKatieSchwarz',
                        'content' => $data['content'],
                        'slug' => \Illuminate\Support\Str::slug($data['title']),
                        'created_by' => $data['created_by'],
                        'updated_by' => $data['created_by'],
                        'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 15))->subHours(rand(0, 23)),
                    ]);
        }

        $deletedNews = News::create([
            'title' => 'Old News That Was Deleted',
            'url' => 'https://www.youtube.com/watch?v=LXb3EKWsInQ&ab_channel=Jacob%2BKatieSchwarz',
            'slug' => 'old-news-that-was-deleted',
            'content' => 'Lorem ipsum dolor sit amet',
            'created_by' => $users->random()->id,
            'updated_by' => $users->random()->id,
            'created_at' => now()->subDays(45),
            'updated_at' => now()->subDays(40),
        ]);

        $deletedNews->delete();
        $deletedNews->update(['deleted_by' => $users->random()->id]);


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('News seeder completed successfully!');
        $this->command->info('Created ' . (count($newsData)) . ' active news articles');
        $this->command->info('Created 1 soft-deleted news for testing');
    }
}
