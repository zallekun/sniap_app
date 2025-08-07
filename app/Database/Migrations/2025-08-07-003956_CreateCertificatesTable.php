<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateCertificatesTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE certificate_type AS ENUM ('presenter', 'participant'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'registration_id' => ['type' => 'INTEGER'],
            'certificate_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'certificate_type' => ['type' => 'certificate_type'],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'generated_by' => ['type' => 'INTEGER'],
            'generated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('generated_by', 'users', 'id');
        $this->forge->createTable('certificates');
        
        $this->db->query("CREATE TRIGGER update_certificates_generated_at BEFORE INSERT ON certificates FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('certificates');
        $this->db->query("DROP TYPE IF EXISTS certificate_type CASCADE");
    }
}