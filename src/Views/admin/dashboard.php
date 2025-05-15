<?php
use LorPHP\Core\RoleMiddleware;
use LorPHP\Core\View;

/** @var View $this */
$this->setLayout('base');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-[#fafafa] mb-8">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <?php if (RoleMiddleware::hasPermission('view_organizations')): ?>
        <!-- Organizations Stats -->
        <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] overflow-hidden rounded-xl shadow-xl relative group">
            <!-- Gradient background effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 transition-opacity duration-300 group-hover:opacity-70"></div>
            
            <div class="p-6 relative">
                <h3 class="text-lg font-medium text-[#fafafa]">Organizations</h3>
                <div class="mt-2 text-3xl font-semibold text-[#fafafa] group-hover:text-white transition-colors">
                    <?php echo count($organizations); ?>
                </div>
                <div class="mt-4">
                    <a href="/admin/organizations" class="text-indigo-400 hover:text-indigo-300 transition-colors inline-flex items-center">
                        <?php echo RoleMiddleware::hasPermission('manage_organizations') ? 'Manage organizations' : 'View all organizations'; ?>
                        <svg class="w-4 h-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (RoleMiddleware::hasPermission('view_users')): ?>
        <!-- Users Stats -->
        <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] overflow-hidden rounded-xl shadow-xl relative group">
            <!-- Gradient background effect -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50 transition-opacity duration-300 group-hover:opacity-70"></div>
            
            <div class="p-6 relative">
                <h3 class="text-lg font-medium text-[#fafafa]">Users</h3>
                <div class="mt-2 text-3xl font-semibold text-[#fafafa] group-hover:text-white transition-colors">
                    <?php echo count($users); ?>
                </div>
                <div class="mt-4">
                    <a href="/admin/users" class="text-indigo-400 hover:text-indigo-300 transition-colors inline-flex items-center">
                        <?php echo RoleMiddleware::hasPermission('manage_users') ? 'Manage users' : 'View all users'; ?>
                        <svg class="w-4 h-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="bg-[#18181b]/80 backdrop-blur-xl border border-[#27272a] overflow-hidden rounded-xl shadow-xl relative">
        <!-- Gradient background effect -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-50"></div>
        
        <div class="px-4 py-5 sm:p-6 relative">
            <h3 class="text-lg font-medium text-[#fafafa] mb-4">Recenty Activity</h3>
            <div class="flow-root">
                <ul class="-mb-8">
                    <?php foreach(array_slice($users, 0, 5) as $user): ?>
                        <li class="mb-4">
                            <div class="relative pb-8">
                                <div class="relative flex items-center space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 flex items-center justify-center shadow-md">
                                            <span class="text-sm font-medium leading-none text-white">
                                                <?php echo substr($user->name, 0, 1); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm text-[#a1a1aa]">
                                                <span class="font-medium text-[#fafafa]"><?php echo htmlspecialchars($user->name); ?></span>
                                                joined organization
                                                <span class="font-medium text-[#fafafa]"><?php 
                                                    $org = $user->getOrganization();
                                                    echo htmlspecialchars($org ? $org->name : 'Unknown');
                                                ?></span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-[#71717a]">
                                                Status: <span class="<?php echo $user->active ? 'text-green-400' : 'text-red-400'; ?>"><?php echo $user->active ? 'Active' : 'Inactive'; ?></span>
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
