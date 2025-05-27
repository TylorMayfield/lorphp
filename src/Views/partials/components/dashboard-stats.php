<?php
/**
 * Dashboard stats component
 */
?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 lg:gap-6 xl:gap-8">
    <?php foreach ($stats as $stat): ?>
        <?php echo $this->ui()->statsCard()
            ->label($stat['label'])
            ->value($stat['value'])
            ->color($stat['color'])
            ->icon($stat['icon']); ?>
    <?php endforeach; ?>
</div>
