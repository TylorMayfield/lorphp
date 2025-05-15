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
    <meta name="theme-color" content="#18181b">
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'gradient-x': 'gradient-x 15s ease infinite',
                        'blob': "blob 7s infinite",
                    },
                    keyframes: {
                        'gradient-x': {
                            '0%, 100%': {
                                'background-size': '200% 200%',
                                'background-position': 'left center'
                            },
                            '50%': {
                                'background-size': '200% 200%',
                                'background-position': 'right center'
                            }
                        },
                        'blob': {
                            "0%": {
                                transform: "translate(0px, 0px) scale(1)",
                            },
                            "33%": {
                                transform: "translate(30px, -50px) scale(1.1)",
                            },
                            "66%": {
                                transform: "translate(-20px, 20px) scale(0.9)",
                            },
                            "100%": {
                                transform: "translate(0px, 0px) scale(1)",
                            },
                        }
                    }
                }
            }
        }
    </script>
    
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

<body class="bg-[#0a0a0c] text-[#fafafa] min-h-screen flex flex-col">


    <!-- Navigation -->
    <?php include __DIR__ . '/../partials/common/navigation.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-12 md:py-16 relative">
        <!-- Gradient decorations -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-1/4 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-soft-light filter blur-xl opacity-15 animate-blob"></div>
            <div class="absolute top-1/3 -right-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-soft-light filter blur-xl opacity-15 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-soft-light filter blur-xl opacity-15 animate-blob animation-delay-4000"></div>
        </div>
        <div class="rounded-2xl shadow-xl bg-[#18181b] backdrop-blur-xl p-6 md:p-10 border border-[#27272a] relative transition-all duration-300 hover:shadow-2xl hover:border-[#3f3f46]">
            <?php if (method_exists($this, 'renderContent')): ?>
                <?php echo $this->renderContent(); ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Toast Component -->
    <?php include __DIR__ . '/../partials/components/toast.php'; ?>
    
    <!-- Footer -->
    <footer class="relative py-8 mt-0 overflow-hidden bg-[#18181b]">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 via-purple-500/5 to-blue-500/5"></div>
        <div class="absolute top-0 h-[1px] w-full bg-gradient-to-r from-transparent via-[#27272a] to-transparent"></div>
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4 text-[#71717a] relative">
            <p class="text-sm">&copy; <?php echo date('Y'); ?> LorPHP Framework. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="https://github.com/tylormayfield/lorphp" target="_blank" rel="noopener" class="hover:text-indigo-400 transition-colors duration-300">GitHub</a>
                <a href="/docs" class="hover:text-purple-400 transition-colors duration-300">Docs</a>
            </div>
        </div>
    </footer>
</body>
</html>