<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateSystemSettingsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE setting_category AS ENUM ('general', 'payment', 'email', 'file'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'setting_value' => ['type' => 'TEXT'],
            'setting_category' => ['type' => 'setting_category', 'default' => 'general'],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'updated_by' => ['type' => 'INTEGER', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('updated_by', 'users', 'id');
        $this->forge->createTable('system_settings');
        
        $this->db->query("CREATE TRIGGER update_system_settings_updated_at BEFORE INSERT OR UPDATE ON system_settings FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('system_settings');
        $this->db->query("DROP TYPE IF EXISTS setting_category CASCADE");
    }
}