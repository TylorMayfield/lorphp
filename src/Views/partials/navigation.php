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
                    <a href="/dashboard" class="text-[#a1a1aa] hover:text-white transition-colors">Dashboard</a>
                    <a href="/clients" class="text-[#a1a1aa] hover:text-white transition-colors">Clients</a>
                    <?php if (RoleMiddleware::hasRole('admin')): ?>
                        <a href="/admin" class="text-[#a1a1aa] hover:text-white transition-colors">Admin</a>
                    <?php endif; ?>
                    <form action="/logout" method="POST" class="inline">
                        <button type="submit" class="text-[#a1a1aa] hover:text-white transition-colors">Logout</button>
                    </form>
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
                <a href="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Dashboard</a>
                <a href="/clients" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Clients</a>
                <?php if (RoleMiddleware::hasRole('admin')): ?>
                    <a href="/admin" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Admin</a>
                <?php endif; ?>
                <form action="/logout" method="POST">
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Login</a>
                <a href="/register" class="block px-3 py-2 rounded-md text-base font-medium text-[#a1a1aa] hover:text-white hover:bg-[#27272a]">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
