<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateAbstractCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('abstract_categories');
        
        $this->db->query("CREATE TRIGGER update_abstract_categories_created_at BEFORE INSERT ON abstract_categories FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }

    public function down()
    {
        $this->forge->dropTable('abstract_categories');
    }
}