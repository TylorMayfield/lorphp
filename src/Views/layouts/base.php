<?php 
/**
 * Base layout template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'LorPHP Framework'; ?></title>
    
    <!-- Base path for all assets -->
    <base href="/">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <?php $this->renderStyles(); ?>
    
    <!-- Scripts -->
    <?php $this->renderScripts(); ?>
    
    <!-- Toast Notifications -->
    <?php echo $this->ui()->toast([
        'flash_error' => $flash_error ?? null,
        'flash_success' => $flash_success ?? null
    ]); ?>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php $this->renderNavigation(); ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php echo $this->renderContent(); ?>
    </main>
    
    <!-- Toast Component -->
    <?php include __DIR__ . '/../partials/components/toast.php'; ?>
    
    <!-- Footer -->
    <footer class="bg-white mt-20 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> LorPHP Framework. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Debug Bar -->
    <?php if (isset($debug) && $debug): ?>
        <?php $this->renderDebugBar(); ?>
    <?php endif; ?>
</body>
</html>