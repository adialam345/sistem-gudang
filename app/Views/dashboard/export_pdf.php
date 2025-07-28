<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Aktivitas Terbaru - <?= $tanggal ?></title>
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
        .status-masuk {
            color: green;
        }
        .status-keluar {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Laporan Aktivitas Terbaru</h1>
    <p>Tanggal: <?= $tanggal ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Transaksi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tipe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aktivitas as $item): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                <td><?= $item['no_transaksi'] ?></td>
                <td><?= $item['kode_barang'] ?></td>
                <td><?= $item['nama_barang'] ?></td>
                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                <td><?= number_format($item['jumlah']) ?></td>
                <td><?= $item['satuan'] ?></td>
                <td><?= ucfirst($item['tipe']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html> 