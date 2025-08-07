<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE review_decision AS ENUM ('accepted', 'accepted_with_revision', 'rejected'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'abstract_id' => ['type' => 'INTEGER'],
            'reviewer_id' => ['type' => 'INTEGER'],
            'review_status' => ['type' => 'review_decision'],
            'comments' => ['type' => 'TEXT', 'null' => true],
            'revision_notes' => ['type' => 'TEXT', 'null' => true],
            'loa_file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reviewed_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('abstract_id', 'abstracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reviews');
        
        $this->db->query("CREATE UNIQUE INDEX unique_abstract_review ON reviews(abstract_id)");
        $this->db->query("CREATE TRIGGER update_reviews_reviewed_at BEFORE INSERT ON reviews FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_reviews_abstract ON reviews(abstract_id)");
    }

    public function down()
    {
        $this->forge->dropTable('reviews');
        $this->db->query("DROP TYPE IF EXISTS review_decision CASCADE");
    }
}