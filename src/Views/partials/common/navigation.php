<?php
/**
 * Common navigation partial
 */
$user = \LorPHP\Core\Application::getInstance()->getState('user');
?>
<nav class="bg-white shadow-lg mb-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-indigo-600">LorPHP</a>
            </div>
            <div class="flex space-x-4">
                <?php if ($user): ?>
                    <a href="/dashboard" class="text-gray-600 hover:text-indigo-600 px-3 py-2">Dashboard</a>
                    <form action="/logout" method="POST" class="inline">
                        <button type="submit" class="text-gray-600 hover:text-indigo-600 px-3 py-2">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/login" class="text-gray-600 hover:text-indigo-600 px-3 py-2">Login</a>
                    <a href="/register" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
