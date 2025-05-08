<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreatePackagesTables extends Migration {
    public function up() {
        // Create packages table with UUIDs
        $this->createTable('packages', function(Schema $table) {
            $table->uuid();
            $table->string('organization_id');
            $table->string('name');
            $table->string('description', true); // nullable
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->foreignKey('organization_id', 'organizations(id)', 'CASCADE');
        });

        // Create client_packages pivot table
        $this->createTable('client_packages', function(Schema $table) {
            $table->string('client_id');
            $table->string('package_id');
            $table->timestamp('assigned_at', 'CURRENT_TIMESTAMP');
            $table->foreignKey('client_id', 'clients(id)', 'CASCADE');
            $table->foreignKey('package_id', 'packages(id)', 'CASCADE');
        });
    }
    
    public function down() {
        $this->dropTable('client_packages');
        $this->dropTable('packages');
    }
}
