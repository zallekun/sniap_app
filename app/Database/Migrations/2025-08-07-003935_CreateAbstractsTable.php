<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateAbstractsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE review_status AS ENUM ('pending', 'accepted', 'accepted_with_revision', 'rejected'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        $this->db->query("DO \$\$ BEGIN CREATE TYPE final_status AS ENUM ('pending', 'final_accepted', 'final_rejected'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'registration_id' => ['type' => 'INTEGER'],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'affiliation' => ['type' => 'VARCHAR', 'constraint' => 255],
            'category_id' => ['type' => 'INTEGER'],
            'title' => ['type' => 'VARCHAR', 'constraint' => 500],
            'abstract_text' => ['type' => 'TEXT'],
            'keywords' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'submission_version' => ['type' => 'INTEGER', 'default' => 1],
            'review_status' => ['type' => 'review_status', 'default' => 'pending'],
            'final_status' => ['type' => 'final_status', 'default' => 'pending'],
            'can_resubmit' => ['type' => 'BOOLEAN', 'default' => true],
            'assigned_reviewer_id' => ['type' => 'INTEGER', 'null' => true],
            'submitted_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'abstract_categories', 'id');
        $this->forge->addForeignKey('assigned_reviewer_id', 'users', 'id');
        $this->forge->createTable('abstracts');
        
        $this->db->query("CREATE TRIGGER update_abstracts_updated_at BEFORE INSERT OR UPDATE ON abstracts FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_abstracts_registration ON abstracts(registration_id)");
        $this->db->query("CREATE INDEX idx_abstracts_category ON abstracts(category_id)");
        $this->db->query("CREATE INDEX idx_abstracts_reviewer ON abstracts(assigned_reviewer_id)");
    }

    public function down()
    {
        $this->forge->dropTable('abstracts');
        $this->db->query("DROP TYPE IF EXISTS review_status CASCADE");
        $this->db->query("DROP TYPE IF EXISTS final_status CASCADE");
    }
}