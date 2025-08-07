<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE payment_status_type AS ENUM ('pending', 'success', 'failed', 'expired'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'registration_id' => ['type' => 'INTEGER'],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'final_amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'voucher_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'payment_gateway' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'transaction_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_status' => ['type' => 'payment_status_type', 'default' => 'pending'],
            'payment_proof' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'paid_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('voucher_code', 'vouchers', 'code');
        $this->forge->createTable('payments');
        
        $this->db->query("CREATE TRIGGER update_payments_updated_at BEFORE INSERT OR UPDATE ON payments FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_payments_registration ON payments(registration_id)");
    }

    public function down()
    {
        $this->forge->dropTable('payments');
        $this->db->query("DROP TYPE IF EXISTS payment_status_type CASCADE");
    }
}