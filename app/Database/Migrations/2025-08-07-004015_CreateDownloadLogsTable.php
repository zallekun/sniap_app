<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateDownloadLogsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE download_type AS ENUM ('all_participants', 'abstracts', 'payments', 'attendance'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'user_id' => ['type' => 'INTEGER'],
            'download_type' => ['type' => 'download_type'],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'downloaded_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->createTable('download_logs');
        
        $this->db->query("CREATE TRIGGER update_download_logs_downloaded_at BEFORE INSERT ON download_logs FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('download_logs');
        $this->db->query("DROP TYPE IF EXISTS download_type CASCADE");
    }
}