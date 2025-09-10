<?php $title='Register'; ob_start(); ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
  <div class="w-full max-w-md bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-semibold mb-6 text-center">Create a Guest Account</h1>
    <form method="post" action="<?= url('/register') ?>" class="space-y-4">
      <input class="w-full border rounded-lg p-3" type="text" name="first_name" placeholder="First name" required>
      <input class="w-full border rounded-lg p-3" type="text" name="last_name" placeholder="Last name" required>
      <input class="w-full border rounded-lg p-3" type="email" name="email" placeholder="Email" required>
      <input class="w-full border rounded-lg p-3" type="text" name="phone" placeholder="Phone">
      <input class="w-full border rounded-lg p-3" type="password" name="password" placeholder="Password (min 8 chars)" minlength="8" required>
      <button class="w-full py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">Register</button>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
