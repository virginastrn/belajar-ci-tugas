<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashData('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Keranjang Belanja Anda</h5>

        <?= form_open('keranjang/edit') ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Harga</th>
                        <th scope="col" style="width: 120px;">Jumlah</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    if (!empty($items)) :
                        foreach ($items as $index => $item) :
                    ?>
                            <tr>
                                <td><?= $item['name'] ?></td>
                                <td><img src="<?= base_url('img/' . $item['options']['foto']) ?>" width="100px" class="rounded"></td>
                                <td>
                                    <?php if ($item['options']['diskon'] > 0) : ?>
                                        <del class="text-muted"><?= number_to_currency($item['price'], 'IDR') ?></del><br>
                                        <strong class="text-success"><?= number_to_currency($item['price'] - $item['options']['diskon'], 'IDR') ?></strong>
                                    <?php else : ?>
                                        <?= number_to_currency($item['price'], 'IDR') ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="number" min="1" name="qty<?= $i++ ?>" class="form-control" value="<?= $item['qty'] ?>">
                                </td>
                                <td>
                                    <?php
                                    // Hitung subtotal setelah diskon
                                    $harga_setelah_diskon = $item['price'] - $item['options']['diskon'];
                                    echo number_to_currency($harga_setelah_diskon * $item['qty'], 'IDR');
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('keranjang/delete/' . $item['rowid']) ?>" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Subtotal</th>
                        <th colspan="2"><?= number_to_currency($total, 'IDR') ?></th>
                    </tr>
                    <?php if (isset($total_diskon) && $total_diskon > 0) : ?>
                        <tr>
                            <th colspan="4" class="text-end text-success">Total Diskon</th>
                            <th colspan="2" class="text-success">- <?= number_to_currency($total_diskon, 'IDR') ?></th>
                        </tr>
                    <?php endif; ?>
                    <tr class="table-group-divider">
                        <th colspan="4" class="text-end fs-5">Total Akhir</th>
                        <th colspan="2" class="fs-5">
                            <?php
                            // Total akhir adalah subtotal dikurangi total diskon
                            $total_akhir = $total - (isset($total_diskon) ? $total_diskon : 0);
                            echo number_to_currency($total_akhir, 'IDR');
                            ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Perbarui Keranjang</button>
            <a class="btn btn-warning" href="<?= base_url('keranjang/clear') ?>">Kosongkan Keranjang</a>
            <?php if (!empty($items)) : ?>
                <a class="btn btn-success" href="<?= base_url('checkout') ?>">Selesai Belanja</a>
            <?php endif; ?>
        </div>
        <?= form_close() ?>

    </div>
</div>

<?= $this->endSection() ?>