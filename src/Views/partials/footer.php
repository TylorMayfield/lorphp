<?php
use LorPHP\Core\RoleMiddleware;
?>
<footer class="bg-[#18181b] border-t border-[#27272a] mt-auto">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo and description -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <img src="/images/logo.svg" alt="LorPHP" class="h-8 w-8">
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 bg-clip-text text-transparent">LorPHP</span>
                </div>
                <p class="text-[#a1a1aa]">A modern PHP framework for building powerful web applications. Built with simplicity and developer experience in mind.</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="/" class="text-[#a1a1aa] hover:text-white transition-colors">Home</a></li>
                    <?php if (RoleMiddleware::isAuthenticated()): ?>
                        <li><a href="/dashboard" class="text-[#a1a1aa] hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="/clients" class="text-[#a1a1aa] hover:text-white transition-colors">Clients</a></li>
                    <?php else: ?>
                        <li><a href="/login" class="text-[#a1a1aa] hover:text-white transition-colors">Login</a></li>
                        <li><a href="/register" class="text-[#a1a1aa] hover:text-white transition-colors">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h3 class="text-white font-semibold mb-4">Resources</h3>
                <ul class="space-y-2">
                    <li><a href="https://github.com/yourusername/lorphp" target="_blank" rel="noopener noreferrer" class="text-[#a1a1aa] hover:text-white transition-colors">GitHub</a></li>
                    <li><a href="https://github.com/yourusername/lorphp/wiki" target="_blank" rel="noopener noreferrer" class="text-[#a1a1aa] hover:text-white transition-colors">Documentation</a></li>
                    <li><a href="https://github.com/yourusername/lorphp/discussions" target="_blank" rel="noopener noreferrer" class="text-[#a1a1aa] hover:text-white transition-colors">Community</a></li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 pt-8 border-t border-[#27272a]">
            <p class="text-center text-[#a1a1aa]">&copy; <?php echo date('Y'); ?> LorPHP Framework. All rights reserved.</p>
        </div>
    </div>
</footer>
