<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class StatsCard extends Component {
    public function label(string $label): self {
        return $this->with('label', $label);
    }
    
    public function value($value): self {
        return $this->with('value', $value);
    }
    
    public function color(string $color): self {
        return $this->with('bgColor', $color);
    }
    
    protected function template(): void {
        $label = $this->attr('label');
        $value = $this->attr('value');
        $bgColor = $this->attr('bgColor', 'bg-indigo-500');
        ?>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="<?php echo $bgColor; ?> rounded-md p-3">
                            <?php echo $this->slot('icon', '<!-- Icon placeholder -->'); ?>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate"><?php echo $label; ?></dt>
                            <dd class="text-3xl font-semibold text-gray-900"><?php echo $value; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
