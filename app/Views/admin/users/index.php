<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Manajemen Pengguna & Petugas</h3>
        <p class="text-muted small mb-0">Kelola akun Super Admin, Admin, Petugas Pelayanan PTSP, dan Pimpinan.</p>
    </div>
    <div>
        <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
        </button>
    </div>
</div>

<div class="card card-panel p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light text-uppercase">
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role / Hak Akses</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <strong class="text-dark"><?= esc($user['name']) ?></strong>
                        </td>
                        <td><?= esc($user['email']) ?></td>
                        <td>
                            <?php
                            $roleBadges = [
                                'superadmin' => 'bg-danger',
                                'admin'      => 'bg-primary',
                                'officer'    => 'bg-success',
                                'pimpinan'   => 'bg-warning text-dark',
                            ];
                            ?>
                            <span class="badge <?= $roleBadges[$user['role']] ?? 'bg-secondary' ?> text-uppercase">
                                <?= esc($user['role']) ?>
                            </span>
                        </td>
                        <td><?= esc($user['phone'] ?: '-') ?></td>
                        <td>
                            <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td><?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '-' ?></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-xs btn-outline-secondary p-1 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= site_url('admin/users/' . $user['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body small text-start">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Alamat Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Peran (Role)</label>
                                            <select name="role" class="form-select" required>
                                                <option value="officer" <?= $user['role'] === 'officer' ? 'selected' : '' ?>>Petugas (Officer)</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin Pelayanan</option>
                                                <option value="pimpinan" <?= $user['role'] === 'pimpinan' ? 'selected' : '' ?>>Pimpinan</option>
                                                <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nomor Telepon / WA</label>
                                            <input type="text" name="phone" class="form-control" value="<?= esc($user['phone'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                                            <input type="password" name="password" class="form-control" placeholder="Kata sandi baru">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status Akun</label>
                                            <select name="status" class="form-select">
                                                <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                                                <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/users') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Petugas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@pa-penajam.go.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role / Hak Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="officer">Petugas (Officer)</option>
                            <option value="admin">Admin Pelayanan</option>
                            <option value="pimpinan">Pimpinan</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon / WA</label>
                        <input type="text" name="phone" class="form-control" placeholder="08...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kata Sandi</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
