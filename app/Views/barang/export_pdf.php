<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Barang - <?= $tanggal ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .stok-warning {
            color: red;
        }
        .stok-ok {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Daftar Barang</h1>
    <p>Tanggal: <?= $tanggal ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($barang as $item): ?>
            <tr>
                <td><?= $item['kode'] ?></td>
                <td><?= $item['nama'] ?></td>
                <td><?= $item['kategori'] ?></td>
                <td><?= $item['satuan'] ?></td>
                <td class="<?= $item['stok'] > 0 ? 'stok-ok' : 'stok-warning' ?>">
                    <?= number_format($item['stok']) ?>
                </td>
                <td><?= $item['deskripsi'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html> 