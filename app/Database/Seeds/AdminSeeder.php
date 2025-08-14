<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'first_name'    => 'Super',
            'last_name'     => 'Admin',
            'email'         => 'superadmin@snia.ac.id',
            'password'      => password_hash('admin123', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'is_verified'   => true,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // Check if admin already exists
        $existingAdmin = $this->db->table('users')->where('email', $data['email'])->get()->getRowArray();
        
        if (!$existingAdmin) {
            $this->db->table('users')->insert($data);
            echo "✅ Admin user created successfully!\n";
        } else {
            // Update password for existing admin
            $this->db->table('users')
                ->where('email', $data['email'])
                ->update([
                    'password' => $data['password'],
                    'role' => 'admin',
                    'is_verified' => true,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            echo "✅ Admin user updated successfully!\n";
        }
        
        // Also create a presenter account for testing
        $presenterData = [
            'first_name'    => 'Test',
            'last_name'     => 'Presenter',
            'email'         => 'presenter@snia.ac.id',
            'password'      => password_hash('presenter123', PASSWORD_DEFAULT),
            'role'          => 'presenter',
            'is_verified'   => true,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $existingPresenter = $this->db->table('users')->where('email', $presenterData['email'])->get()->getRowArray();
        
        if (!$existingPresenter) {
            $this->db->table('users')->insert($presenterData);
            echo "✅ Presenter user created successfully!\n";
        } else {
            $this->db->table('users')
                ->where('email', $presenterData['email'])
                ->update([
                    'password' => $presenterData['password'],
                    'role' => 'presenter',
                    'is_verified' => true,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            echo "✅ Presenter user updated successfully!\n";
        }

        echo "Admin Login Details:\n";
        echo "Email: superadmin@snia.ac.id\n";
        echo "Password: admin123\n";
        echo "Role: admin\n";
        echo "\nPresenter Login Details:\n";
        echo "Email: presenter@snia.ac.id\n";
        echo "Password: presenter123\n";
        echo "Role: presenter\n";

        // Also create a reviewer account for testing
        $reviewerData = [
            'first_name'    => 'Test',
            'last_name'     => 'Reviewer',
            'email'         => 'reviewer@snia.ac.id',
            'password'      => password_hash('reviewer123', PASSWORD_DEFAULT),
            'role'          => 'reviewer',
            'is_verified'   => true,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $existingReviewer = $this->db->table('users')->where('email', $reviewerData['email'])->get()->getRowArray();
        
        if (!$existingReviewer) {
            $this->db->table('users')->insert($reviewerData);
            echo "\n✅ Reviewer user created successfully!\n";
        } else {
            $this->db->table('users')
                ->where('email', $reviewerData['email'])
                ->update([
                    'password' => $reviewerData['password'],
                    'role' => 'reviewer',
                    'is_verified' => true,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            echo "\n✅ Reviewer user updated successfully!\n";
        }

        echo "\nReviewer Login Details:\n";
        echo "Email: reviewer@snia.ac.id\n";
        echo "Password: reviewer123\n";
        echo "Role: reviewer\n";
    }
}