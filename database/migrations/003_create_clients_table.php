<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateClientsTable extends Migration {
    public function up() {
        $this->createTable('clients', function(Schema $table) {
            $table->id();
            $table->integer('organization_id');
            $table->string('name');
            $table->string('email', true);
            $table->string('phone', true);
            $table->string('status')->default('active');
            $table->string('notes', true);
            $table->timestamp('last_contact_date', true);
            $table->timestamp('created_at', 'CURRENT_TIMESTAMP');
            $table->foreignKey('organization_id', 'organizations(id)', 'CASCADE');
        });
    }
    
    public function down() {
        $this->dropTable('clients');
    }
}
