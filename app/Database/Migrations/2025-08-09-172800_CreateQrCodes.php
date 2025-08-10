<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQrCodes extends Migration
{
    public function up()
    {
        // Create qr_codes table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'qr_data' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'JSON encoded QR data'
            ],
            'qr_image' => [
                'type' => 'LONGTEXT',
                'null' => false,
                'comment' => 'Base64 encoded QR image'
            ],
            'qr_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'SHA256 hash of QR data'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'expired'],
                'default' => 'active',
            ],
            'is_verified' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Whether email is verified'
            ],
            'last_scanned_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Last time QR was scanned'
            ],
            'scan_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Number of times scanned'
            ],
            'expires_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'QR code expiration time'
            ],
            'verified_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'When email was verified'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        // Set primary key
        $this->forge->addPrimaryKey('id');
        
        // Add foreign key constraint
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        // Add indexes for better performance
        $this->forge->addKey('user_id');
        $this->forge->addKey('qr_hash');
        $this->forge->addKey('status');
        $this->forge->addKey('is_verified');
        $this->forge->addKey('expires_at');
        
        // Create table
        $this->forge->createTable('qr_codes');
    }

    public function down()
    {
        $this->forge->dropTable('qr_codes');
    }
}