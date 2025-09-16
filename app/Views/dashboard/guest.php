<?php $title='My Bookings'; ob_start(); ?>
<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow">
  <h1 class="text-2xl font-bold mb-4">My Bookings</h1>

  <?php if(!$bookings): ?>
    <p>You have no bookings yet. <a class="underline" href="<?= url('/booking') ?>">Make a booking</a></p>
  <?php else: ?>
    <div class="space-y-3">
      <?php foreach($bookings as $b): ?>
        <div class="p-4 border rounded flex flex-wrap items-center gap-3 justify-between">
          <div>
            <div><strong>#<?= (int)$b['booking_id'] ?></strong> — <?= htmlspecialchars($b['rooms'] ?: '—') ?></div>
            <div class="text-sm text-gray-600">
              <?= htmlspecialchars($b['check_in'] ?? '') ?> → <?= htmlspecialchars($b['check_out'] ?? '') ?>
              · <?= htmlspecialchars(ucfirst($b['status'])) ?>
              · <?= number_format(((int)$b['total_cents'])/100, 2) ?> USD
            </div>
          </div>
          <?php if(in_array($b['status'],['pending','confirmed'])): ?>
          <form method="post" action="<?= url('/booking/cancel') ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="booking_id" value="<?= (int)$b['booking_id'] ?>">
            <button class="px-3 py-1 bg-rose-600 text-white rounded" onclick="return confirm('Cancel this booking?')">Cancel</button>
          </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
