<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateRegistrationsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE registration_type AS ENUM ('presenter', 'audience'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        $this->db->query("DO \$\$ BEGIN CREATE TYPE registration_status AS ENUM ('pending', 'approved', 'rejected'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        $this->db->query("DO \$\$ BEGIN CREATE TYPE payment_status AS ENUM ('pending', 'paid', 'failed'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'user_id' => ['type' => 'INTEGER'],
            'event_id' => ['type' => 'INTEGER'],
            'registration_type' => ['type' => 'registration_type'],
            'registration_status' => ['type' => 'registration_status', 'default' => 'pending'],
            'payment_status' => ['type' => 'payment_status', 'default' => 'pending'],
            'qr_code' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'attended' => ['type' => 'BOOLEAN', 'default' => false],
            'attendance_time' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('registrations');
        
        $this->db->query("CREATE UNIQUE INDEX unique_user_event ON registrations(user_id, event_id)");
        $this->db->query("CREATE TRIGGER update_registrations_updated_at BEFORE INSERT OR UPDATE ON registrations FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_registrations_user_event ON registrations(user_id, event_id)");
    }

    public function down()
    {
        $this->forge->dropTable('registrations');
        $this->db->query("DROP TYPE IF EXISTS registration_type CASCADE");
        $this->db->query("DROP TYPE IF EXISTS registration_status CASCADE");
        $this->db->query("DROP TYPE IF EXISTS payment_status CASCADE");
    }
}