/* ============================================================
   arahOS OIT Help Desk — Service Worker (PWA offline support)
   Strategy:
     - Static assets (css/js/images/fonts): cache-first
     - HTML pages: network-first, fall back to cache + offline page
   Bump CACHE_VERSION to force refresh on deploy.
   ============================================================ */
const CACHE_VERSION = 'arahOS-v1';
const STATIC_CACHE = CACHE_VERSION + '-static';
const RUNTIME_CACHE = CACHE_VERSION + '-runtime';

const PRECACHE = [
  '/index.php',
  '/offline.html',
  '/css/arahOS/arahOS-tokens.css',
  '/css/arahOS/arahOS-client-portal.css',
  '/js/arahOS/arahOS-client-portal.js',
  '/images/arahOS/viseal.png',
  '/images/arahOS/pwa/icon-192.png',
  '/images/arahOS/pwa/icon-512.png'
];

// Install: precache core assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE)).catch(() => {})
  );
  self.skipWaiting();
});

// Activate: drop old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => !k.startsWith(CACHE_VERSION)).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch
self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== location.origin) return;

  // Static assets → cache-first
  if (/\.(css|js|png|jpg|jpeg|svg|woff2?|ttf|ico)(\?.*)?$/.test(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cached) =>
        cached ||
        fetch(request).then((res) => {
          if (res.ok) {
            const clone = res.clone();
            caches.open(STATIC_CACHE).then((c) => c.put(request, clone));
          }
          return res;
        })
      )
    );
    return;
  }

  // HTML pages → network-first, fallback to cache, then offline page
  event.respondWith(
    fetch(request)
      .then((res) => {
        if (res.ok && res.type === 'basic') {
          const clone = res.clone();
          caches.open(RUNTIME_CACHE).then((c) => c.put(request, clone));
        }
        return res;
      })
      .catch(() =>
        caches.match(request).then(
          (cached) => cached || caches.match('/offline.html')
        )
      )
  );
});
