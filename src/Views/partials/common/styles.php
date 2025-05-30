<?php
/**
 * Common styles partial
 */
?>
<link href="/css/transitions.css" rel="stylesheet">
<style>
    /* Glassmorphic Navbar Styles */
    .glassmorphic-nav {
        position: relative;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .glassmorphic-nav::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.2) 0%,
            rgba(255, 255, 255, 0.05) 100%
        );
        z-index: -1;
    }    /* Adjust main content to account for fixed navbar */
    .main-content {
        padding-top: 4rem; /* h-16 = 4rem */
    }
    .debug-bar {
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    }
</style>
