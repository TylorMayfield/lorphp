<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Table extends Component {
    protected function template(): void {
        $headers = $this->attr('headers', []);
        $striped = $this->attr('striped', true);
        $hover = $this->attr('hover', true);
        
        $tableClasses = $this->classes([
            'min-w-full divide-y divide-gray-200' => true
        ]);
        
        $rowClasses = $this->classes([
            'bg-white' => !$striped,
            'even:bg-gray-50' => $striped,
            'hover:bg-gray-100' => $hover
        ]);
        ?>
        <div class="overflow-x-auto">
            <table class="<?php echo $tableClasses; ?>">
                <?php if (!empty($headers)): ?>
                <thead>
                    <tr>
                        <?php foreach ($headers as $header): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo $header; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <?php endif; ?>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php echo $this->slot('default'); ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
