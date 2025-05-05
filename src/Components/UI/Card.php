<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Card extends Component {
    protected function template(): void {
        $padded = $this->attr('padded', true);
        
        $classes = $this->classes([
            'bg-white shadow rounded-lg' => true,
            'p-6' => $padded
        ]);
        ?>
        <div class="<?php echo $classes; ?>">
            <?php if ($this->slot('header')): ?>
                <div class="border-b border-gray-200 pb-5">
                    <?php echo $this->slot('header'); ?>
                </div>
            <?php endif; ?>

            <div class="<?php echo $this->slot('header') ? 'pt-5' : ''; ?>">
                <?php echo $this->slot('default'); ?>
            </div>

            <?php if ($this->slot('footer')): ?>
                <div class="border-t border-gray-200 pt-5 mt-5">
                    <?php echo $this->slot('footer'); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
