<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Admin User
            [
                'email' => 'admin@snia.ac.id',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'SNIA',
                'last_name' => 'Administrator',
                'phone' => '081234567890',
                'institution' => 'SNIA Committee',
                'role' => 'admin',
                'is_verified' => true,
            ],
            
            // Reviewer Users
            [
                'email' => 'reviewer1@snia.ac.id',
                'password' => password_hash('reviewer123', PASSWORD_DEFAULT),
                'first_name' => 'Dr. Ahmad',
                'last_name' => 'Wijaya',
                'phone' => '081234567891',
                'institution' => 'Universitas Indonesia',
                'role' => 'reviewer',
                'is_verified' => true,
            ],
            [
                'email' => 'reviewer2@snia.ac.id',
                'password' => password_hash('reviewer123', PASSWORD_DEFAULT),
                'first_name' => 'Prof. Sari',
                'last_name' => 'Kusuma',
                'phone' => '081234567892',
                'institution' => 'Institut Teknologi Bandung',
                'role' => 'reviewer',
                'is_verified' => true,
            ],
            [
                'email' => 'reviewer3@snia.ac.id',
                'password' => password_hash('reviewer123', PASSWORD_DEFAULT),
                'first_name' => 'Dr. Budi',
                'last_name' => 'Santoso',
                'phone' => '081234567893',
                'institution' => 'Universitas Gadjah Mada',
                'role' => 'reviewer',
                'is_verified' => true,
            ],
            
            // Sample Presenter Users
            [
                'email' => 'presenter@example.com',
                'password' => password_hash('presenter123', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '081234567894',
                'institution' => 'Institut Teknologi Sepuluh Nopember',
                'role' => 'presenter',
                'is_verified' => true,
            ],
            [
                'email' => 'presenter2@example.com',
                'password' => password_hash('presenter123', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '081234567895',
                'institution' => 'Universitas Brawijaya',
                'role' => 'presenter',
                'is_verified' => true,
            ],
            
            // Sample Audience Users
            [
                'email' => 'audience1@example.com',
                'password' => password_hash('audience123', PASSWORD_DEFAULT),
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'phone' => '081234567896',
                'institution' => 'Universitas Diponegoro',
                'role' => 'audience',
                'is_verified' => true,
            ],
            [
                'email' => 'audience2@example.com',
                'password' => password_hash('audience123', PASSWORD_DEFAULT),
                'first_name' => 'Bob',
                'last_name' => 'Wilson',
                'phone' => '081234567897',
                'institution' => 'Universitas Airlangga',
                'role' => 'audience',
                'is_verified' => true,
            ],
        ];

        foreach ($data as $user) {
            $this->db->table('users')->insert($user);
        }
        
        echo "✅ Users seeded successfully (8 users created)\n";
    }
}