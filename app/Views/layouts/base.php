<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Luxury Resort') ?></title>

  <!-- Tailwind via CDN (quick start) -->
  <link rel="preconnect" href="https://cdn.jsdelivr.net" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- If you already have a local CSS, you can also keep this: -->
  <!-- <link rel="stylesheet" href="<?= url('/assets/css/tailwind.css') ?>"> -->
</head>
<body class="min-h-screen bg-gray-50">
  <?php include __DIR__.'/../partials/nav.php'; ?>
  <main class="max-w-5xl mx-auto p-6">
    <?php include __DIR__.'/../partials/flash.php'; ?>
    <?= $content ?? '' ?>
  </main>
</body>
</html>
