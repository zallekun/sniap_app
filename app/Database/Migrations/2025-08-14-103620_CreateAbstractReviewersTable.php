<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbstractReviewersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'abstract_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'reviewer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'assigned_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('abstract_id');
        $this->forge->addKey('reviewer_id');
        
        // Add foreign key constraints
        $this->forge->addForeignKey('abstract_id', 'abstracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        // Add unique constraint to prevent duplicate assignments
        $this->forge->addUniqueKey(['abstract_id', 'reviewer_id']);
        
        $this->forge->createTable('abstract_reviewers');
    }

    public function down()
    {
        $this->forge->dropTable('abstract_reviewers');
    }
}
