<?php
/**
 * Common navigation partial
 */
$user = \LorPHP\Core\Application::getInstance()->getState('user');
?>
<div id="nav" class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-white/20 shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">LorPHP</span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($user): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 rounded-full p-2 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512">
                                    <path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"></path>
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{ 'rotate-180': open }">
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
                             class="absolute right-0 mt-2 w-64 py-2 bg-white rounded-lg shadow-xl border border-gray-100 z-50">
                            
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user->email); ?></p>
                            </div>
                            
                            <a href="/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            
                            <div class="border-t border-gray-100">
                                <form action="/logout" method="POST">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-full transition-all duration-200 hover:bg-indigo-50">Log in</a>
                    <a href="/register" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                        Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
