<?php
// Always define these before using
$u     = Session::get('user');          // array|null
$roles = $u['roles'] ?? [];             // array
?>
<nav class="bg-white border-b">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-4">
    <a class="font-semibold" href="<?= url('/') ?>">LuxResort</a>
    <div class="flex-1"></div>

    <?php if(!$u): ?>
      <a class="hover:underline" href="<?= url('/login') ?>">Login</a>
      <a class="ml-2 px-3 py-1 bg-gray-900 text-white rounded" href="<?= url('/register') ?>">Register</a>
    <?php else: ?>
      <a class="hover:underline" href="<?= url('/dashboard') ?>">Guest</a>
      <?php if(in_array('staff',$roles) || in_array('admin',$roles)): ?>
        <a class="hover:underline" href="<?= url('/staff') ?>">Staff</a>
      <?php endif; ?>
      <?php if(in_array('admin',$roles)): ?>
        <a class="hover:underline" href="<?= url('/admin') ?>">Admin</a>
      <?php endif; ?>
      <span class="text-gray-500 ml-2"><?= htmlspecialchars($u['email'] ?? '') ?></span>
      <a class="ml-2 px-3 py-1 bg-gray-900 text-white rounded" href="<?= url('/logout') ?>">Logout</a>
    <?php endif; ?>
  </div>
</nav>
