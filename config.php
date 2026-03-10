<?php


// 1. Ambil kredensial dari Environment Variables Vercel
$host     = $_ENV['POSTGRES_HOST'] ?? getenv('POSTGRES_HOST');
$db_name  = $_ENV['POSTGRES_DATABASE'] ?? getenv('POSTGRES_DATABASE');
$user     = $_ENV['POSTGRES_USER'] ?? getenv('POSTGRES_USER');
$password = $_ENV['POSTGRES_PASSWORD'] ?? getenv('POSTGRES_PASSWORD');

// 2. Fallback: Jika variabel satuan kosong, coba parsing dari POSTGRES_URL (Fitur Otomatis Vercel)
if (empty($host) && (getenv('POSTGRES_URL') || isset($_ENV['POSTGRES_URL']))) {
    $url = parse_url(getenv('POSTGRES_URL') ?: $_ENV['POSTGRES_URL']);
    $host     = $url['host'] ?? '';
    $db_name  = ltrim($url['path'] ?? '', '/');
    $user     = $url['user'] ?? '';
    $password = $url['pass'] ?? '';
}

// 3. Validasi akhir sebelum mencoba koneksi
if (empty($host)) {
    die("Detail Error Koneksi: Variabel database (HOST) tidak ditemukan. Pastikan sudah mengisi Environment Variables di Dashboard Vercel dan melakukan REDEPLOY.");
}

try {
    // 4. Inisialisasi Koneksi PDO PostgreSQL
    $dsn = "pgsql:host=$host;port=5432;dbname=$db_name;sslmode=require";
    $pdo = new PDO($dsn, $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 5. Otomatis buat tabel 'notulen' jika belum ada di cloud
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
    // Menampilkan pesan error spesifik jika koneksi gagal
    die("Detail Error Koneksi: " . $e->getMessage());
}
?>