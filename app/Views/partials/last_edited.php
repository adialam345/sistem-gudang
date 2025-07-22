<?php if (isset($updated_at) && isset($updated_by_name)): ?>
    <span class="text-xs bg-gray-200 rounded px-2 py-1" title="Diedit oleh <?= esc($updated_by_name) ?> pada <?= esc($updated_at) ?>">
        Terakhir diedit: <?= esc($updated_at) ?> oleh <?= esc($updated_by_name) ?>
    </span>
<?php endif; ?> 