<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Table extends Component {
    protected function template(): void {
        $headers = $this->attr('headers', []);
        $striped = $this->attr('striped', true);
        $hover = $this->attr('hover', true);
        
        $tableClasses = $this->classes([
            'min-w-full divide-y divide-[#3f3f46]' => true
        ]);
        
        $rowClasses = $this->classes([
            'bg-[#18181b]/50' => !$striped,
            'even:bg-[#27272a]/50' => $striped,
            'hover:bg-[#3f3f46]/50 transition-colors duration-150' => $hover
        ]);
        ?>
        <div class="overflow-x-auto">
            <table class="<?php echo $tableClasses; ?>">
                <?php if (!empty($headers)): ?>
                <thead>
                    <tr>
                        <?php foreach ($headers as $header): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#a1a1aa] uppercase tracking-wider border-b border-[#3f3f46]">
                            <?php echo $header; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <?php endif; ?>
                <tbody class="bg-transparent divide-y divide-[#3f3f46]">
                    <?php echo $this->slot('default'); ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
