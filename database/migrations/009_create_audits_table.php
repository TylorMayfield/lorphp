<?php

use LorPHP\Core\Migration;

class CreateAuditsTable extends Migration {
    public function up() {
        $this->createTable('audits', [
            'id' => 'TEXT PRIMARY KEY',
            'user_id' => 'TEXT',
            'auditable_type' => 'TEXT NOT NULL',
            'auditable_id' => 'TEXT NOT NULL',
            'event' => 'TEXT NOT NULL',
            'old_values' => 'TEXT',
            'new_values' => 'TEXT',
            'organization_id' => 'TEXT',
            'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'FOREIGN KEY (user_id) REFERENCES users(id)',
            'FOREIGN KEY (organization_id) REFERENCES organizations(id)'
        ]);

        // Add indexes for better query performance
        $this->createIndex('audits', 'auditable_type');
        $this->createIndex('audits', 'auditable_id');
        $this->createIndex('audits', 'organization_id');
    }

    public function down() {
        $this->dropTable('audits');
    }
}
