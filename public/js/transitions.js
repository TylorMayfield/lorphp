document.addEventListener('DOMContentLoaded', () => {
    // Only run if the View Transitions API is supported and we're not in an iframe
    if (!document.startViewTransition || window.self !== window.top) return;

    // Wait for the initial page load to complete
    window.addEventListener('load', () => {
        // Handle all link clicks
        document.addEventListener('click', async e => {
            const link = e.target.closest('a');
            if (!link || !link.href || link.target === '_blank') return;

            // Only handle links to the same origin
            if (new URL(link.href).origin !== location.origin) return;

            e.preventDefault();

            try {
                // Navigate to the new page using View Transitions API
                await document.startViewTransition(async () => {
                    // Load the new page
                    const response = await fetch(link.href);
                    if (!response.ok) throw new Error('Navigation failed');
                    
                    const text = await response.text();
                    
                    // Extract and update the main content
                    document.documentElement.innerHTML = text;
                });
            } catch (error) {
                console.error('Navigation error:', error);
                // Fall back to regular navigation
                window.location.href = link.href;
            }
        });
    });
});
