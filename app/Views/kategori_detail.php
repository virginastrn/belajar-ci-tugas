<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kategori</title>
</head>
<body>
    <h1><center>Kategori <?= esc($kategori); ?></center></h1><br>

    <center>
        <p>SELAMAT DATANG DI HALAMAN KATEGORI "<strong><?= esc($kategori); ?></strong>".</p>
        <a href="<?= site_url('kategori'); ?>">Kembali</a>
    </center>

</body>
</html>
