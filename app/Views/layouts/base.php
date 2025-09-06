<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Luxury Resort') ?></title>

  <!-- LOCAL CSS (always available) -->
  <link rel="stylesheet" href="<?= url('/assets/css/base.css') ?>">

  <!-- OPTIONAL: local Tailwind if you add it later -->
  <link rel="preload" href="<?= url('/assets/css/tailwind.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="<?= url('/assets/css/tailwind.min.css') ?>"></noscript>

  <!-- CDN Tailwind as last fallback -->
  <link id="tw-cdn" rel="preload" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
  <script>
    window.addEventListener('error', e => {
      const el = document.getElementById('tw-cdn'); if (el && e.target===el) el.remove();
    }, true);
  </script>

  <style>
    /* tiny extras for smooth look even without Tailwind */
    .hero{position:relative;min-height:70vh}
    .hero video,.hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
    .hero::after{content:"";position:absolute;inset:0;background:rgba(0,0,0,.4)}
    .hero-content{position:relative;color:#fff;padding:48px 24px;max-width:1100px;margin:0 auto;top:10vh}
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <?php include __DIR__.'/../partials/nav.php'; ?>
  <main>
    <?php include __DIR__.'/../partials/flash.php'; ?>
    <?= $content ?? '' ?>
  </main>
</body>
</html>
