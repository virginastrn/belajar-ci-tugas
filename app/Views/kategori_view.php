<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori</title>
</head>
<body>
    <h1>Ini adalah halaman kategori</h1>
    <ul>
        <li><a href="<?= site_url('kategori/Alat Tulis'); ?>">Alat Tulis</a></li>
        <li><a href="<?= base_url('kategori/Pakaian'); ?>">Pakaian</a></li>
        <li><a href="<?= base_url('kategori/Pertukangan'); ?>">Pertukangan</a></li>
        <li><a href="<?= base_url('kategori/Elektronik'); ?>">Elektronik</a></li>
        <li><a href="<?= base_url('kategori/Snack'); ?>">Snack</a></li>
    </ul>
</body>
</html>
