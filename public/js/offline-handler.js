// Store the last URL before going offline
window.addEventListener('offline', () => {
    sessionStorage.setItem('wasOffline', 'true');
    sessionStorage.setItem('lastAttemptedUrl', window.location.pathname);
});

// Clear offline state when online
window.addEventListener('online', () => {
    const lastUrl = sessionStorage.getItem('lastAttemptedUrl') || '/';
    window.location.href = lastUrl;
    sessionStorage.removeItem('wasOffline');
    sessionStorage.removeItem('lastAttemptedUrl');
});
