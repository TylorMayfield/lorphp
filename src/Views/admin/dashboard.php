<?php
use LorPHP\Core\RoleMiddleware;
use LorPHP\Core\View;

/** @var View $this */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <?php if (RoleMiddleware::hasPermission('view_organizations')): ?>
        <!-- Organizations Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Organizations</h3>
                <div class="mt-2 text-3xl font-semibold text-gray-900">
                    <?php echo count($organizations); ?>
                </div>
                <div class="mt-4">
                    <a href="/admin/organizations" class="text-indigo-600 hover:text-indigo-900">
                        <?php echo RoleMiddleware::hasPermission('manage_organizations') ? 'Manage organizations' : 'View all organizations'; ?> →
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (RoleMiddleware::hasPermission('view_users')): ?>
        <!-- Users Stats -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900">Users</h3>
                <div class="mt-2 text-3xl font-semibold text-gray-900">
                    <?php echo count($users); ?>
                </div>
                <div class="mt-4">
                    <a href="/admin/users" class="text-indigo-600 hover:text-indigo-900">
                        <?php echo RoleMiddleware::hasPermission('manage_users') ? 'Manage users' : 'View all users'; ?> →
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    <?php foreach(array_slice($users, 0, 5) as $user): ?>
                        <li class="mb-4">
                            <div class="relative pb-8">
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center">
                                            <span class="text-sm font-medium leading-none text-white">
                                                <?php echo substr($user->name, 0, 1); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm text-gray-500">
                                                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($user->name); ?></span>
                                                joined organization
                                                <span class="font-medium text-gray-900"><?php 
                                                    $org = $user->getOrganization();
                                                    echo htmlspecialchars($org ? $org->name : 'Unknown');
                                                ?></span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                Status: <?php echo $user->active ? 'Active' : 'Inactive'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
