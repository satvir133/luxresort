<?php $title='Register'; ob_start(); ?>
<h1 class="text-2xl font-semibold mb-6">Create a Guest Account</h1>
<form method="post" action="<?= url('/register') ?>" class="max-w-md grid gap-4">
  <input class="border rounded p-2" type="text" name="first_name" placeholder="First name">
  <input class="border rounded p-2" type="text" name="last_name" placeholder="Last name">
  <input class="border rounded p-2" type="email" name="email" placeholder="Email" required>
  <input class="border rounded p-2" type="text" name="phone" placeholder="Phone">
  <input class="border rounded p-2" type="password" name="password" placeholder="Password (min 8 chars)" minlength="8" required>
  <button class="px-4 py-2 bg-gray-900 text-white rounded">Register</button>
</form>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
