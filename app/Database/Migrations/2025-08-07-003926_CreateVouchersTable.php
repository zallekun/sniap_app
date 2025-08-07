<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateVouchersTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE discount_type AS ENUM ('percentage', 'fixed'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'discount_type' => ['type' => 'discount_type'],
            'discount_value' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'max_uses' => ['type' => 'INTEGER', 'null' => true],
            'used_count' => ['type' => 'INTEGER', 'default' => 0],
            'valid_from' => ['type' => 'TIMESTAMP'],
            'valid_until' => ['type' => 'TIMESTAMP'],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_by' => ['type' => 'INTEGER'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('created_by', 'users', 'id');
        $this->forge->createTable('vouchers');
        
        $this->db->query("CREATE TRIGGER update_vouchers_created_at BEFORE INSERT ON vouchers FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_vouchers_code ON vouchers(code)");
    }

    public function down()
    {
        $this->forge->dropTable('vouchers');
        $this->db->query("DROP TYPE IF EXISTS discount_type CASCADE");
    }
}