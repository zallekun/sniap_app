<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateLoasTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE upload_type AS ENUM ('admin', 'reviewer'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'registration_id' => ['type' => 'INTEGER'],
            'loa_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'upload_type' => ['type' => 'upload_type'],
            'generated_by' => ['type' => 'INTEGER'],
            'generated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('generated_by', 'users', 'id');
        $this->forge->createTable('loas');
        
        $this->db->query("CREATE TRIGGER update_loas_generated_at BEFORE INSERT ON loas FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('loas');
        $this->db->query("DROP TYPE IF EXISTS upload_type CASCADE");
    }
}