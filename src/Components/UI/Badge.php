<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Badge extends Component {
    public function type(string $type): self {
        return $this->with('type', $type);
    }
    
    protected function template(): void {
        $type = $this->attr('type', 'default');
        
        $colors = [
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'error' => 'bg-red-100 text-red-800',
            'info' => 'bg-blue-100 text-blue-800',
            'default' => 'bg-gray-100 text-gray-800'
        ];
        
        $colorClass = $colors[$type] ?? $colors['default'];
        ?>
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $colorClass; ?>">
            <?php echo $this->slot('default'); ?>
        </span>
        <?php
    }
}
