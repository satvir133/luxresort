<?php if($m=Session::flash_get('error')): ?><div class="p-3 mb-4 bg-red-100 text-red-800 rounded"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<?php if($m=Session::flash_get('success')): ?><div class="p-3 mb-4 bg-green-100 text-green-800 rounded"><?= htmlspecialchars($m) ?></div><?php endif; ?>
