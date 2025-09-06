<?php $title='Admin Dashboard'; ob_start(); ?>
<h1 class="text-xl font-semibold mb-2">Admin Dashboard</h1>
<ul class="list-disc pl-6"><li>User & Role Management</li><li>Pricing & Policies</li></ul>
<?php $content=ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
