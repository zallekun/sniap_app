<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'code' => 'EARLY2024',
                'description' => 'Early Bird Discount - 20% off untuk pendaftar awal (berlaku hingga 30 November 2024)',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'max_uses' => 100,
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-11-30 23:59:59',
                'is_active' => true,
                'created_by' => 1, // Admin user
            ],
            [
                'code' => 'STUDENT50',
                'description' => 'Student Discount - 50% off untuk mahasiswa dengan KTM valid',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'max_uses' => null, // Unlimited
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-12-14 23:59:59',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'FREE2024',
                'description' => 'Free Registration - Gratis untuk keynote speaker dan invited speaker',
                'discount_type' => 'percentage',
                'discount_value' => 100.00,
                'max_uses' => 20,
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-12-14 23:59:59',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'FIXED100K',
                'description' => 'Fixed Discount - Potongan Rp 100.000 untuk peserta corporate',
                'discount_type' => 'fixed',
                'discount_value' => 100000.00,
                'max_uses' => 50,
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-12-14 23:59:59',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'WORKSHOP25',
                'description' => 'Workshop Discount - 25% off untuk workshop AI in Education',
                'discount_type' => 'percentage',
                'discount_value' => 25.00,
                'max_uses' => 30,
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-12-15 23:59:59',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'code' => 'LOYALTY15',
                'description' => 'Loyalty Discount - 15% off untuk peserta SNIA tahun sebelumnya',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'max_uses' => 200,
                'used_count' => 0,
                'valid_from' => '2024-10-01 00:00:00',
                'valid_until' => '2024-12-14 23:59:59',
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($data as $voucher) {
            $this->db->table('vouchers')->insert($voucher);
        }
        
        echo "✅ Vouchers seeded successfully (6 vouchers created)\n";
    }
}