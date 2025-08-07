<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'user_id' => ['type' => 'INTEGER'],
            'type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 255],
            'message' => ['type' => 'TEXT'],
            'sent_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'is_read' => ['type' => 'BOOLEAN', 'default' => false],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications');
        
        $this->db->query("CREATE TRIGGER update_notifications_sent_at BEFORE INSERT ON notifications FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_notifications_user ON notifications(user_id)");
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}