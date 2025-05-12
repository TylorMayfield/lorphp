<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;

class CreateAuditsTable extends Migration {
    public function up() {
        $this->createTable('audits', function($table) {
            $table->text('id')->primary();
            $table->text('user_id')->nullable();
            $table->text('auditable_type');
            $table->text('auditable_id');
            $table->text('event');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('organization_id')->nullable();
            $table->datetime('created_at')->default('CURRENT_TIMESTAMP');
            $table->foreignKey('user_id')->references('users', 'id');
            $table->foreignKey('organization_id')->references('organizations', 'id');
            $table->index('auditable_id');
            $table->index('organization_id');
        });
    }

    public function down() {
        $this->dropTable('audits');
    }
}
