<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateEventSchedulesTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE session_type AS ENUM ('opening', 'keynote', 'presentation', 'break', 'closing'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'event_id' => ['type' => 'INTEGER'],
            'session_title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'session_type' => ['type' => 'session_type'],
            'start_time' => ['type' => 'TIME'],
            'end_time' => ['type' => 'TIME'],
            'speaker_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'room_location' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'zoom_link' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('event_schedules');
        
        $this->db->query("CREATE TRIGGER update_event_schedules_created_at BEFORE INSERT ON event_schedules FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('event_schedules');
        $this->db->query("DROP TYPE IF EXISTS session_type CASCADE");
    }
}