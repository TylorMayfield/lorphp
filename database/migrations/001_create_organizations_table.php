<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateOrganizationsTable extends Migration {
    public function up() {
        $this->createTable('organizations', function(Schema $table) {
            $table->uuid();
            $table->string('name');
            $table->timestamps();
        });
    }
    
    public function down() {
        $this->dropTable('organizations');
    }
}
