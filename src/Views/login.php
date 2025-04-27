<?php
/**
 * Login page view
 */
$this->setLayout('base');

ob_start();
?>
<form class="mt-8 space-y-6" action="/login" method="POST">
    <input type="hidden" name="_csrf" value="<?php echo  $_SESSION['csrf_token'] ?? ''; ?>">
    
    <?php $this->partial('forms/input', [
        'id' => 'email',
        'type' => 'email',
        'label' => 'Email address',
        'required' => true,
        'value' => isset($value) && is_array($value) ? ($value['email'] ?? '') : '',
        'class' => 'rounded-t-md',
        'error' => $error ?? null
    ]); ?>

    <?php $this->partial('forms/input', [
        'id' => 'password',
        'type' => 'password',
        'label' => 'Password',
        'required' => true,
        'class' => 'rounded-b-md',
        'error' => $error ?? null
    ]); ?>

    <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Sign in
        </button>
    </div>
</form>
<?php
$content = ob_get_clean();

$this->partial('components/auth-container', [
    'title' => 'Sign in to your account',
    'subtitle' => 'Or',
    'linkText' => 'create a new account',
    'linkUrl' => '/register',
    'content' => $content
]);


