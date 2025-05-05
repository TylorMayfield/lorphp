<?php
namespace LorPHP\Components\UI;

use LorPHP\Core\Component;

class Toast extends Component {
    protected function template(): void {
        $error = $this->attr('flash_error');
        $success = $this->attr('flash_success');
        $info = $this->attr('flash_info');
        $warning = $this->attr('flash_warning');
        $messages = $this->attr('flash_messages'); // optional: array of ['type' => ..., 'message' => ...]
        ?>
        <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col items-end space-y-2"></div>
        <script>
        function showToast(message, type = 'info') {
            const icons = {
                error: '❌',
                success: '✅',
                info: 'ℹ️',
                warning: '⚠️'
            };

            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            toast.className = `relative flex items-start gap-2 max-w-xs px-4 py-3 rounded shadow-md text-white transition-all duration-300 opacity-0 transform translate-x-2 text-sm
                ${type === 'error' ? 'bg-red-600' :
                  type === 'success' ? 'bg-green-600' :
                  type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'}`;

            toast.innerHTML = `
                <span class="text-lg">${icons[type] || ''}</span>
                <span class="flex-1">${message}</span>
                <button class="ml-2 text-white hover:text-gray-200 focus:outline-none text-lg leading-none absolute top-1 right-2" aria-label="Dismiss">&times;</button>
            `;

            // Handle click to dismiss
            toast.querySelector('button').addEventListener('click', () => {
                toast.classList.add('opacity-0', 'translate-x-2');
                setTimeout(() => toast.remove(), 300);
            });

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-x-2');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-2');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        <?php if ($error): ?>
            showToast(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>

        <?php if ($success): ?>
            showToast(<?php echo json_encode($success); ?>, 'success');
        <?php endif; ?>

        <?php if ($info): ?>
            showToast(<?php echo json_encode($info); ?>, 'info');
        <?php endif; ?>

        <?php if ($warning): ?>
            showToast(<?php echo json_encode($warning); ?>, 'warning');
        <?php endif; ?>

        <?php if (is_array($messages)): 
            foreach ($messages as $msg): ?>
                showToast(<?php echo json_encode($msg['message']); ?>, <?php echo json_encode($msg['type'] ?? 'info'); ?>);
        <?php endforeach; endif; ?>
        </script>
        <?php
    }
}
