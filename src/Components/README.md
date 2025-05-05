# LorPHP Components

This directory contains all the reusable UI components for the LorPHP framework.

## Directory Structure

- `Layout/` - Components for page layout and structure
  - `Container.php` - Main content container with responsive padding
  
- `UI/` - Basic UI components
  - `Alert.php` - Alert messages (success, error, warning, info)
  - `Card.php` - Card container with optional header and footer
  
- `Form/` - Form-related components
  - `Input.php` - Form input fields with labels and error states
  
- `Navigation/` - Navigation-related components
  - `NavLink.php` - Navigation links with active states

## Usage

Components can be used in views in two ways:

1. Simple usage:
```php
<?php echo $this->component(\LorPHP\Components\UI\Alert::class, [
    'type' => 'error',
    'dismissible' => true
])->withSlot('default', 'Error message here'); ?>
```

2. Complex usage with slots:
```php
<?php $card = $this->beginComponent(\LorPHP\Components\UI\Card::class, ['padded' => true]); ?>
    <?php $this->slot('header'); ?>
        <h3 class="text-lg font-medium">Card Title</h3>
    <?php $this->endSlot(); ?>
    
    <p>Card content goes here...</p>
    
    <?php $this->slot('footer'); ?>
        <button type="button">Action</button>
    <?php $this->endSlot(); ?>
<?php echo $this->endComponent($card); ?>
```

## Creating New Components

1. Create a new PHP class in the appropriate directory
2. Extend the `LorPHP\Core\Component` class
3. Implement the `template()` method
4. Use `$this->attr()` for properties
5. Use `$this->slot()` for content
6. Use `$this->classes()` for dynamic classes
