<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class FeatureCard extends Component {
    protected function template(): void {
        $title = $this->attr('title');
        $description = $this->attr('description');
        ?>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4"><?php echo $title; ?></h3>
            <?php if ($this->slot('default')): ?>
                <?php echo $this->slot('default'); ?>
            <?php else: ?>
                <p class="text-gray-600"><?php echo $description; ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
