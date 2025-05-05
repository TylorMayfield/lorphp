<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Alert extends Component {
    protected function template(): void {
        $type = $this->attr('type', 'info');
        $dismissible = $this->attr('dismissible', true);
        
        $colors = [
            'info' => 'bg-blue-50 text-blue-800',
            'success' => 'bg-green-50 text-green-800',
            'error' => 'bg-red-50 text-red-800',
            'warning' => 'bg-yellow-50 text-yellow-800',
        ];
        
        $iconColors = [
            'info' => 'text-blue-400',
            'success' => 'text-green-400',
            'error' => 'text-red-400',
            'warning' => 'text-yellow-400',
        ];
        ?>
        <div class="rounded-md p-4 <?php echo $colors[$type] ?? $colors['info']; ?>" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 <?php echo $iconColors[$type] ?? $iconColors['info']; ?>" viewBox="0 0 20 20" fill="currentColor">
                        <?php if ($type === 'error'): ?>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        <?php else: ?>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        <?php endif; ?>
                    </svg>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium">
                        <?php echo $this->slot('default'); ?>
                    </div>
                </div>
                <?php if ($dismissible): ?>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 <?php echo $colors[$type] ?? $colors['info']; ?>" data-dismiss="alert">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
