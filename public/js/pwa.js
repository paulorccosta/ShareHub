if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').catch(function (err) {
            console.warn('ShareHub: falha ao registrar service worker', err);
        });
    });
}
