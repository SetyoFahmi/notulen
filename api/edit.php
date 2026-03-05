<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

$id = $_GET['id'] ?? 0;
if (!$id) die("ID tidak valid.");

$stmt = $pdo->prepare("SELECT * FROM notulen WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) die("Data tidak ditemukan.");

// Pastikan folder uploads ada
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    if (empty($judul) || empty($tanggal)) {
        $error = "Judul dan tanggal harus diisi!";
    } else {
        // Upload gambar baru jika ada
        $gambar = $data['gambar']; // tetap pakai lama
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $gambar = 'notulen_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar)) {
                    // Hapus gambar lama jika ada
                    if ($data['gambar'] && file_exists($upload_dir . $data['gambar'])) {
                        unlink($upload_dir . $data['gambar']);
                    }
                } else {
                    $error = "Gagal mengupload gambar.";
                }
            } else {
                $error = "Tipe file tidak diizinkan. Hanya gambar.";
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE notulen SET 
                    judul=?, tanggal=?, waktu_mulai=?, waktu_selesai=?, tempat=?, pimpinan_rapat=?, notulis=?,
                    peserta=?, agenda=?, latar_belakang=?, pembahasan=?, kesimpulan=?, tindak_lanjut=?, gambar=?,
                    dibuat_nama=?, dibuat_nip=?, diperiksa_nama=?, diperiksa_nip=?, disahkan_nama=?, disahkan_nip=?
                    WHERE id=?");
                
                $stmt->execute([
                    $judul,
                    $tanggal,
                    $_POST['waktu_mulai'] ?? '',
                    $_POST['waktu_selesai'] ?? '',
                    $_POST['tempat'] ?? '',
                    $_POST['pimpinan_rapat'] ?? '',
                    $_POST['notulis'] ?? '',
                    $_POST['peserta'] ?? '',
                    $_POST['agenda'] ?? '',
                    $_POST['latar_belakang'] ?? '',
                    $_POST['pembahasan'] ?? '',
                    $_POST['kesimpulan'] ?? '',
                    $_POST['tindak_lanjut'] ?? '',
                    $gambar,
                    $_POST['dibuat_nama'] ?? '',
                    $_POST['dibuat_nip'] ?? '',
                    $_POST['diperiksa_nama'] ?? '',
                    $_POST['diperiksa_nip'] ?? '',
                    $_POST['disahkan_nama'] ?? '',
                    $_POST['disahkan_nip'] ?? '',
                    $id
                ]);
                
                header("Location: index.php?success=Notulen diperbarui");
                exit;
            } catch (PDOException $e) {
                $error = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Notulen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css">
    <script src="https://js.puter.com/v2/"></script>
</head>
<body>
<div class="container mt-4 mb-5">
    <h2>Edit Notulen</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="judul" class="form-label">Judul Rapat *</label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($data['judul']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal" class="form-label">Tanggal *</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="waktu_mulai">Waktu Mulai</label>
                <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" value="<?= htmlspecialchars($data['waktu_mulai']) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="waktu_selesai">Waktu Selesai</label>
                <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" value="<?= htmlspecialchars($data['waktu_selesai']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tempat">Tempat</label>
                <input type="text" class="form-control" id="tempat" name="tempat" value="<?= htmlspecialchars($data['tempat']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="pimpinan_rapat">Pimpinan Rapat</label>
                <input type="text" class="form-control" id="pimpinan_rapat" name="pimpinan_rapat" value="<?= htmlspecialchars($data['pimpinan_rapat']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="notulis">Notulis</label>
                <input type="text" class="form-control" id="notulis" name="notulis" value="<?= htmlspecialchars($data['notulis']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="peserta">Peserta (satu nama per baris)</label>
            <textarea class="form-control" id="peserta" name="peserta" rows="4"><?= htmlspecialchars($data['peserta']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="latar_belakang">Latar Belakang</label>
            <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="4"><?= htmlspecialchars($data['latar_belakang'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="pembahasan">Pembahasan</label>
            <div class="input-group">
                <textarea class="form-control" id="pembahasan" name="pembahasan" rows="5"><?= htmlspecialchars($data['pembahasan']) ?></textarea>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('pembahasan', 'koreksi')">✍️ Koreksi</button>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('pembahasan', 'kembangkan')">✨ Kembangkan</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="kesimpulan">Kesimpulan</label>
            <div class="input-group">
                <textarea class="form-control" id="kesimpulan" name="kesimpulan" rows="3"><?= htmlspecialchars($data['kesimpulan']) ?></textarea>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('kesimpulan', 'koreksi')">✍️ Koreksi</button>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('kesimpulan', 'kembangkan')">✨ Kembangkan</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="tindak_lanjut">Tindak Lanjut</label>
            <textarea class="form-control" id="tindak_lanjut" name="tindak_lanjut" rows="3"><?= htmlspecialchars($data['tindak_lanjut'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="gambar">Upload Gambar (kosongkan jika tidak ingin mengganti)</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            <?php if ($data['gambar']): ?>
                <div class="mt-2">
                    <img src="uploads/<?= htmlspecialchars($data['gambar']) ?>" style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>
        
        <h4 class="mt-4">Penandatangan</h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="dibuat_nama">Dibuat - Nama</label>
                <input type="text" class="form-control" id="dibuat_nama" name="dibuat_nama" value="<?= htmlspecialchars($data['dibuat_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="dibuat_nip">Dibuat - NIP</label>
                <input type="text" class="form-control" id="dibuat_nip" name="dibuat_nip" value="<?= htmlspecialchars($data['dibuat_nip'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="diperiksa_nama">Diperiksa - Nama</label>
                <input type="text" class="form-control" id="diperiksa_nama" name="diperiksa_nama" value="<?= htmlspecialchars($data['diperiksa_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="diperiksa_nip">Diperiksa - NIP</label>
                <input type="text" class="form-control" id="diperiksa_nip" name="diperiksa_nip" value="<?= htmlspecialchars($data['diperiksa_nip'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="disahkan_nama">Disahkan - Nama</label>
                <input type="text" class="form-control" id="disahkan_nama" name="disahkan_nama" value="<?= htmlspecialchars($data['disahkan_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="disahkan_nip">Disahkan - NIP</label>
                <input type="text" class="form-control" id="disahkan_nip" name="disahkan_nip" value="<?= htmlspecialchars($data['disahkan_nip'] ?? '') ?>">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="detail.php?id=<?= $id ?>" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script>
function prosesAI(fieldId, action) {
    const textarea = document.getElementById(fieldId);
    const text = textarea.value.trim();
    if (!text) {
        alert('Teks masih kosong!');
        return;
    }
    const button = event.target;
    const originalText = button.innerText;
    button.disabled = true;
    button.innerText = 'Memproses...';

    let prompt = "";
    let options = {
        model: 'gpt-4o',
        temperature: 0.7,
        max_tokens: 1000
    };
    if (action === 'koreksi') {
        prompt = `Perbaiki tata bahasa dan ejaan teks berikut. Hasilnya harus sama persis secara konteks, hanya perbaiki kesalahan:\n\n${text}`;
        options.temperature = 0.3;
    } else if (action === 'kembangkan') {
        prompt = `Kembangkan teks berikut menjadi notulen rapat yang formal, terstruktur, dan kaya informasi dengan mempertahankan ide utama. Susun notulen tersebut ke dalam tiga bagian utama:

1. Latar Belakang: Jelaskan secara naratif maksud dan tujuan dari pertemuan ini berdasarkan konteks yang ada.
2. Pembahasan: Uraikan poin-poin diskusi secara mendetail, logis, dan mengalir, mencakup argumen atau data yang muncul dalam teks.
3. Kesimpulan: Rangkum hasil akhir, keputusan yang diambil, serta tindak lanjut (action items) yang diperlukan.

Gunakan bahasa Indonesia yang profesional, koheren, dan mudah dipahami.

pertahankan ide utama:
${text}`;

        options.temperature = 0.8;
        options.max_tokens = 1500;
    } else {
        prompt = `Rapikan teks berikut:\n\n${text}`;
    }

    puter.ai.chat(prompt, options)
    .then(response => {
        textarea.value = response;
    })
    .catch(error => {
        alert('Gagal: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.innerText = originalText;
    });
}
</script>
</body>
</html>