<?php
require_once __DIR__ . '/config.php';
$host     = getenv('POSTGRES_HOST');
$db_name  = getenv('POSTGRES_DATABASE');
$user     = getenv('POSTGRES_USER');
$password = getenv('POSTGRES_PASSWORD');

try {
    // Koneksi menggunakan driver PostgreSQL (pgsql)
    $dsn = "pgsql:host=$host;port=5432;dbname=$db_name;";
    $pdo = new PDO($dsn, $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Otomatis membuat tabel 'notulen' di cloud jika belum ada
    $sql = "
    CREATE TABLE IF NOT EXISTS notulen (
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
    error_log("Gagal konek database: " . $e->getMessage());
    die("Sistem sedang maintenance. Silakan coba lagi nanti.");
}
?>