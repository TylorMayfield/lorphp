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
            'mt-1 block w-full px-4 py-3 rounded-xl bg-[#27272a]/50 border border-[#3f3f46] shadow-lg transition-all duration-200 text-[#fafafa] placeholder-[#71717a]' => true,
            'hover:bg-[#27272a]/70 focus:bg-[#27272a]/70 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 focus:ring-offset-0' => true,
            'border-red-500/20 focus:border-red-500 focus:ring-red-500 hover:border-red-500/30' => isset($error)
        ]);
        ?>
        <div>
            <?php if ($label): ?>
                <label for="<?php echo $name; ?>" class="block text-sm font-medium text-[#a1a1aa]">
                    <?php echo $label; ?>
                    <?php if ($required): ?>
                        <span class="text-red-400 ml-1">*</span>
                    <?php endif; ?>
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
                <p class="mt-2 text-sm text-red-400"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
