<?php
require_once __DIR__ . '/config.php';

// Pastikan folder uploads ada
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    
    if (empty($judul) || empty($tanggal)) {
        $error = "Judul dan tanggal harus diisi!";
    } else {
        // Upload gambar
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $gambar = 'notulen_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar);
            } else {
                $error = "Tipe file tidak diizinkan. Hanya gambar (jpg, jpeg, png, gif, webp).";
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO notulen (
                judul, tanggal, waktu_mulai, waktu_selesai, tempat, pimpinan_rapat, notulis, 
                peserta, agenda, latar_belakang, pembahasan, kesimpulan, gambar, 
                dibuat_nama, dibuat_nip, diperiksa_nama, diperiksa_nip, disahkan_nama, disahkan_nip
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            
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
                $gambar,
                $_POST['dibuat_nama'] ?? '',
                $_POST['dibuat_nip'] ?? '',
                $_POST['diperiksa_nama'] ?? '',
                $_POST['diperiksa_nip'] ?? '',
                $_POST['disahkan_nama'] ?? '',
                $_POST['disahkan_nip'] ?? ''
            ]);
            
            header("Location: index.php?success=Notulen berhasil ditambahkan");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Notulen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/notulen_form.css">
    <script src="https://js.puter.com/v2/"></script>
</head>
<body>
<div class="container mt-4 mb-5">
    <h2>Tambah Notulen</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="judul" class="form-label">Judul Rapat *</label>
                <input type="text" class="form-control" id="judul" name="judul" required value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal" class="form-label">Tanggal *</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="waktu_mulai">Waktu Mulai</label>
                <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" value="<?= htmlspecialchars($_POST['waktu_mulai'] ?? '') ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="waktu_selesai">Waktu Selesai</label>
                <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" value="<?= htmlspecialchars($_POST['waktu_selesai'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tempat">Tempat</label>
                <input type="text" class="form-control" id="tempat" name="tempat" value="<?= htmlspecialchars($_POST['tempat'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="pimpinan_rapat">Pimpinan Rapat</label>
                <input type="text" class="form-control" id="pimpinan_rapat" name="pimpinan_rapat" value="<?= htmlspecialchars($_POST['pimpinan_rapat'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="notulis">Notulis</label>
                <input type="text" class="form-control" id="notulis" name="notulis" value="<?= htmlspecialchars($_POST['notulis'] ?? '') ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="peserta">Peserta (satu nama per baris)</label>
            <textarea class="form-control" id="peserta" name="peserta" rows="4" placeholder="Contoh:&#10;KABAG&#10;Koordinator Pemberdayaan&#10;koordinator Intala&#10;Instruktur BBPVP"><?= htmlspecialchars($_POST['peserta'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="latar_belakang">Latar Belakang</label>
            <textarea class="form-control" id="latar_belakang" name="latar_belakang" rows="4" placeholder="Isi latar belakang rapat..."><?= htmlspecialchars($_POST['latar_belakang'] ?? '') ?></textarea>
        </div>

        <!-- Tombol Kembangkan Lengkap (diletakkan setelah latar belakang) -->
        <div class="mb-3">
            <button type="button" class="btn btn-success" onclick="kembangkanLengkap(this)">✨ Kembangkan Notulen Lengkap</button>
            <small class="text-muted">Berdasarkan teks Latar Belakang</small>
        </div>

        <div class="mb-3">
            <label for="pembahasan">Pembahasan</label>
            <div class="input-group">
                <textarea class="form-control" id="pembahasan" name="pembahasan" rows="5"><?= htmlspecialchars($_POST['pembahasan'] ?? '') ?></textarea>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('pembahasan', 'koreksi')">✍️ Koreksi</button>
                <button type="button" class="btn btn-outline-secondary" onclick="prosesAI('pembahasan', 'kembangkan')">✨ Kembangkan</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="kesimpulan">Kesimpulan</label>
            <div class="input-group">
                <textarea class="form-control" id="kesimpulan" name="kesimpulan" rows="3"><?= htmlspecialchars($_POST['kesimpulan'] ?? '') ?></textarea>
                <!-- Opsional: tambahkan tombol koreksi/kembangkan untuk kesimpulan jika diinginkan -->
            </div>
        </div>
        <div class="mb-3">
            <label for="gambar">Upload Gambar (opsional)</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
        </div>
        
        <h4 class="mt-4">Penandatangan</h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="dibuat_nama">Dibuat - Nama</label>
                <input type="text" class="form-control" id="dibuat_nama" name="dibuat_nama" value="<?= htmlspecialchars($_POST['dibuat_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="dibuat_nip">Dibuat - NIP</label>
                <input type="text" class="form-control" id="dibuat_nip" name="dibuat_nip" value="<?= htmlspecialchars($_POST['dibuat_nip'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="diperiksa_nama">Diperiksa - Nama</label>
                <input type="text" class="form-control" id="diperiksa_nama" name="diperiksa_nama" value="<?= htmlspecialchars($_POST['diperiksa_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="diperiksa_nip">Diperiksa - NIP</label>
                <input type="text" class="form-control" id="diperiksa_nip" name="diperiksa_nip" value="<?= htmlspecialchars($_POST['diperiksa_nip'] ?? '') ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="disahkan_nama">Disahkan - Nama</label>
                <input type="text" class="form-control" id="disahkan_nama" name="disahkan_nama" value="<?= htmlspecialchars($_POST['disahkan_nama'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="disahkan_nip">Disahkan - NIP</label>
                <input type="text" class="form-control" id="disahkan_nip" name="disahkan_nip" value="<?= htmlspecialchars($_POST['disahkan_nip'] ?? '') ?>">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
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
        // Ekstrak teks dari respons (bisa berupa objek)
        let responseText = '';
        if (typeof response === 'string') {
            responseText = response;
        } else if (response && response.text) {
            responseText = response.text;
        } else if (response && response.message && response.message.content) {
            responseText = response.message.content;
        } else if (response && response.response) {
            responseText = response.response;
        } else {
            responseText = JSON.stringify(response);
        }
        textarea.value = responseText;
    })
    .catch(error => {
        alert('Gagal: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.innerText = originalText;
    });
}

function kembangkanLengkap(btn) {
    const latarText = document.getElementById('latar_belakang').value.trim();
    if (!latarText) {
        alert('Isi latar belakang terlebih dahulu!');
        return;
    }
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = 'Memproses...';

    const prompt = `Buatlah notulen rapat yang lengkap dan formal berdasarkan latar belakang berikut. Hasilkan dalam tiga bagian: LATAR BELAKANG, PEMBAHASAN, dan KESIMPULAN. Gunakan bahasa Indonesia yang baik dan profesional. Berikan output dengan format:

[LATAR_BELAKANG]
... teks latar belakang yang dikembangkan ...
[PEMBAHASAN]
... teks pembahasan ...
[KESIMPULAN]
... teks kesimpulan ...

Latar belakang: ${latarText}`;

    puter.ai.chat(prompt, {
        model: 'gpt-4o',
        temperature: 0.7,
        max_tokens: 2000
    })
    .then(response => {
        // Ekstrak teks dari respons
        let responseText = '';
        if (typeof response === 'string') {
            responseText = response;
        } else if (response && response.text) {
            responseText = response.text;
        } else if (response && response.message && response.message.content) {
            responseText = response.message.content;
        } else if (response && response.response) {
            responseText = response.response;
        } else {
            responseText = JSON.stringify(response);
        }

        // Parsing respons berdasarkan tag
        const regexLatar = /\[LATAR_BELAKANG\]([\s\S]*?)\[PEMBAHASAN\]/;
        const regexPembahasan = /\[PEMBAHASAN\]([\s\S]*?)\[KESIMPULAN\]/;
        const regexKesimpulan = /\[KESIMPULAN\]([\s\S]*?)$/;

        const matchLatar = responseText.match(regexLatar);
        const matchPembahasan = responseText.match(regexPembahasan);
        const matchKesimpulan = responseText.match(regexKesimpulan);

        if (matchLatar) {
            document.getElementById('latar_belakang').value = matchLatar[1].trim();
        }
        if (matchPembahasan) {
            document.getElementById('pembahasan').value = matchPembahasan[1].trim();
        }
        if (matchKesimpulan) {
            document.getElementById('kesimpulan').value = matchKesimpulan[1].trim();
        }

        // Jika gagal parsing, fallback: seluruh respons dimasukkan ke pembahasan
        if (!matchLatar && !matchPembahasan && !matchKesimpulan) {
            document.getElementById('pembahasan').value = responseText.trim();
        }
    })
    .catch(error => {
        alert('Gagal: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerText = originalText;
    });
}
</script>
</body>
</html>