<?php
require_once 'config/database.php'; // [cite: 882]
require_once 'classes/Produk.php'; // [cite: 883]

// TODO: Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // [cite: 802, 885]

// Pastikan ID valid
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// TODO: Buat object produk
$produkObj = new Produk(); // [cite: 887]

// TODO: Panggil method delete
if ($produkObj->delete($id)) { // [cite: 803, 889]
    // Berhasil
    header("Location: index.php?message=deleted"); // [cite: 804, 892]
    exit(); // [cite: 893]
} else {
    // Gagal
    header("Location: detail.php?id=" . $id . "&error=delete_failed"); // [cite: 896, 897, 898]
    exit();
}
?>