// تحميل مكتبة Workbox من CDN
importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.5.4/workbox-sw.js');

if (workbox) {
  // قائمة الملفات التي **يجب تخزينها**
  const manualCacheFiles = [
    // Init file \\
    "init.js",

    // Pages \\
    "index.html",

    // // Manifest file \\
    "manifest.json",

    // // Includes dir \\
    "includes/templates/navBar.html",
    "includes/themes/lightmode.css",

    // Layout dir \\
    // css
    "layout/css/compos.css",
    "layout/css/style.css",
    
    // imgs
    "layout/imgs/favicon.svg",
    "layout/imgs/profile.svg",
    "layout/imgs/manifest-icons/icon-72x72.png",
    "layout/imgs/manifest-icons/icon-96x96.png",
    "layout/imgs/manifest-icons/icon-128x128.png",
    "layout/imgs/manifest-icons/icon-144x144.png",
    "layout/imgs/manifest-icons/icon-152x152.png",
    "layout/imgs/manifest-icons/icon-192x192.png",
    "layout/imgs/manifest-icons/icon-384x384.png",

    // js
    "layout/js/compo.js",
    "layout/js/database.js",
    "layout/js/dexie.min.js",
    "layout/js/navBar.js",
    "layout/js/theme.js",
    "layout/js/xlsx.full.min.js",
  ];

  // Check All files \\
  manualCacheFiles.forEach(file => {
    fetch(file)
      .then(response => {
        if (!response.ok) console.error(`⚠️ خطأ في الملف ${file}:`, response.status);
      })
      .catch(err => console.error(`⚠️ فشل جلب الملف ${file}:`, err));
  });
  
  const CACHE_NAME = 'admin-files-v1';

  // Delete old caches
  self.addEventListener('activate', (event) => {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cache => {
            if (cache !== CACHE_NAME) {
              return caches.delete(cache);
            }
          })
        );
      })
    );
  });

  // عند التثبيت، نقوم بتخزين الملفات فورًا في الكاش
  self.addEventListener('install', (event) => {
    event.waitUntil(
      caches.open(CACHE_NAME).then(cache => {
        return cache.addAll(manualCacheFiles)
      })
    );
  });


  // استخدام Workbox لتخزين الملفات عند طلبها
  workbox.routing.registerRoute(
    ({ url }) => manualCacheFiles.some(file => url.pathname.includes(file)),
    new workbox.strategies.CacheFirst({
      cacheName: CACHE_NAME,
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxAgeSeconds: 30 * 24 * 60 * 60, // تخزين الملفات لمدة 30 يومًا
        }),
      ],
    })
  );
}