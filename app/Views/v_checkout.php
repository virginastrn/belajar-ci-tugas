<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-7">
        <?php 
        // Buka form dengan URL yang benar ke controller TransaksiController fungsi buy()
        // Helper form_open() sudah otomatis menyertakan CSRF field.
        echo form_open('transaksi/buy', ['class' => 'row g-3']);
        ?>
        
        <?php 
        // Input tersembunyi untuk mengirim data penting
        echo form_hidden('username', session()->get('username'));
        echo form_input(['type' => 'hidden', 'name' => 'total_harga', 'id' => 'total_harga', 'value' => $total]);
        ?>

        <div class="col-12">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" value="<?= session()->get('username') ?>" readonly>
        </div>
        <div class="col-12">
            <label for="alamat" class="form-label">Alamat Lengkap</label>
            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
        </div>
        <div class="col-12">
            <label for="kelurahan" class="form-label">Kelurahan Tujuan</label>
            <select class="form-control" id="kelurahan" name="kelurahan" required></select>
        </div>
        <div class="col-12">
            <label for="layanan" class="form-label">Layanan Pengiriman</label>
            <select class="form-control" id="layanan" name="layanan" required></select>
        </div>
        <div class="col-12">
            <label for="ongkir" class="form-label">Ongkir</label>
            <input type="text" class="form-control" id="ongkir" name="ongkir" readonly>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Rincian Pesanan</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col" class="text-end">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td>
                                        <?= $item['name'] ?><br>
                                        <small><?= $item['qty'] ?> x <?= number_to_currency($item['price'], 'IDR') ?></small>
                                    </td>
                                    <td class="text-end"><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <tr class="fw-bold">
                            <td>Subtotal Produk</td>
                            <td class="text-end"><?= number_to_currency($total, 'IDR') ?></td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Ongkos Kirim</td>
                            <td class="text-end"><span id="ongkir-summary">-</span></td>
                        </tr>
                        <tr class="fw-bold table-group-divider">
                            <td>Total Akhir</td>
                            <td class="text-end"><span id="total-akhir"><?= number_to_currency($total, 'IDR') ?></span></td>
                        </tr>
                    </tbody>
                </table>
                 <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">Buat Pesanan</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    // Menutup form yang dibuka dengan form_open()
    echo form_close(); 
    ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function() {
        // Mengambil nilai subtotal produk dari PHP
        let subtotalProduk = <?= $total ?>;
        let ongkir = 0;

        // Inisialisasi Select2 untuk dropdown Kelurahan
        $('#kelurahan').select2({
            placeholder: 'Ketik nama kelurahan...',
            ajax: {
                url: '<?= base_url('get-location') ?>',
                dataType: 'json',
                delay: 250, // Mengurangi delay untuk respons lebih cepat
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: `${item.subdistrict_name}, ${item.district_name}, ${item.city_name}`
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 3
        });

        // Event handler ketika Kelurahan dipilih
        $("#kelurahan").on('change', function() {
            let id_kelurahan = $(this).val();
            $("#layanan").empty().append('<option value="">Pilih Layanan</option>'); // Reset layanan
            ongkir = 0;
            hitungTotal(); // Hitung ulang total dengan ongkir 0

            if (id_kelurahan) {
                $.ajax({
                    url: "<?= site_url('get-cost') ?>",
                    type: 'GET',
                    data: { 'destination': id_kelurahan },
                    dataType: 'json',
                    success: function(data) {
                        data.forEach(function(item) {
                            // Format teks untuk opsi layanan
                            let text = `${item.description} (${item.service}) - Estimasi ${item.etd} hari`;
                            $("#layanan").append($('<option>', {
                                value: item.cost,
                                text: text
                            }));
                        });
                    }
                });
            }
        });

        // Event handler ketika Layanan pengiriman dipilih
        $("#layanan").on('change', function() {
            ongkir = parseInt($(this).val()) || 0; // Ambil nilai ongkir, jika tidak valid maka 0
            hitungTotal();
        });

        // Fungsi untuk menghitung dan menampilkan total
        function hitungTotal() {
            let totalAkhir = subtotalProduk + ongkir;

            // Format angka ke format mata uang Rupiah
            let formatRupiah = (angka) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            };

            // Update nilai di form dan tampilan
            $("#ongkir").val(ongkir);
            $("#ongkir-summary").html(formatRupiah(ongkir));
            $("#total-akhir").html(formatRupiah(totalAkhir));
            $("#total_harga").val(totalAkhir); // Update hidden input untuk dikirim ke controller
        }
    });
</script>
<?= $this->endSection() ?>