<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateContactsTable extends Migration {
    public function up() {
        $this->createTable('contacts', function(Schema $table) {
            $table->id();
            $table->integer('client_id');
            $table->integer('user_id');
            $table->string('type');
            $table->string('notes', true);
            $table->timestamp('contact_date', 'CURRENT_TIMESTAMP');
            $table->foreignKey('client_id', 'clients(id)', 'CASCADE');
            $table->foreignKey('user_id', 'users(id)', 'CASCADE');
        });
    }
    
    public function down() {
        $this->dropTable('contacts');
    }
}
