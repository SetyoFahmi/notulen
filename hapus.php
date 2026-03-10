<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;

if ($id <= 0) {
    header("Location: index.php?error=ID tidak valid");
    exit;
}

try {
    // Ambil data gambar sebelum dihapus
    $stmt = $pdo->prepare("SELECT gambar FROM notulen WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        // Hapus file gambar jika ada
        if (!empty($data['gambar'])) {
            $gambar_path = __DIR__ . '/uploads/' . $data['gambar'];
            if (file_exists($gambar_path)) {
                unlink($gambar_path);
            }
        }

        // Hapus data dari database
        $stmt = $pdo->prepare("DELETE FROM notulen WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php?success=Notulen berhasil dihapus");
    } else {
        header("Location: index.php?error=Data tidak ditemukan");
    }
} catch (PDOException $e) {
    header("Location: index.php?error=Gagal menghapus: " . $e->getMessage());
}
exit;
?>