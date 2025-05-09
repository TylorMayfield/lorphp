<?php
use LorPHP\Core\RoleMiddleware;
use LorPHP\Core\View;

/** @var View $this */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <meta name="theme-color" content="#4f46e5">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'LorPHP Framework'; ?></title>
    
    <!-- PWA Support -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    
    <!-- Base path for all assets -->
    <base href="/">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom styles -->
    <?php if (method_exists($this, 'renderStyles')): ?>
        <?php $this->renderStyles(); ?>
    <?php endif; ?>
    
    <!-- Scripts -->
    <?php if (method_exists($this, 'renderScripts')): ?>
        <?php $this->renderScripts(); ?>
    <?php endif; ?>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(error => {
                        console.error('ServiceWorker registration failed:', error);
                    });
            });
        }
    </script>
    
    <!-- Toast Notifications -->
    <?php if (method_exists($this, 'ui')): ?>
        <?php echo $this->ui()->toast([
            'flash_error' => $flash_error ?? null,
            'flash_success' => $flash_success ?? null
        ]); ?>
    <?php endif; ?>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php if (method_exists($this, 'renderNavigation')): ?>
        <?php $this->renderNavigation(); ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php if (method_exists($this, 'renderContent')): ?>
            <?php echo $this->renderContent(); ?>
        <?php endif; ?>
    </main>
    
    <!-- Toast Component -->
    <?php include __DIR__ . '/../partials/components/toast.php'; ?>
    
    <!-- Footer -->
    <footer class="bg-white mt-20 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> LorPHP Framework. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>