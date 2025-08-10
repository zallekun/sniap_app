<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVoucherUsageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'voucher_id' => ['type' => 'INTEGER'],
            'user_id' => ['type' => 'INTEGER'],
            'registration_id' => ['type' => 'INTEGER', 'null' => true],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'used_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('voucher_id', 'vouchers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('voucher_usage');
        
        $this->db->query("CREATE TRIGGER update_voucher_usage_used_at BEFORE INSERT ON voucher_usage FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_voucher_usage_user ON voucher_usage(user_id)");
        $this->db->query("CREATE INDEX idx_voucher_usage_voucher ON voucher_usage(voucher_id)");
    }

    public function down()
    {
        $this->forge->dropTable('voucher_usage');
    }
}
