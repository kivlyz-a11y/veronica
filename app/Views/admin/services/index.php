<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Jenis Layanan</h3>
        <p class="text-muted small mb-0">Kelola master layanan informasi dan pendaftaran Pengadilan Agama Penajam.</p>
    </div>
    <div>
        <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Layanan Baru
        </button>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($services as $service): ?>
        <div class="col-lg-6">
            <div class="card card-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-success text-white fs-4">
                            <i class="bi <?= esc($service['icon']) ?>"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1"><?= esc($service['name']) ?></h5>
                            <small class="text-muted">Slug: <code><?= esc($service['slug']) ?></code></small>
                        </div>
                    </div>
                    <div>
                        <span class="badge <?= $service['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($service['status']) ?>
                        </span>
                    </div>
                </div>

                <p class="text-muted small mb-3"><?= esc($service['description']) ?></p>

                <!-- Subcategories list -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark small mb-0">Daftar Sub-Kategori (<?= count($service['subcategories']) ?>):</h6>
                    <button type="button" class="btn btn-xs btn-outline-success p-1 px-2" data-bs-toggle="modal" data-bs-target="#addSubModal<?= $service['id'] ?>">
                        <i class="bi bi-plus"></i> Tambah Sub
                    </button>
                </div>

                <ul class="list-group list-group-flush mb-3 small">
                    <?php if (empty($service['subcategories'])): ?>
                        <li class="list-group-item text-muted px-0">Belum ada sub-kategori.</li>
                    <?php else: ?>
                        <?php foreach ($service['subcategories'] as $sub): ?>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    <span><?= esc($sub['name']) ?></span>
                                </div>
                                <form action="<?= site_url('admin/services/subcategory/' . $sub['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus sub-layanan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-xs text-danger p-0 border-0">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Add Subcategory Modal -->
        <div class="modal fade" id="addSubModal<?= $service['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="<?= site_url('admin/services/subcategory') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">Tambah Sub-Kategori untuk <?= esc($service['name']) ?></h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body small">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Sub-Layanan</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Informasi Perkara" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi (Opsional)</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success fw-semibold">Simpan Sub-Layanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/services') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Jenis Layanan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Layanan</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Layanan Konsultasi Posbakum" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icon Bootstrap Icons</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-info-circle-fill" value="bi-info-circle-fill" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Layanan</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
