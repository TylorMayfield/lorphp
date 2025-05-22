<?php
use LorPHP\Core\RoleMiddleware;
use LorPHP\Core\View;

/** @var View $this */
?>
<!DOCTYPE html>
<html lang="en" class="bg-[#0a0a0c]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <meta name="theme-color" content="#18181b">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'LorPHP Framework'; ?></title>
    
    <!-- Critical rendering styles -->
    <style>
        /* Prevent FOUC */
        html {
            background-color: #0a0a0c;
            visibility: hidden;
        }
        
        /* Base styles to prevent flash */
        body {
            background-color: #0a0a0c;
            color: #fafafa;
            min-height: 100vh;
        }
        
        /* Show content once DOM is ready */
        .render-ready {
            visibility: visible;
            opacity: 1;
        }
        
        /* Smooth transitions between pages */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }
        
        ::view-transition-group(root) {
            z-index: 1;
            background-color: #0a0a0c;
        }
        
        main {
            view-transition-name: main-content;
            contain: paint;
        }
    </style>
    
    <!-- Add Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Add base styles -->
    <link rel="stylesheet" href="/css/transitions.css">
    
    <!-- PWA manifest -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
</head>
<body class="antialiased text-neutral-100 flex flex-col min-h-screen">
    <!-- Navigation -->
    <?php $this->partial('partials/navigation'); ?>

    <!-- Page content -->
    <?php echo $content; ?>

    <!-- Footer -->
    <?php $this->partial('partials/footer'); ?>

    <!-- Page scripts -->
    <script src="/js/transitions.js" defer></script>
    <script src="/js/offline-handler.js" defer></script>
    <script>
        // Add render-ready class once DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            document.documentElement.classList.add('render-ready');
        });
    </script>
</body>
</html>