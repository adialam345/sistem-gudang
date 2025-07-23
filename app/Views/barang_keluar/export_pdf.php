<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Barang Keluar - <?= $tanggal ?></title>
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
        .filter-info {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
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
            font-size: 14px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Daftar Barang Keluar</h1>
    
    <div class="filter-info">
        <p>Tanggal: <?= $tanggal ?></p>
        <?php if ($tanggal_awal && $tanggal_akhir): ?>
        <p>Periode: <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></p>
        <?php endif; ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No Transaksi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Penerima</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($barangKeluar as $item): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                <td><?= $item['no_transaksi'] ?></td>
                <td><?= $item['kode_barang'] ?></td>
                <td><?= $item['nama_barang'] ?></td>
                <td><?= number_format($item['jumlah']) ?></td>
                <td><?= $item['satuan'] ?></td>
                <td><?= $item['penerima'] ?></td>
                <td><?= $item['keterangan'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html> 