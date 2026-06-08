const CACHE_NAME = 'travelmap-cache-v3'; // Tăng phiên bản cache để ép trình duyệt xóa cache cũ
const urlsToCache = [
  './css/style.css',
  './css/dashboard_mobile.css',
  'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css',
  'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js'
];

// Cài đặt và kích hoạt ngay lập tức
self.addEventListener('install', event => {
  self.skipWaiting(); // Bỏ qua trạng thái chờ, kích hoạt Service Worker mới ngay lập tức
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

// Dọn dẹp cache cũ khi kích hoạt
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Xóa cache cũ:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim()) // Buộc tất cả các tab đang chạy áp dụng Service Worker mới ngay
  );
});

// Xử lý fetch request
self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  
  // KHÔNG CACHE bất kỳ file PHP hoặc request động nào
  if (url.pathname.includes('.php') || url.search.includes('url=') || event.request.method !== 'GET') {
    return event.respondWith(fetch(event.request));
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Trả về từ cache nếu có (chỉ dành cho file tĩnh như CSS, JS, Libs)
        if (response) {
          return response;
        }
        
        return fetch(event.request).then(
          response => {
            // Không cache các response lỗi hoặc không phải dạng cơ bản
            if(!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Nhân bản response để đưa vào cache
            var responseToCache = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      }).catch(() => {
        // Fallback offline nếu mất mạng
      })
  );
});
