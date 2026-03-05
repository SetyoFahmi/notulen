<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT * FROM notulen ORDER BY tanggal DESC, waktu_mulai DESC");
$notulen = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Notulen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
<div class="container mt-4">
    <h2>Daftar Notulen</h2>
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Notulen</a>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <?php if (empty($notulen)): ?>
        <div class="alert alert-info">Belum ada notulen.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>No</th><th>Judul</th><th>Tanggal</th><th>Tempat</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php $no=1; foreach ($notulen as $row): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['tanggal']) ?></td>
            <td><?= htmlspecialchars($row['tempat']) ?></td>
            <td>
                <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>