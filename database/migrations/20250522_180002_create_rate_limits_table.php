<?php

namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class CreateRateLimitsTable extends Migration
{
    public function up()
    {
        $this->createTable('rate_limits', function(Schema $table) {
            $table->uuid();
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->string('ipAddress');
            $table->string('endpoint');
            $table->integer('requests')->default(1);
            $table->timestamp('windowStart');
            $table->integer('windowDuration')->default(3600); // Default 1 hour in seconds
            $table->integer('maxRequests')->default(1000);
            $table->string('userId')->nullable();
            $table->boolean('blocked')->default(false);
            
            // Unique constraint on IP + endpoint combination
            $table->unique(['ipAddress', 'endpoint']);
            // Index on windowStart for cleanup
            $table->index('windowStart');
        });
    }

    public function down()
    {
        $this->dropTable('rate_limits');
    }
}
