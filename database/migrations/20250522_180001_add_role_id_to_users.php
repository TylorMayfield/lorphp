<?php

namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class AddRoleIdToUsers extends Migration
{
    public function up()
    {
        $this->alterTable('users', function(Schema $table) {
            $table->string('roleId')->default('1');
        });

        // Convert existing role field to roleId if exists
        $this->db->exec("UPDATE users SET roleId = '1' WHERE roleId IS NULL");
    }

    public function down()
    {
        $this->alterTable('users', function(Schema $table) {
            $table->dropColumn('roleId');
        });
    }
}
