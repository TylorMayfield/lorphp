<?php
/**
 * Offline page view
 */

use LorPHP\Core\View;

/** @var View $__view */
$__view->setLayout('base');
?>

<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    You're Offline
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    It looks like your device isn't connected to the internet. Please check your connection and try again.
                </p>
                <button onclick="checkConnectivityAndRedirect()" class="mt-4 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Try Again
                </button>
                
                <script>
                    // Check if we're offline when the page loads
                    if (navigator.onLine) {
                        window.location.href = '/dashboard';
                    }

                    function checkConnectivityAndRedirect() {
                        const button = document.querySelector('button');
                        button.disabled = true;
                        button.innerHTML = 'Checking connection...';

                        // Try to fetch a small resource to verify connectivity
                        fetch('/manifest.json', { cache: 'no-store' })
                            .then(response => {
                                if (response.ok) {
                                    window.location.href = '/dashboard';
                                } else {
                                    button.disabled = false;
                                    button.innerHTML = 'Try Again';
                                }
                            })
                            .catch(() => {
                                button.disabled = false;
                                button.innerHTML = 'Try Again';
                            });
                    }

                    // Listen for online/offline events
                    window.addEventListener('online', () => {
                        window.location.href = '/dashboard';
                    });
                </script>
            </div>
        </div>
    </div>
</div>
