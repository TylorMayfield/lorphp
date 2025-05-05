<?php
namespace LorPHP\Components\Form;

use LorPHP\Core\Component;

class Input extends Component {
    public function type(string $type): self {
        return $this->with('type', $type);
    }
    
    public function name(string $name): self {
        return $this->with('name', $name);
    }
    
    public function value($value): self {
        return $this->with('value', $value);
    }
    
    public function label(string $label): self {
        return $this->with('label', $label);
    }
    
    public function placeholder(string $placeholder): self {
        return $this->with('placeholder', $placeholder);
    }
    
    public function required(bool $required = true): self {
        return $this->with('required', $required);
    }
    
    public function error(?string $error): self {
        return $this->with('error', $error);
    }
    
    protected function template(): void {
        $type = $this->attr('type', 'text');
        $name = $this->attr('name');
        $value = $this->attr('value', '');
        $label = $this->attr('label');
        $placeholder = $this->attr('placeholder', '');
        $required = $this->attr('required', false);
        $error = $this->attr('error');
        
        $inputClasses = $this->classes([
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm' => true,
            'border-red-300' => isset($error)
        ]);
        ?>
        <div>
            <?php if ($label): ?>
                <label for="<?php echo $name; ?>" class="block text-sm font-medium text-gray-700">
                    <?php echo $label; ?>
                </label>
            <?php endif; ?>
            
            <input 
                type="<?php echo $type; ?>"
                name="<?php echo $name; ?>"
                id="<?php echo $name; ?>"
                value="<?php echo htmlspecialchars($value); ?>"
                placeholder="<?php echo htmlspecialchars($placeholder); ?>"
                <?php echo $required ? 'required' : ''; ?>
                class="<?php echo $inputClasses; ?>"
            />
            
            <?php if ($error): ?>
                <p class="mt-2 text-sm text-red-600"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
