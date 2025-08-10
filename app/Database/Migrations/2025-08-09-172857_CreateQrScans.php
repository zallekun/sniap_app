<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQrScans extends Migration
{
    public function up()
    {
        // Create qr_scans table for tracking scan history
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'qr_code_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'scan_type' => [
                'type' => 'ENUM',
                'constraint' => ['check_in', 'check_out', 'session_access', 'certificate', 'verification'],
                'default' => 'check_in',
            ],
            'scanner_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Admin/staff who scanned the QR'
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Scan location (e.g., Main Hall, Registration Desk)'
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
                'comment' => 'IP address of scanner'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Browser/device info'
            ],
            'scan_result' => [
                'type' => 'ENUM',
                'constraint' => ['success', 'failed', 'expired', 'invalid', 'duplicate'],
                'default' => 'success',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes about the scan'
            ],
            'scanned_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
                'comment' => 'When the scan occurred'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
        ]);

        // Set primary key
        $this->forge->addPrimaryKey('id');
        
        // Add foreign key constraints
        $this->forge->addForeignKey('qr_code_id', 'qr_codes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('scanner_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        
        // Add indexes for better performance
        $this->forge->addKey('qr_code_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('scanner_user_id');
        $this->forge->addKey('scan_type');
        $this->forge->addKey('scan_result');
        $this->forge->addKey('scanned_at');
        
        // Create table
        $this->forge->createTable('qr_scans');
    }

    public function down()
    {
        $this->forge->dropTable('qr_scans');
    }
}