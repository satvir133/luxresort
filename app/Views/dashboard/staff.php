<?php $title='Staff Dashboard'; ob_start(); ?>
<h1 class="text-xl font-semibold mb-2">Staff Dashboard</h1>
<ul class="list-disc pl-6"><li>Arrivals / Departures</li><li>Inventory</li></ul>
<?php $content=ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
