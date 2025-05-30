<?php
use LorPHP\Core\RoleMiddleware;
?>
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#18181b]/80 backdrop-blur-sm border-b border-[#27272a]">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center space-x-2">
                    <img src="/images/logo.svg" alt="LorPHP" class="h-8 w-8">
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 bg-clip-text text-transparent">LorPHP</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex md:items-center md:space-x-6">
                <?php if (RoleMiddleware::isAuthenticated()): ?>
                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" type="button" class="flex items-center space-x-3 rounded-md p-2 hover:bg-[#23232a] transition-colors duration-200">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 text-white">
                                <?php echo strtoupper(substr(RoleMiddleware::getCurrentUser()->name ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-white">
                                    <?php echo htmlspecialchars(RoleMiddleware::getCurrentUser()->name ?? ''); ?>
                                </span>
                                <!-- Dropdown arrow -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-white transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <!-- Dropdown Menu -->
                        <div x-cloak x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-[#18181b] ring-1 ring-[#27272a] z-50">
                            <div class="py-1">
                                <a href="/dashboard" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Dashboard</a>
                                <?php if (RoleMiddleware::hasRole('admin')): ?>
                                    <a href="/admin" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Admin Panel</a>
                                <?php endif; ?>
                                <a href="/clients" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Clients</a>
                                <a href="/settings" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Settings</a>
                                <hr class="my-1 border-[#27272a]">
                                <form action="/logout" method="POST" class="block">
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#ef4444] hover:text-white hover:bg-[#27272a]">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="text-[#a1a1aa] hover:text-white transition-colors">Login</a>
                    <a href="/register" class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-blue-500 text-white font-medium hover:opacity-90 transition-opacity">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-[#a1a1aa] hover:text-white hover:bg-[#27272a] focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-[#18181b] border-t border-[#27272a]">
            <?php if (RoleMiddleware::isAuthenticated()): ?>
                <!-- User info -->
                <div class="px-3 py-2 border-b border-[#27272a] mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 via-purple-400 to-blue-400 flex items-center justify-center text-white">
                                <?php echo strtoupper(substr(RoleMiddleware::getCurrentUser()->name ?? 'U', 0, 1)); ?>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-white truncate">
                                <?php echo htmlspecialchars(RoleMiddleware::getCurrentUser()->name ?? ''); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation items -->
                <a href="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Dashboard</a>
                <a href="/clients" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Clients</a>
                <?php if (RoleMiddleware::hasRole('admin')): ?>
                    <a href="/admin" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Admin</a>
                <?php endif; ?>
                <a href="/settings" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Settings</a>
                <form action="/logout" method="POST" class="border-t border-[#27272a] mt-2">
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-[#ef4444] hover:text-white hover:bg-[#27272a]">
                        Sign Out
                    </button>
                </form>
            <?php else: ?>
                <a href="/login" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Login</a>
                <a href="/register" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
