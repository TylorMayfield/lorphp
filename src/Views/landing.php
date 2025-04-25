<?php
/**
 * Landing page view
 */
$this->setLayout('base');
?>
<main class="max-w-6xl mx-auto mt-10 px-4">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to LorPHP Framework</h1>
        <p class="text-xl text-gray-600 mb-8">A modern PHP framework for building powerful web applications</p>
        <a href="/register" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-indigo-700">
            Get Started
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
        <?php $this->partial('components/feature-card', [
            'title' => 'Easy to Use',
            'description' => 'Built with simplicity in mind, get your application up and running quickly.'
        ]); ?>
        <?php $this->partial('components/feature-card', [
            'title' => 'Modern Stack',
            'description' => 'Integrated with Tailwind CSS and modern PHP practices.'
        ]); ?>
        <?php $this->partial('components/feature-card', [
            'title' => 'Secure',
            'description' => 'Built-in security features and authentication system.'
        ]); ?>
    </div>
</main>
