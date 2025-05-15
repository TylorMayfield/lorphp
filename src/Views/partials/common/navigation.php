<?php
/**
 * Common navigation partial
 */
$user = \LorPHP\Core\Application::getInstance()->getState('user');
?>
<div id="nav" class="fixed top-0 left-0 right-0 z-50 bg-[#0f0f11]/90 backdrop-blur-xl border-b border-[#27272a] shadow-lg transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center group">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 bg-clip-text text-transparent animate-gradient-x relative">
                        LorPHP
                        <span class="absolute inset-0 bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 opacity-20 blur-sm transition-opacity duration-300 group-hover:opacity-40"></span>
                    </span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($user): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 rounded-full p-2 hover:bg-[#23232a] transition-colors duration-200">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-[#6366f1]/20 text-[#6366f1]">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"></path>
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-[#a1a1aa]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{ 'rotate-180': open }">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Dropdown menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 mt-2 w-64 py-2 bg-[#18181b] rounded-lg shadow-xl border border-[#27272a] z-50">
                            <div class="px-4 py-2 border-b border-[#23232a]">
                                <p class="text-sm font-medium text-[#fafafa] "><?php echo htmlspecialchars($user->name); ?></p>
                                <p class="text-sm text-[#a1a1aa] "><?php echo htmlspecialchars($user->email); ?></p>
                            </div>
                            <a href="/dashboard" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:bg-[#18181b]">Dashboard</a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-[#a1a1aa] hover:bg-[#18181b]">Settings</a>
                            <div class="border-t border-[#23232a]">
                                <form action="/logout" method="POST">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#a1a1aa] hover:bg-[#18181b]">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="text-[#a1a1aa] px-4 py-2 rounded-full transition-all duration-300 hover:bg-[#27272a] hover:text-[#fafafa] hover:scale-105">Log in</a>
                    <a href="/register" class="group relative inline-flex items-center px-6 py-2 rounded-full overflow-hidden bg-gradient-to-r from-indigo-500 via-purple-500 to-blue-500 text-white font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                        <span class="relative z-10">Get Started</span>
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-all duration-500 -rotate-180 group-hover:rotate-0"></div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
