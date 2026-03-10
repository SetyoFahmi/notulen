<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config.php';

$id = $_GET['id'] ?? 0;
if (!$id) die("ID tidak valid.");

$stmt = $pdo->prepare("SELECT * FROM notulen WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) die("Data tidak ditemukan.");

// Format tanggal Indonesia
function tgl_indo($tanggal) {
    if (empty($tanggal)) return '';
    $pecah = explode('-', $tanggal);
    if (count($pecah) != 3) return $tanggal;
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

$hari = date('l', strtotime($data['tanggal']));
$hari_indonesia = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari = $hari_indonesia[$hari] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Notulen</title>
    <link rel="stylesheet" href="static/css/detail.css">
</head>
<body>
<div class="notulen-container">
    <div class="header-logo">
        <img src="static/image/logo.png" alt="Logo BBPVP Medan">
    </div>

     <h1>NOTULEN RAPAT</h1>

    <!-- Informasi Rapat -->
    <div class="info-rapat">
        <div class="row"><span class="label">Hari/Tanggal :</span> <span class="value"><?= $hari ?>, <?= tgl_indo($data['tanggal']) ?></span></div>
        <div class="row"><span class="label">Waktu :</span> <span class="value"><?= htmlspecialchars($data['waktu_mulai']) ?> WIB s.d. <?= htmlspecialchars($data['waktu_selesai'] ?: 'selesai') ?></span></div>
        <div class="row"><span class="label">Tempat :</span> <span class="value"><?= htmlspecialchars($data['tempat']) ?></span></div>
        <div class="row"><span class="label">Pimpinan :</span> <span class="value"><?= htmlspecialchars($data['pimpinan_rapat']) ?></span></div>
        <div class="row"><span class="label">Agenda :</span> <span class="value"><?= nl2br(htmlspecialchars($data['agenda'])) ?></span></div>
    </div>

    <!-- A. PESERTA RAPAT -->
    <div class="section-title">A. PESERTA RAPAT</div>
    <div class="peserta-text">
        <?php
        $peserta = explode("\n", trim($data['peserta']));
        if (!empty($peserta) && $peserta[0] != '') {
            echo "<ol>";
            foreach ($peserta as $p) {
                if (trim($p) != '') {
                    echo "<li>" . htmlspecialchars(trim($p)) . "</li>";
                }
            }
            echo "</ol>";
        } else {
            echo "-";
        }
        ?>
    </div>
    
    <!-- B. LATAR BELAKANG RAPAT -->
    <?php if (!empty($data['latar_belakang'])): ?>
    <div class="section-title">B. LATAR BELAKANG RAPAT</div>
    <div class="latar-text"><?= nl2br(htmlspecialchars($data['latar_belakang'])) ?></div>
    <?php endif; ?>
    
    <!-- C. PEMBAHASAN -->
    <?php if (!empty($data['pembahasan'])): ?>
    <div class="section-title">C. PEMBAHASAN</div>
    <div class="pembahasan-text"><?= nl2br(htmlspecialchars($data['pembahasan'])) ?></div>
    <?php endif; ?>

    <!-- D. KESIMPULAN RAPAT -->
    <?php if (!empty($data['kesimpulan'])): ?>
    <div class="section-title">D. KESIMPULAN RAPAT</div>
    <div class="kesimpulan-text"><?= nl2br(htmlspecialchars($data['kesimpulan'])) ?></div>
    <?php endif; ?>

    <!-- E. GAMBAR DOKUMENTASI -->
    <?php if (!empty($data['gambar'])): ?>
    <div class="section-title">E. DOKUMENTASI</div>
    <div style="text-align: center; margin: 20px 0;">
        <img src="uploads/<?= htmlspecialchars($data['gambar']) ?>" style="max-width: 100%; max-height: 400px; border: 1px solid #ccc; padding: 5px;">
    </div>
    <?php endif; ?>

    <!-- Signature Table -->
    <table class="signature-table">
        <tr><th>DIBUAT</th><th>DIPERIKSA</th><th>DISAHKAN</th></tr>
        <tr><td class="signature-space"></td><td class="signature-space"></td><td class="signature-space"></td></tr>
        <tr >
            <td class="bold-text"><?= htmlspecialchars($data['dibuat_nama'] ?? '') ?><br><?= !empty($data['dibuat_nip']) ? 'NIP. '.htmlspecialchars($data['dibuat_nip']) : '' ?></td>
            <td><?= htmlspecialchars($data['diperiksa_nama'] ?? '') ?><br><?= !empty($data['diperiksa_nip']) ? 'NIP. '.htmlspecialchars($data['diperiksa_nip']) : '' ?></td>
            <td><?= htmlspecialchars($data['disahkan_nama'] ?? '') ?><br><?= !empty($data['disahkan_nip']) ? 'NIP. '.htmlspecialchars($data['disahkan_nip']) : '' ?></td>
        </tr>
        
    </table>
</div>

<!-- Tombol navigasi (tidak ikut print) -->
<div class="no-print" style="text-align:center; margin-top:20px;">
    <a href="edit.php?id=<?= $id ?>">Edit</a> | <a href="index.php">Kembali</a> | <button onclick="window.print()">Cetak</button>
</div>

<!-- Footer nomor halaman (hanya tampil saat print) -->
<div class="page-number"></div>
</body>
</html>