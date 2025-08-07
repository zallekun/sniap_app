<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'SNIA 2024 - Seminar Nasional Informatika dan Aplikasinya',
                'description' => 'Seminar nasional yang membahas perkembangan terkini dalam bidang informatika dan aplikasinya dalam berbagai sektor industri. Tema tahun ini: "Digital Transformation and Innovation in the Era of Artificial Intelligence".',
                'event_date' => '2024-12-15',
                'event_time' => '08:00:00',
                'format' => 'offline',
                'location' => 'Gedung Serbaguna Universitas Indonesia, Kampus Depok, Jawa Barat',
                'zoom_link' => null,
                'registration_fee' => 150000.00,
                'max_participants' => 500,
                'registration_deadline' => '2024-12-01 23:59:59',
                'abstract_deadline' => '2024-11-15 23:59:59',
                'registration_active' => true,
                'abstract_submission_active' => true,
                'is_active' => true,
            ],
            [
                'title' => 'SNIA Workshop: AI in Education',
                'description' => 'Workshop khusus membahas penerapan Artificial Intelligence dalam bidang pendidikan.',
                'event_date' => '2024-12-16',
                'event_time' => '13:00:00',
                'format' => 'online',
                'location' => null,
                'zoom_link' => 'https://zoom.us/j/123456789',
                'registration_fee' => 75000.00,
                'max_participants' => 200,
                'registration_deadline' => '2024-12-10 23:59:59',
                'abstract_deadline' => '2024-11-20 23:59:59',
                'registration_active' => true,
                'abstract_submission_active' => true,
                'is_active' => true,
            ],
        ];

        foreach ($data as $event) {
            $this->db->table('events')->insert($event);
        }
        
        echo "✅ Events seeded successfully (2 events created)\n";
    }
}