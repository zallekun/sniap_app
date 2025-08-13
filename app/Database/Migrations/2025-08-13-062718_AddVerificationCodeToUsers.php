<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerificationCodeToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => 6,
                'null' => true,
                'after' => 'verification_token'
            ],
            'verification_code_expires' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'verification_code'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['verification_code', 'verification_code_expires']);
    }
}
