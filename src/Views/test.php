<?php
/**
 * Test page to verify view rendering
 */
$this->setLayout('base');
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="p-8 bg-white rounded-lg shadow-md max-w-md w-full">
        <h1 class="text-3xl font-bold text-center mb-6">View Test Page</h1>
        <p class="text-gray-600 text-center">If you can see this message, the view system is working correctly.</p>
        <div class="mt-6 text-center">
            <a href="/register" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Go to Registration
            </a>
        </div>
    </div>
</div>