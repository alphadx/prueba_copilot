// Service Worker for SGDII Tesis PWA
const CACHE_NAME = 'sgdii-tesis-v1';
const urlsToCache = [
  '/',
  '/css/site.css',
  '/manifest.json',
  // Add more static resources as needed
];

// Install event - cache resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
      .catch((error) => {
        console.log('Cache installation failed:', error);
      })
  );
  // Force the waiting service worker to become the active service worker
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Take control of all pages immediately
  return self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  // Skip non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip chrome extensions and other non-http(s) requests
  if (!event.request.url.startsWith('http')) {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Cache hit - return response from cache
        if (response) {
          return response;
        }

        // Clone the request
        const fetchRequest = event.request.clone();

        return fetch(fetchRequest).then((response) => {
          // Check if valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response
          const responseToCache = response.clone();

          // Only cache GET requests for same origin
          if (event.request.url.startsWith(self.location.origin)) {
            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              });
          }

          return response;
        }).catch((error) => {
          console.log('Fetch failed:', error);
          // Return a custom offline page if available
          return caches.match('/offline.html');
        });
      })
  );
});

// Listen for messages from the client
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

// Background sync for notifications (future enhancement)
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-notifications') {
    event.waitUntil(syncNotifications());
  }
});

async function syncNotifications() {
  try {
    // Fetch new notifications when back online
    const response = await fetch('/notification/api/check-new');
    const data = await response.json();
    
    if (data.hasNew) {
      // Show notification
      self.registration.showNotification('SGDII Tesis', {
        body: 'Tienes nuevas notificaciones',
        icon: '/assets/icon-192.png',
        badge: '/assets/badge.png',
        tag: 'new-notifications',
        requireInteraction: false
      });
    }
  } catch (error) {
    console.log('Sync failed:', error);
  }
}

// Push notifications support (future enhancement)
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'Nueva notificación',
    icon: '/assets/icon-192.png',
    badge: '/assets/badge.png',
    vibrate: [200, 100, 200],
    tag: 'thesis-notification',
    requireInteraction: false,
    actions: [
      {
        action: 'view',
        title: 'Ver'
      },
      {
        action: 'close',
        title: 'Cerrar'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('SGDII Tesis', options)
  );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'view') {
    event.waitUntil(
      clients.openWindow('/notifications')
    );
  }
});
