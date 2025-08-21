<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRevisionFieldsToAbstracts extends Migration
{
    public function up()
    {
        // Add revision-related fields
        $fields = [
            'revision_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reviewer notes for revision'
            ],
            'revision_deadline' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Deadline for revision submission'
            ],
            'can_upload_again' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'comment' => 'Whether presenter can upload revised version'
            ],
            'revision_count' => [
                'type' => 'INTEGER',
                'default' => 0,
                'comment' => 'Number of revisions submitted'
            ],
            'max_revisions' => [
                'type' => 'INTEGER',
                'default' => 2,
                'comment' => 'Maximum allowed revisions'
            ],
            'revised_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'Last revision submission date'
            ],
            'reviewer_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Internal reviewer notes'
            ]
        ];

        $this->forge->addColumn('abstracts', $fields);

        // Add index for revision queries
        $this->db->query("CREATE INDEX idx_abstracts_revision_status ON abstracts(review_status, revision_deadline)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX IF EXISTS idx_abstracts_revision_status");
        
        $this->forge->dropColumn('abstracts', [
            'revision_notes',
            'revision_deadline', 
            'can_upload_again',
            'revision_count',
            'max_revisions',
            'revised_at',
            'reviewer_notes'
        ]);
    }
}
