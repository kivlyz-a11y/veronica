<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Riwayat Log Aktivitas (Audit Trail)</h3>
        <p class="text-muted small mb-0">Catatan audit lengkap atas seluruh aktivitas verifikasi, check-in, dan perubahan data dalam sistem.</p>
    </div>
</div>

<div class="card card-panel p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>Waktu (WITA)</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Ref ID</th>
                    <th>Deskripsi Aktivitas</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada log aktivitas tercatat.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-muted" style="min-width: 130px;">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <strong><?= esc($log['user_name'] ?: 'Sistem / Publik') ?></strong>
                                <?php if (!empty($log['user_role'])): ?>
                                    <small class="badge bg-light text-secondary border"><?= esc($log['user_role']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?= esc($log['action']) ?></code></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($log['module']) ?></span></td>
                            <td><strong><?= esc($log['reference_id'] ?: '-') ?></strong></td>
                            <td><?= esc($log['description']) ?></td>
                            <td><small class="text-muted"><?= esc($log['ip_address'] ?: '-') ?></small></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
