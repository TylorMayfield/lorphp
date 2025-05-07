<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class AddRoleToUsers extends Migration {
    public function up() {
        $this->alterTable('users', function(Schema $table) {
            $table->string('role_id')->nullable();
        });
    }
    
    public function down() {
        $this->alterTable('users', function(Schema $table) {
            $table->dropColumn('role_id');
        });
    }
}
