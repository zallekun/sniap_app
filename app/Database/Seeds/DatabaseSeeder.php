<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Jalankan semua seeders dalam urutan yang benar
        $this->call('UserSeeder');
        $this->call('AbstractCategorySeeder');
        $this->call('EventSeeder');
        $this->call('SystemSettingSeeder');
        $this->call('VoucherSeeder');
        
        echo "✅ All seeders completed successfully!\n";
        echo "📧 Admin login: admin@snia.ac.id / admin123\n";
        echo "🔍 Reviewer login: reviewer1@snia.ac.id / reviewer123\n";
        echo "👤 Sample presenter: presenter@example.com / presenter123\n";
    }
}