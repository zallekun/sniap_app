<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyLoasToLoaDocuments extends Migration
{
    public function up()
    {
        // First, backup existing data if any
        $this->db->query("CREATE TABLE loas_backup AS SELECT * FROM loas");

        // Drop the existing loas table
        $this->forge->dropTable('loas');
        
        // Drop the old upload_type enum
        $this->db->query("DROP TYPE IF EXISTS upload_type CASCADE");

        // Create new ENUM type for status
        $this->db->query("DO $$ BEGIN CREATE TYPE loa_status_type AS ENUM ('active', 'revoked'); EXCEPTION WHEN duplicate_object THEN null; END $$;");
        
        // Create new loa_documents table with complete structure
        $this->forge->addField([
            'id' => [
                'type' => 'SERIAL'
            ],
            'abstract_id' => [
                'type' => 'INTEGER'
            ],
            'loa_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'presenter_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'presenter_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'event_title' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'event_date' => [
                'type' => 'DATE'
            ],
            'abstract_title' => [
                'type' => 'TEXT'
            ],
            'pdf_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true
            ],
            'html_content' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'generated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'generated_by' => [
                'type' => 'INTEGER'
            ],
            'revoked_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'revoked_by' => [
                'type' => 'INTEGER',
                'null' => true
            ],
            'revoke_reason' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'reactivated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'reactivated_by' => [
                'type' => 'INTEGER',
                'null' => true
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ]
        ]);

        // Add primary key
        $this->forge->addPrimaryKey('id');
        
        // Add foreign keys
        $this->forge->addForeignKey('abstract_id', 'abstracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('generated_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('revoked_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('reactivated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        
        // Create the table
        $this->forge->createTable('loa_documents');

        // Add status column with ENUM type after table creation
        $this->db->query("ALTER TABLE loa_documents ADD COLUMN status loa_status_type DEFAULT 'active'");

        // Add indexes for performance
        $this->db->query('CREATE INDEX idx_loa_documents_abstract_id ON loa_documents(abstract_id)');
        $this->db->query('CREATE INDEX idx_loa_documents_loa_number ON loa_documents(loa_number)');
        $this->db->query('CREATE INDEX idx_loa_documents_presenter_email ON loa_documents(presenter_email)');
        $this->db->query('CREATE INDEX idx_loa_documents_status ON loa_documents(status)');
        $this->db->query('CREATE INDEX idx_loa_documents_generated_at ON loa_documents(generated_at)');

        // Set default timestamps
        $this->db->query("ALTER TABLE loa_documents ALTER COLUMN generated_at SET DEFAULT NOW()");
        $this->db->query("ALTER TABLE loa_documents ALTER COLUMN created_at SET DEFAULT NOW()");
        $this->db->query("ALTER TABLE loa_documents ALTER COLUMN updated_at SET DEFAULT NOW()");

        // Create trigger function for updated_at
        $this->db->query("
            CREATE OR REPLACE FUNCTION update_loa_documents_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        // Create trigger
        $this->db->query("
            CREATE TRIGGER update_loa_documents_updated_at 
            BEFORE UPDATE ON loa_documents 
            FOR EACH ROW EXECUTE FUNCTION update_loa_documents_updated_at();
        ");

        // Add LOA tracking fields to abstracts table
        $this->forge->addColumn('abstracts', [
            'loa_generated' => [
                'type' => 'BOOLEAN',
                'default' => false
            ],
            'loa_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ]
        ]);

        // Clean up backup table (optional - remove this line if you want to keep backup)
        $this->db->query("DROP TABLE IF EXISTS loas_backup");
    }

    public function down()
    {
        // Drop loa_documents table
        $this->forge->dropTable('loa_documents');
        
        // Drop custom ENUM type
        $this->db->query("DROP TYPE IF EXISTS loa_status_type CASCADE");
        
        // Drop functions
        $this->db->query("DROP FUNCTION IF EXISTS update_loa_documents_updated_at() CASCADE");
        
        // Remove LOA columns from abstracts table
        $this->forge->dropColumn('abstracts', ['loa_generated', 'loa_number']);
        
        // Recreate original loas table
        $this->db->query("DO $$ BEGIN CREATE TYPE upload_type AS ENUM ('admin', 'reviewer'); EXCEPTION WHEN duplicate_object THEN null; END $$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'registration_id' => ['type' => 'INTEGER'],
            'loa_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'upload_type' => ['type' => 'upload_type'],
            'generated_by' => ['type' => 'INTEGER'],
            'generated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('registration_id', 'registrations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('generated_by', 'users', 'id');
        $this->forge->createTable('loas');
        
        // Add trigger for original table
        $this->db->query("CREATE TRIGGER update_loas_generated_at BEFORE INSERT ON loas FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
    }
}