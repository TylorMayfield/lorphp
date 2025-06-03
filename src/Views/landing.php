<?php
/**
 * Landing page view
 */
$this->setLayout('base');
?>
<main class="max-w-6xl mx-auto mt-24 px-4 min-h-screen pb-16">
    <div class="text-center bg-[#18181b] rounded-2xl shadow-xl p-8 md:p-12 border border-[#27272a] relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-blue-500/10 group-hover:opacity-75 transition-opacity duration-500"></div>
        <h1 class="relative text-5xl font-bold mb-4 bg-gradient-to-r from-indigo-400 via-purple-400 to-blue-400 bg-clip-text text-transparent animate-gradient-x">Welcome to LorPHP Framework</h1>
        <p class="text-xl text-[#a1a1aa] mb-8">A semi-modern PHP framework for building powerful web applications</p>
        <a href="/register" class="group relative inline-flex items-center px-8 py-4 rounded-xl overflow-hidden bg-gradient-to-r from-indigo-500 via-purple-500 to-blue-500 text-white font-medium text-lg transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
            <span class="relative z-10">Get Started</span>
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-all duration-500 -rotate-180 group-hover:rotate-0"></div>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mt-16">
        <?php $this->partial('components/feature-card', [
            'title' => 'Easy to Use',
            'description' => 'Built with simplicity in mind, get your application up and running quickly.',
            'icon' => '<svg class="w-8 h-8 mb-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>'
        ]); ?>
        <?php $this->partial('components/feature-card', [
            'title' => 'Modern Stack',
            'description' => 'Integrated with Tailwind CSS and modern PHP practices.',
            'icon' => '<svg class="w-8 h-8 mb-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/><path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/><path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/></svg>'
        ]); ?>
        <?php $this->partial('components/feature-card', [
            'title' => 'Secure',
            'description' => 'Built-in security features and authentication system.',
            'icon' => '<svg class="w-8 h-8 mb-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
        ]); ?>
        <?php if (getenv('APP_ENV') === 'dev' || getenv('APP_DEBUG')): ?>
        <?php $this->partial('components/feature-card', [
            'title' => 'Hot Reload',
            'description' => 'Auto-refresh your browser instantly on file changes for a seamless development experience.',
            'icon' => '<svg class="w-8 h-8 mb-4 text-pink-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 0021 12.07M18.364 5A9 9 0 003 11.93"/></svg>'
        ]); ?>
        <?php endif; ?>
    </div>
</main>
