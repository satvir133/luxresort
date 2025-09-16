<?php $title='Admin — Rooms & Analytics'; ob_start(); ?>
<div class="max-w-6xl mx-auto p-6 space-y-8">

  <!-- Analytics -->
  <section class="bg-white rounded-xl shadow p-5">
    <h2 class="text-xl font-semibold mb-3">Analytics (last 30 days snapshot)</h2>
    <div class="grid md:grid-cols-3 gap-4">
      <div class="p-4 border rounded">
        <div class="text-sm text-gray-600">Total Bookings</div>
        <div class="text-2xl font-bold"><?= (int)$totals['total_bookings'] ?></div>
      </div>
      <div class="p-4 border rounded">
        <div class="text-sm text-gray-600">Revenue</div>
        <div class="text-2xl font-bold"><?= number_format(((int)$totals['revenue_cents'])/100,2) ?> USD</div>
      </div>
      <div class="p-4 border rounded">
        <div class="text-sm text-gray-600">Occupancy</div>
        <div class="text-2xl font-bold"><?= $occupancyPct ?>%</div>
      </div>
    </div>
  </section>

  <!-- Rooms -->
  <section class="bg-white rounded-xl shadow p-5">
    <h2 class="text-xl font-semibold mb-3">Rooms</h2>

    <form method="post" action="<?= url('/admin/room/create') ?>" class="flex flex-wrap gap-3 items-end mb-4">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <label>Room Type
        <select name="room_type_id" class="border rounded p-2">
          <?php foreach($types as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Code
        <input type="text" name="code" class="border rounded p-2" placeholder="e.g., OV-103" required>
      </label>
      <button class="px-3 py-2 bg-gray-900 text-white rounded">Add Room</button>
    </form>

    <?php if(!$rooms): ?>
      <p>No rooms added yet.</p>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full border">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-2 border">ID</th>
              <th class="text-left p-2 border">Code</th>
              <th class="text-left p-2 border">Type</th>
              <th class="text-left p-2 border">Status</th>
              <th class="text-left p-2 border">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rooms as $r): ?>
              <tr>
                <td class="p-2 border"><?= (int)$r['id'] ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['code']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['type_name']) ?></td>
                <td class="p-2 border"><?= htmlspecialchars($r['status']) ?></td>
                <td class="p-2 border">
                  <form method="post" action="<?= url('/admin/room/status') ?>" class="flex items-center gap-2">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="room_id" value="<?= (int)$r['id'] ?>">
                    <select name="status" class="border rounded p-1">
                      <?php foreach(['active','maintenance','retired'] as $s): ?>
                        <option value="<?= $s ?>" <?= $s===$r['status']?'selected':'' ?>><?= ucfirst($s) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="px-3 py-1 bg-gray-900 text-white rounded">Save</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
