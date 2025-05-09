<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;

class CreateRateLimitsTable extends Migration {
    public function up() {
        $this->createTable('rate_limits', [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'key' => 'VARCHAR(255) NOT NULL',
            'attempts' => 'INTEGER NOT NULL DEFAULT 0',
            'expires_at' => 'INTEGER NOT NULL',
            'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);
        
        // Add index for faster lookups
        $this->addIndex('rate_limits', 'key');
        $this->addIndex('rate_limits', 'expires_at');
    }

    public function down() {
        $this->dropTable('rate_limits');
    }
}
