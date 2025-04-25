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
    <title>LorPHP Framework</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php $this->renderStyles(); ?>
    <?php $this->renderScripts(); ?>
</head>
<body class="bg-gray-100">    <?php $this->renderNavigation(); ?>
    <?php echo $this->renderContent(); ?>
    
    <footer class="bg-white mt-20 py-8">
        <div class="max-w-6xl mx-auto px-4 text-center text-gray-600">
            <p>&copy; <?php echo date('Y'); ?> LorPHP Framework. All rights reserved.</p>
        </div>
    </footer>
    
    <?php if ($this->debug): ?>
        <?php $this->renderDebugBar(); ?>
    <?php endif; ?>
</body>
</html>
