<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;

class CreateRateLimitsTable extends Migration {
    public function up() {
        $this->createTable('rate_limits', function($table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->string('key', 255);
            $table->integer('attempts')->default(0);
            $table->integer('expires_at');
            $table->datetime('created_at')->default('CURRENT_TIMESTAMP');
            $table->index('key');
            $table->index('expires_at');
        });
    }

    public function down() {
        $this->dropTable('rate_limits');
    }
}
