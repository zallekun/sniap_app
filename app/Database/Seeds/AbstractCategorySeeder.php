<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AbstractCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Artificial Intelligence',
                'description' => 'Penelitian terkait AI, Machine Learning, Deep Learning, Natural Language Processing, Computer Vision, dan aplikasinya dalam berbagai bidang.',
                'is_active' => true,
            ],
            [
                'name' => 'Software Engineering',
                'description' => 'Penelitian terkait pengembangan perangkat lunak, metodologi pengembangan, software architecture, testing, dan manajemen proyek software.',
                'is_active' => true,
            ],
            [
                'name' => 'Computer Networks',
                'description' => 'Penelitian terkait jaringan komputer, protokol komunikasi, network security, wireless networks, dan Internet of Things (IoT).',
                'is_active' => true,
            ],
            [
                'name' => 'Database Systems',
                'description' => 'Penelitian terkait sistem basis data, data mining, big data analytics, data warehousing, dan sistem informasi.',
                'is_active' => true,
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Penelitian terkait keamanan siber, kriptografi, network security, ethical hacking, dan digital forensics.',
                'is_active' => true,
            ],
            [
                'name' => 'Human-Computer Interaction',
                'description' => 'Penelitian terkait interaksi manusia-komputer, user experience (UX), user interface (UI), dan usability testing.',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Computing',
                'description' => 'Penelitian terkait pengembangan aplikasi mobile, mobile security, mobile networks, dan ubiquitous computing.',
                'is_active' => true,
            ],
            [
                'name' => 'Cloud Computing',
                'description' => 'Penelitian terkait cloud infrastructure, distributed systems, virtualization, dan cloud security.',
                'is_active' => true,
            ],
            [
                'name' => 'Game Development',
                'description' => 'Penelitian terkait pengembangan game, game engine, virtual reality (VR), augmented reality (AR), dan multimedia.',
                'is_active' => true,
            ],
            [
                'name' => 'Bioinformatics',
                'description' => 'Penelitian terkait penerapan informatika dalam bidang biologi, computational biology, dan medical informatics.',
                'is_active' => true,
            ],
        ];

        foreach ($data as $category) {
            $this->db->table('abstract_categories')->insert($category);
        }
        
        echo "✅ Abstract categories seeded successfully (10 categories created)\n";
    }
}