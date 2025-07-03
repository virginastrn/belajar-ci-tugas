<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">History Transaksi Pembelian <strong><?= $username ?></strong></h5>

        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID Pembelian</th>
                        <th scope="col">Waktu Pembelian</th>
                        <th scope="col">Total Bayar</th>
                        <th scope="col">Ongkir</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)) : ?>
                        <?php foreach ($transactions as $index => $transaction) : ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td><?= $transaction['id'] ?></td>
                                <td>
                                    <?= date('Y-m-d', strtotime($transaction['created_at'])) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i:s', strtotime($transaction['created_at'])) ?></small>
                                </td>
                                <td><?= number_to_currency($transaction['total_harga'], 'IDR', 'id_ID') ?></td>
                                <td><?= number_to_currency($transaction['ongkir'], 'IDR', 'id_ID') ?></td>
                                <td><?= $transaction['alamat'] ?></td>
                                <td>
                                    <?php if ($transaction['status'] == 0) : ?>
                                        <span class="badge bg-warning text-dark">Belum Selesai</span>
                                    <?php else : ?>
                                        <span class="badge bg-success">Sudah Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $transaction['id'] ?>">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal untuk setiap transaksi -->
                            <div class="modal fade" id="detailModal-<?= $transaction['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php if (!empty($products[$transaction['id']])) : ?>
                                                <?php foreach ($products[$transaction['id']] as $key => $product) : 
                                                    // Ambil diskon live dari array
                                                    $diskon_hari_ini = $diskon_live[$product['id']] ?? 0;
                                                ?>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="me-3 fw-bold"><?= $key + 1 ?>)</div>
                                                        
                                                        <img src="<?= base_url('img/' . $product['foto_produk']) ?>" alt="<?= $product['nama_produk'] ?>" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">

                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1"><?= $product['nama_produk'] ?></h6>
                                                            
                                                            <!-- Tampilkan harga dengan diskon live -->
                                                            <?php if ($diskon_hari_ini > 0) : ?>
                                                                <del class="text-muted"><?= number_to_currency($product['harga_produk'], 'IDR', 'id_ID') ?></del>
                                                                <strong class="text-danger ms-2"><?= number_to_currency($product['harga_produk'] - $diskon_hari_ini, 'IDR', 'id_ID') ?></strong>
                                                            <?php else : ?>
                                                                <strong><?= number_to_currency($product['harga_produk'], 'IDR', 'id_ID') ?></strong>
                                                            <?php endif; ?>

                                                            <div class="d-flex justify-content-between mt-1">
                                                                <span class="text-muted"><?= $product['jumlah'] ?> pcs</span>
                                                                <!-- Subtotal tetap dari data transaksi asli -->
                                                                <strong class="text-primary"><?= number_to_currency($product['subtotal_harga'], 'IDR', 'id_ID') ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <hr>
                                            
                                            <div class="d-flex justify-content-between">
                                                <span>Ongkir</span>
                                                <strong><?= number_to_currency($transaction['ongkir'], 'IDR', 'id_ID') ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <h5>Total Bayar (Saat Transaksi)</h5>
                                                <h5 class="text-danger"><?= number_to_currency($transaction['total_harga'], 'IDR', 'id_ID') ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Akhir Modal -->
                            
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center">Anda belum memiliki riwayat transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
