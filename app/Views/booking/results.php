<?php $title='Available Rooms'; ob_start(); ?>
<div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
  <h1 class="text-2xl font-bold mb-4">Available Rooms</h1>
  <?php if(!$rooms): ?>
    <p>No rooms available for <?= htmlspecialchars($checkIn) ?> → <?= htmlspecialchars($checkOut) ?></p>
  <?php else: ?>
    <ul class="space-y-3">
      <?php foreach($rooms as $r): ?>
        <li class="p-4 border rounded flex justify-between items-center">
          <div>
            <strong><?= htmlspecialchars($r['code']) ?></strong>
            <span class="text-gray-500"> (Room #<?= $r['id'] ?>)</span>
          </div>
          <form method="post" action="<?= url('/booking/confirm') ?>">
            <input type="hidden" name="room_id" value="<?= $r['id'] ?>">
            <input type="hidden" name="check_in" value="<?= htmlspecialchars($checkIn) ?>">
            <input type="hidden" name="check_out" value="<?= htmlspecialchars($checkOut) ?>">
            <button class="px-3 py-1 bg-green-600 text-white rounded">Book</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
