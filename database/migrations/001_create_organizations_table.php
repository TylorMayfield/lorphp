<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateOrganizationsTable extends Migration {
    public function up() {
        $this->createTable('organizations', function(Schema $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('created_at', 'CURRENT_TIMESTAMP');
        });
    }
    
    public function down() {
        $this->dropTable('organizations');
    }
}
