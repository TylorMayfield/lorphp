<?php
// Migration command helpers for lorphp CLI

function handleMigrateCommand($command, $name) {
    if ($command === 'status') {
        echo "Migration Status:\n";
        $db = \LorPHP\Core\Database::getInstance();
        $applied = $db->query("SELECT migration, batch FROM migrations ORDER BY id")->fetchAll(\PDO::FETCH_ASSOC);
        $migrations = glob(__DIR__ . '/../database/migrations/*.php');
        $appliedNames = array_column($applied, 'migration');
        echo "Applied migrations:\n";
        foreach ($applied as $row) {
            echo "  [Batch {$row['batch']}] {$row['migration']}\n";
        }
        echo "\nPending migrations:\n";
        foreach ($migrations as $file) {
            $name = basename($file);
            if (!in_array($name, $appliedNames)) {
                echo "  $name\n";
            }
        }
        exit(0);
    }
    switch ($command) {
        case 'run':
            echo "Running migrations...\n";
            $pending = getPendingMigrations();
            if (empty($pending)) {
                echo "Nothing to migrate.\n";
                exit(0);
            }
            foreach ($pending as $file) {
                runMigration($file);
            }
            break;
        case 'rollback':
            echo "Rolling back last batch...\n";
            $db = \LorPHP\Core\Database::getInstance();
            $lastBatch = $db->query("SELECT MAX(batch) as batch FROM migrations")->fetch(\PDO::FETCH_ASSOC)['batch'];
            if (!$lastBatch) {
                echo "Nothing to rollback.\n";
                exit(0);
            }
            $migrations = $db->query(
                "SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC",
                [$lastBatch]
            )->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($migrations as $migration) {
                $file = __DIR__ . '/../database/migrations/' . $migration;
                if (file_exists($file)) {
                    rollbackMigration($file);
                }
            }
            break;
        case 'create':
            if (!$name) {
                echo "Error: Migration name required\n";
                echo "Usage: ./lor migrate:create <name>\n";
                exit(1);
            }
            $template = <<<PHP
<?php
namespace LorPHP\Database\Migrations;

use LorPHP\Core\Migration;
use LorPHP\Core\Schema;

class %s extends Migration {
    public function up() {
        // Your migration code here
    }
    public function down() {
        // Rollback code here
    }
}
PHP;
            $className = str_replace(' ', '_', ucwords(str_replace('_', ' ', $name)));
            $migrationPattern = __DIR__ . '/../database/migrations/*_' . $name . '.php';
            $existing = glob($migrationPattern);
            if (!empty($existing)) {
                echo "Migration for '{$name}' already exists. Skipping.\n";
                break;
            }
            $migrations = glob(__DIR__ . '/../database/migrations/*.php');
            $nextNum = sprintf('%03d', count($migrations) + 1);
            $filename = __DIR__ . "/../database/migrations/{$nextNum}_{$name}.php";
            file_put_contents($filename, sprintf($template, $className));
            echo "Created migration: " . basename($filename) . "\n";
            break;
        default:
            echo "Error: Unknown command '{$command}'\n";
            showHelp();
            exit(1);
    }
}
