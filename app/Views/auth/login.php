<?php $title='Login'; ob_start(); ?>
<h1 class="text-2xl font-semibold mb-6">Sign in</h1>
<form method="post" action="<?= url('/login') ?>" class="max-w-md space-y-4">
  <input class="w-full border rounded p-2" type="email" name="email" placeholder="Email" required>
  <input class="w-full border rounded p-2" type="password" name="password" placeholder="Password" required>
  <button class="px-4 py-2 bg-gray-900 text-white rounded">Login</button>
</form>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
