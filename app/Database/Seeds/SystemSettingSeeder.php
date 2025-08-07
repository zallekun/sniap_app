<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // General Settings
            [
                'setting_key' => 'site_name',
                'setting_value' => 'SNIA 2024',
                'setting_category' => 'general',
                'description' => 'Nama situs web',
            ],
            [
                'setting_key' => 'site_description',
                'setting_value' => 'Seminar Nasional Informatika dan Aplikasinya',
                'setting_category' => 'general',
                'description' => 'Deskripsi situs web',
            ],
            [
                'setting_key' => 'contact_email',
                'setting_value' => 'admin@snia.ac.id',
                'setting_category' => 'general',
                'description' => 'Email kontak utama',
            ],
            [
                'setting_key' => 'contact_phone',
                'setting_value' => '+62-21-12345678',
                'setting_category' => 'general',
                'description' => 'Nomor telepon kontak',
            ],
            
            // File Settings
            [
                'setting_key' => 'max_abstract_size',
                'setting_value' => '5242880',
                'setting_category' => 'file',
                'description' => 'Maksimal ukuran file abstrak (5MB)',
            ],
            [
                'setting_key' => 'allowed_abstract_types',
                'setting_value' => 'pdf,doc,docx',
                'setting_category' => 'file',
                'description' => 'Tipe file yang diizinkan untuk abstrak',
            ],
            [
                'setting_key' => 'max_payment_proof_size',
                'setting_value' => '2097152',
                'setting_category' => 'file',
                'description' => 'Maksimal ukuran bukti pembayaran (2MB)',
            ],
            [
                'setting_key' => 'allowed_payment_types',
                'setting_value' => 'jpg,jpeg,png,pdf',
                'setting_category' => 'file',
                'description' => 'Tipe file bukti pembayaran',
            ],
            
            // Payment Settings
            [
                'setting_key' => 'payment_gateway',
                'setting_value' => 'midtrans',
                'setting_category' => 'payment',
                'description' => 'Payment gateway yang digunakan',
            ],
            [
                'setting_key' => 'midtrans_server_key',
                'setting_value' => 'SB-Mid-server-sandbox-key',
                'setting_category' => 'payment',
                'description' => 'Midtrans Server Key (Sandbox)',
            ],
            [
                'setting_key' => 'midtrans_client_key',
                'setting_value' => 'SB-Mid-client-sandbox-key',
                'setting_category' => 'payment',
                'description' => 'Midtrans Client Key (Sandbox)',
            ],
            [
                'setting_key' => 'payment_expiry_hours',
                'setting_value' => '24',
                'setting_category' => 'payment',
                'description' => 'Batas waktu pembayaran (jam)',
            ],
            
            // Email Settings
            [
                'setting_key' => 'smtp_host',
                'setting_value' => 'smtp.gmail.com',
                'setting_category' => 'email',
                'description' => 'SMTP Host',
            ],
            [
                'setting_key' => 'smtp_port',
                'setting_value' => '587',
                'setting_category' => 'email',
                'description' => 'SMTP Port',
            ],
            [
                'setting_key' => 'smtp_user',
                'setting_value' => 'admin@snia.ac.id',
                'setting_category' => 'email',
                'description' => 'SMTP Username',
            ],
            [
                'setting_key' => 'smtp_password',
                'setting_value' => '',
                'setting_category' => 'email',
                'description' => 'SMTP Password (App Password)',
            ],
            [
                'setting_key' => 'email_from_name',
                'setting_value' => 'SNIA 2024 System',
                'setting_category' => 'email',
                'description' => 'Nama pengirim email',
            ],
        ];

        foreach ($data as $setting) {
            $this->db->table('system_settings')->insert($setting);
        }
        
        echo "✅ System settings seeded successfully (16 settings created)\n";
    }
}