<?php
// bin/seed.php

// Manually require all dependencies in correct order
require_once __DIR__ . '/../src/Core/Traits/HasUuid.php';
require_once __DIR__ . '/../src/Core/Model.php';
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Interfaces/RoleInterface.php';
require_once __DIR__ . '/../src/Interfaces/PermissionInterface.php';
require_once __DIR__ . '/../src/Interfaces/UserInterface.php';
require_once __DIR__ . '/../src/Models/Permission.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Role.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../database/seeders/RoleSeeder.php';


LorPHP\Database\Seeders\RoleSeeder::run();
echo "Seeders executed.\n";
