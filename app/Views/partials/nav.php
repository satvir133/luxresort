<?php $u = Session::get('user'); $roles = $u['roles'] ?? []; ?>
<nav class="nav">
  <div class="nav-inner">
    <a href="<?= url('/') ?>" style="font-weight:600">LuxResort</a>
    <a href="<?= url('/') ?>">Home</a>
    <a href="#rooms">Rooms</a>
    <a href="#experiences">Experiences</a>
    <a href="#contact">Contact</a>
    <div class="nav-spacer"></div>

    <?php if(!$u): ?>
      <a href="<?= url('/login') ?>">Login</a>
      <a class="btn" style="margin-left:8px" href="<?= url('/register') ?>">Register</a>
    <?php else: ?>
      <a href="<?= url('/dashboard') ?>">My Dashboard</a>
      <?php if(in_array('staff',$roles)||in_array('admin',$roles)): ?><a href="<?= url('/staff') ?>">Staff</a><?php endif; ?>
      <?php if(in_array('admin',$roles)): ?><a href="<?= url('/admin') ?>">Admin</a><?php endif; ?>
      <a class="btn" style="margin-left:8px" href="<?= url('/logout') ?>">Logout</a>
    <?php endif; ?>
  </div>
</nav>
