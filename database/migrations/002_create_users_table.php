<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateUsersTable extends Migration {
    public function up() {
        $this->createTable('users', function(Schema $table) {
            $table->uuid();
            $table->string('organization_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamp('created_at', 'CURRENT_TIMESTAMP');
            $table->foreignKey('organization_id', 'organizations(id)', 'CASCADE');
            $table->unique('email');
        });
    }
    
    public function down() {
        $this->dropTable('users');
    }
}
