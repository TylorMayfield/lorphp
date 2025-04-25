<?php
/**
 * Debug bar partial
 * Displays debugging information when debug mode is enabled
 */
?>
<div id="debug-bar" class="fixed bottom-0 left-0 right-0 bg-gray-800 text-white p-4 shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex space-x-6">
            <div class="debug-section">
                <span class="font-semibold">PHP Version:</span>
                <span><?php echo PHP_VERSION; ?></span>
            </div>
            <div class="debug-section">
                <span class="font-semibold">Memory Usage:</span>
                <span><?php echo number_format(memory_get_usage() / 1024 / 1024, 2); ?> MB</span>
            </div>
            <div class="debug-section">
                <span class="font-semibold">Request Method:</span>
                <span><?php echo $_SERVER['REQUEST_METHOD']; ?></span>
            </div>
            <div class="debug-section">
                <span class="font-semibold">Request URI:</span>
                <span><?php echo $_SERVER['REQUEST_URI']; ?></span>
            </div>
        </div>
        <button onclick="document.getElementById('debug-bar').style.display='none'" class="text-gray-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
