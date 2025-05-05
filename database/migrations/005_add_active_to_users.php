<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class AddActiveToUsers extends Migration {
    public function up() {
        $this->alterTable('users', function(Schema $table) {
            $table->integer('active')->nullable()->default(1);
        });
    }
    
    public function down() {
        $this->alterTable('users', function(Schema $table) {
            $table->dropColumn('active');
        });
    }
}
