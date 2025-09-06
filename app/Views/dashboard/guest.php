<?php $title='Guest Dashboard'; ob_start(); ?>
<h1 class="text-xl font-semibold mb-2">Guest Dashboard</h1>
<p>Welcome! Search and book rooms (module next).</p>
<?php $content=ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
