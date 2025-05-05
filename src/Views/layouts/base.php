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
    <base href="/" />
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <?php 
    try {
        $this->renderStyles();
        echo "<!-- Debug: Styles rendered successfully -->\n";
    } catch (\Throwable $e) {
        echo "<!-- Debug: Styles render error: " . htmlspecialchars($e->getMessage()) . " -->\n";
    }
    ?>
    
    <!-- Scripts -->
    <?php 
    try {
        $this->renderScripts();
        echo "<!-- Debug: Scripts rendered successfully -->\n";
    } catch (\Throwable $e) {
        echo "<!-- Debug: Scripts render error: " . htmlspecialchars($e->getMessage()) . " -->\n";
    }
    ?>
    
    <!-- Toast Notifications -->
    <?php echo $this->ui()->toast([
        'flash_error' => $flash_error ?? null,
        'flash_success' => $flash_success ?? null
    ]); ?>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <?php 
    try {
        $this->renderNavigation();
        echo "<!-- Debug: Navigation rendered successfully -->\n";
    } catch (\Throwable $e) {
        echo "<!-- Debug: Navigation render error: " . htmlspecialchars($e->getMessage()) . " -->\n";
    }
    ?>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php 
        try {
            echo $this->renderContent();
            echo "<!-- Debug: Main content rendered successfully -->\n";
        } catch (\Throwable $e) {
            echo "<!-- Debug: Main content render error: " . htmlspecialchars($e->getMessage()) . " -->\n";
        }
        ?>
    </main>
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
