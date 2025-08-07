<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->db->query("DO \$\$ BEGIN CREATE TYPE user_role AS ENUM ('presenter', 'audience', 'reviewer', 'admin'); EXCEPTION WHEN duplicate_object THEN null; END \$\$;");
        
        $this->forge->addField([
            'id' => ['type' => 'SERIAL'],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'institution' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role' => ['type' => 'user_role'],
            'is_verified' => ['type' => 'BOOLEAN', 'default' => false],
            'verification_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_password_token' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'reset_password_expires' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
        
        $this->db->query("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                NEW.created_at = COALESCE(NEW.created_at, CURRENT_TIMESTAMP);
                RETURN NEW;
            END;
            \$\$ language 'plpgsql'
        ");
        
        $this->db->query("CREATE TRIGGER update_users_updated_at BEFORE INSERT OR UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $this->db->query("CREATE INDEX idx_users_email ON users(email)");
        $this->db->query("CREATE INDEX idx_users_role ON users(role)");
    }

    public function down()
    {
        $this->forge->dropTable('users');
        $this->db->query("DROP TYPE IF EXISTS user_role CASCADE");
        $this->db->query("DROP FUNCTION IF EXISTS update_updated_at_column()");
    }
}