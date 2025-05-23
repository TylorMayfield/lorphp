<?php

namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class FixOrganizationIsActive extends Migration
{
    public function up()
    {
        $this->alterTable('organizations', function(Schema $table) {
            // Drop the existing column and recreate it with proper constraints
            $table->dropColumn('isActive');
            $table->boolean('isActive')->default(true)->notNull();
        });
    }

    public function down()
    {
        $this->alterTable('organizations', function(Schema $table) {
            $table->dropColumn('isActive');
            $table->boolean('isActive');
        });
    }
}
