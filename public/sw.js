// Self-Destruct Service Worker
// Automatically unregisters itself and clears all cache storage to resolve layout/login freezing.

self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(keys.map(key => caches.delete(key)));
    }).then(() => {
      return self.registration.unregister();
    }).then(() => {
      return self.clients.matchAll();
    }).then(clients => {
      clients.forEach(client => {
        if (client.navigate) {
          client.navigate(client.url);
        }
      });
    })
  );
});
