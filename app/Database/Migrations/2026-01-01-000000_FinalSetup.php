<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FinalSetup extends Migration
{
    public function up()
    {
        // Hapus tabel lama jika ada (Biar bersih)
        $this->forge->dropTable('collaborators', true);
        $this->forge->dropTable('documents', true);
        $this->forge->dropTable('users', true);

        // 1. Tabel Users
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // 2. Tabel Documents
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'owner_id' => ['type' => 'INT', 'unsigned' => true],
            'judul' => ['type' => 'VARCHAR', 'constraint' => 255],
            'isi' => ['type' => 'LONGTEXT', 'null' => true],
            'is_public' => ['type' => 'TINYINT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('owner_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documents');

        // 3. Tabel Collaborators
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'document_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('collaborators');
    }

    public function down()
    {
        $this->forge->dropTable('collaborators');
        $this->forge->dropTable('documents');
        $this->forge->dropTable('users');
    }
}
