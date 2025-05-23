<?php

namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class AddDefaultIsActiveToOrganizations extends Migration
{
    public function up()
    {
        $this->alterTable('organizations', function(Schema $table) {
            $table->boolean('isActive')->default(true);
        });
    }

    public function down()
    {
        $this->alterTable('organizations', function(Schema $table) {
            $table->boolean('isActive');
        });
    }
}
