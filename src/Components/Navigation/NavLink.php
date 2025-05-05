<?php
namespace LorPHP\Components\Navigation;

use LorPHP\Core\Component;

class NavLink extends Component {
    protected function template(): void {
        $href = $this->attr('href', '#');
        $active = $this->attr('active', false);
        
        $classes = $this->classes([
            'px-3 py-2 rounded-md text-sm font-medium' => true,
            'bg-gray-900 text-white' => $active,
            'text-gray-300 hover:bg-gray-700 hover:text-white' => !$active
        ]);
        ?>
        <a href="<?php echo $href; ?>" class="<?php echo $classes; ?>">
            <?php echo $this->slot('default'); ?>
        </a>
        <?php
    }
}
