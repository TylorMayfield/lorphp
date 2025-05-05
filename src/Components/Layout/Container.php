<?php
namespace LorPHP\Components\Layout;

use LorPHP\Core\Component;

class Container extends Component {
    protected function template(): void {
        $maxWidth = $this->attr('maxWidth', '7xl');
        $padded = $this->attr('padded', true);
        
        $classes = $this->classes([
            "max-w-{$maxWidth} mx-auto" => true,
            'px-4 sm:px-6 lg:px-8' => $padded
        ]);
        ?>
        <div class="<?php echo $classes; ?>">
            <?php echo $this->slot('default'); ?>
        </div>
        <?php
    }
}
