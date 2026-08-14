const CACHE_VERSION = 1.14;
const SW_NAME = 'projectpage-test';
const STATIC_NAME = `${SW_NAME + CACHE_VERSION}`;
const STATIC_ASSETS = [
  '/'
];

const OFFLINE_EXCLUDE = [
  '/login/',
  '/logout/',
  '/signup/',
  '/admin/'                      //exclude a directory
];

self.addEventListener('install', async () => {
  const cache = await caches.open(STATIC_NAME);
  await cache.addAll(STATIC_ASSETS);
  return self.skipWaiting();

});


self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then(function (cacheNames) {

      return Promise.all(
        cacheNames.map(function (cacheName) {
          if (cacheName !== STATIC_NAME && cacheName.startsWith(SW_NAME)) {

            return caches.delete(cacheName);

          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', async event => {
  const req = event.request;
  const url = new URL(req.url);
  for (let i = 0; i < OFFLINE_EXCLUDE.length; i++) {
    if (event.request.url.indexOf(OFFLINE_EXCLUDE[i]) !== -1) {
      console.log('WORKER: fetch event ignored. URL in exclude list.', event.request.url);
      return false;
    }
  }

  event.respondWith(networkFirst(req));

});

async function cacheFirst(req) {
  const cache = await caches.open(STATIC_NAME);
  const cachedResponse = await cache.match(req);
  //console.log(`Response:`, cachedResponse);
  return cachedResponse || fetch(req);

}
async function networkFirst(req) {

  const cachedResponse = await caches.match(req);
  //console.log(`Response:`, cachedResponse);
  return cachedResponse || fetch(req);

}

async function networkAndCache(req) {

  const cache = await caches.open(STATIC_NAME);
  try {
    const fresh = await fetch(req);

    await cache.put(req, fresh.clone());
    console.log(`Fresh:`, fresh);
    return fresh;
  } catch (error) {
    console.log(error);
    const cachedResponse = await cache.match(req);
    return cachedResponse;
  }
}

