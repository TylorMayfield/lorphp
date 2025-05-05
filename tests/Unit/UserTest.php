<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../php-error.log');

echo "Starting test script...\n";
var_dump("Test output");  // Debug output

// Include the necessary files directly
require_once __DIR__ . '/../../src/Core/Bootstrap.php';

echo "Test script started\n";

try {
    // Create test organization
    $organization = new \LorPHP\Models\Organization();
    $organization->name = "Test Organization";
    if (!$organization->save()) {
        throw new Exception("Failed to save organization");
    }
    echo "Created test organization\n";

    // Create test user
    $user = new \LorPHP\Models\User();
    $user->name = "Test User";
    $user->email = "test" . time() . "@example.com";
    $user->setPassword("password123");
    $user->organization_id = $organization->id;
    if (!$user->save()) {
        throw new Exception("Failed to save user");
    }
    echo "Created test user\n";

    // Test organization relationship
    $userOrg = $user->getOrganization();
    if (!$userOrg) {
        throw new Exception("Failed to get organization from user");
    }
    echo "Successfully retrieved organization through relationship\n";
    echo "Organization name: " . $userOrg->name . "\n";

    // Clean up
    $db = \LorPHP\Core\Database::getInstance();
    $db->delete('users', ['id' => $user->id]);
    $db->delete('organizations', ['id' => $organization->id]);
    echo "Test cleanup completed\n";

    echo "\nAll tests passed successfully!\n";
} catch (Exception $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}

use LorPHP\Models\User;
use LorPHP\Models\Organization;
use LorPHP\Core\Database;

class UserTest {
    private static $db;
    private $testUser;
    private $testOrg;

    public function setUp(): void {
        self::$db = Database::getInstance();
        
        // Create test organization
        $this->testOrg = new Organization();
        $this->testOrg->name = "Test Organization";
        $this->testOrg->save();
        
        // Create test user
        $this->testUser = new User();
        $this->testUser->name = "Test User";
        $this->testUser->email = "test@example.com";
        $this->testUser->setPassword("password123");
        $this->testUser->organization_id = $this->testOrg->id;
        $this->testUser->save();
    }

    public function testOrganizationRelationship(): void {
        // Test getting organization
        $organization = $this->testUser->getOrganization();
        assert($organization !== null, "Organization should not be null");
        assert($organization->id === $this->testOrg->id, "Organization ID should match");
        assert($organization->name === "Test Organization", "Organization name should match");
        
        // Test getting organization clients
        $clients = $this->testUser->getOrganizationClients();
        assert(is_array($clients), "getOrganizationClients should return an array");
        
        echo "Organization relationship tests passed!\n";
    }

    public function tearDown(): void {
        // Clean up test data
        if ($this->testUser && $this->testUser->id) {
            self::$db->delete('users', ['id' => $this->testUser->id]);
        }
        if ($this->testOrg && $this->testOrg->id) {
            self::$db->delete('organizations', ['id' => $this->testOrg->id]);
        }
    }

    public function runTests(): void {
        try {
            echo "Starting User Model tests...\n";
            $this->setUp();
            $this->testOrganizationRelationship();
            $this->tearDown();
            echo "All tests completed successfully!\n";
        } catch (\Exception $e) {
            echo "Test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run the tests
$test = new UserTest();
$test->runTests();
