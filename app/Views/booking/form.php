<?php $title='Book Your Stay'; ob_start(); ?>
<div class="max-w-xl mx-auto p-6 bg-white rounded-xl shadow">
  <h1 class="text-2xl font-bold mb-4">Book Your Stay</h1>
  <form method="post" action="<?= url('/booking/search') ?>" class="space-y-3">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <label class="block">Check-in:
      <input class="w-full border rounded p-2" type="date" name="check_in" required>
    </label>
    <label class="block">Check-out:
      <input class="w-full border rounded p-2" type="date" name="check_out" required>
    </label>
    <label class="block">Room Type:
      <select class="w-full border rounded p-2" name="room_type_id" required>
        <option value="1">Ocean Villa</option>
        <option value="2">Family Suite</option>
      </select>
    </label>
    <button class="w-full py-2 bg-gray-900 text-white rounded">Search</button>
  </form>
</div>
<?php $content = ob_get_clean(); include __DIR__.'/../layouts/base.php'; ?>
