<?php
/**
 * Feature card partial
 */
?>
<div class="group bg-[#18181b] p-8 rounded-xl shadow-lg border border-[#27272a] hover:border-[#3f3f46] transition-all duration-300 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 via-purple-500/5 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    <?php if (isset($icon)) echo $icon; ?>
    <h3 class="text-xl font-semibold mb-3 text-[#fafafa] relative"><?php echo $title; ?></h3>
    <p class="text-[#a1a1aa] relative"><?php echo $description; ?></p>
</div>
