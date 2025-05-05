<?php
/**
 * Toast notification partial
 * Usage: include this partial in your base layout
 */
?>
<div id="toast-container" class="fixed top-5 right-5 z-50"></div>
<script>
function showToast(message, type = 'error') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `mb-2 px-4 py-2 rounded shadow text-white ${type === 'error' ? 'bg-red-600' : 'bg-green-600'}`;
    toast.innerText = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
<?php if (isset($flash_error) && $flash_error): ?>
    showToast(<?php echo json_encode($flash_error); ?>, 'error');
<?php endif; ?>
<?php if (isset($flash_success) && $flash_success): ?>
    showToast(<?php echo json_encode($flash_success); ?>, 'success');
<?php endif; ?>
</script>
