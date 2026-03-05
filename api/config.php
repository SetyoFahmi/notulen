<?php
// Mengambil kredensial dengan fallback string kosong jika tidak ada
$host     = getenv('POSTGRES_HOST') ?: '';
$db_name  = getenv('POSTGRES_DATABASE') ?: '';
$user     = getenv('POSTGRES_USER') ?: '';
$password = getenv('POSTGRES_PASSWORD') ?: '';

// Cek apakah variabel penting sudah terisi
if (empty($host) || empty($db_name)) {
    die("Error: Environment Variables (POSTGRES_HOST / DATABASE) belum diatur di Dashboard Vercel.");
}

try {
    // Pastikan format DSN benar: pgsql:host=...;port=5432;dbname=...
    $dsn = "pgsql:host=$host;port=5432;dbname=$db_name";
    $pdo = new PDO($dsn, $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // SQL Create Table (Tetap seperti sebelumnya)
    $sql = "CREATE TABLE IF NOT EXISTS notulen (
        id SERIAL PRIMARY KEY,
        judul VARCHAR(200) NOT NULL,
        tanggal DATE NOT NULL,
        waktu_mulai TIME,
        waktu_selesai TIME,
        tempat VARCHAR(200),
        pimpinan_rapat VARCHAR(100),
        notulis VARCHAR(100),
        peserta TEXT,
        agenda TEXT,
        pembahasan TEXT,
        kesimpulan TEXT,
        tindak_lanjut TEXT,
        gambar TEXT,
        latar_belakang TEXT,
        dibuat_nama TEXT,
        dibuat_nip TEXT,
        diperiksa_nama TEXT,
        diperiksa_nip TEXT,
        disahkan_nama TEXT,
        disahkan_nip TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

} catch (PDOException $e) {
    // Tampilkan error spesifik untuk mempermudah perbaikan
    die("Detail Error Koneksi: " . $e->getMessage());
}
?>