<?php $title='Login'; ob_start(); ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
  <div class="w-full max-w-md bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-semibold mb-6 text-center">Sign in</h1>
    <form method="post" action="<?= url('/login') ?>" class="space-y-4">
      <input class="w-full border rounded-lg p-3" type="email" name="email" placeholder="Email" required>
      <input class="w-full border rounded-lg p-3" type="password" name="password" placeholder="Password" required>
      <button class="w-full py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">Login</button>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
