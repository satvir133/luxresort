<?php $title='Staff — Manage Bookings'; ob_start(); ?>
<div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow">
  <h1 class="text-2xl font-bold mb-4">Manage Bookings</h1>
  <?php if(!$bookings): ?>
    <p>No bookings yet.</p>
  <?php else: ?>
    <div class="space-y-3">
      <?php foreach($bookings as $b): ?>
        <div class="p-4 border rounded">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div><strong>#<?= (int)$b['id'] ?></strong> — <?= htmlspecialchars($b['rooms'] ?: '—') ?></div>
              <div class="text-sm text-gray-600">
                <?= htmlspecialchars($b['check_in'] ?? '') ?> → <?= htmlspecialchars($b['check_out'] ?? '') ?>
                · <?= htmlspecialchars(ucfirst($b['status'])) ?>
                · <?= number_format(((int)$b['total_cents'])/100, 2) ?> USD
              </div>
              <div class="text-sm text-gray-500">User: <?= htmlspecialchars($b['email'] ?? '—') ?></div>
            </div>
            <form method="post" action="<?= url('/staff/booking/status') ?>" class="flex items-center gap-2">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
              <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
              <select name="status" class="border rounded p-1">
                <?php foreach($statuses as $s): ?>
                  <option value="<?= $s ?>" <?= $s===$b['status']?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="px-3 py-1 bg-gray-900 text-white rounded">Update</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
