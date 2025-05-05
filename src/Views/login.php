<?php
/**
 * Login page view
 */

// Start capturing the form content
ob_start();
?>
<form class="mt-2" action="/login" method="POST">
    <input type="hidden" name="_csrf" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    
    <?php echo $this->component(\LorPHP\Components\Form\Input::class)
        ->name('email')
        ->type('email')
        ->label('Email address')
        ->required()
        ->value(isset($value) && is_array($value) ? ($value['email'] ?? '') : '')
        ->with('class', 'rounded-t-md')
        ->error($error ?? null); ?>

    <?php echo $this->component(\LorPHP\Components\Form\Input::class)
        ->name('password')
        ->type('password')
        ->label('Password')
        ->required()
        ->with('class', 'rounded-b-md')
        ->error($error ?? null); ?>

    <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Sign in
        </button>
    </div>
</form>
<?php
$formContent = ob_get_clean();

echo $this->component(\LorPHP\Components\Layout\AuthContainer::class)
    ->with('title', 'Sign in to your account')
    ->with('subtitle', 'Or')
    ->with('linkText', 'create a new account')
    ->with('linkUrl', '/register')
    ->withSlot('default', $formContent);


