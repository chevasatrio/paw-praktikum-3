<?php
require_once 'config/database.php'; // [cite: 819]
require_once 'classes/Produk.php'; // [cite: 820]

// TODO: Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; // [cite: 521, 522, 822]

// TODO: Buat object produk
$produkObj = new Produk(); // [cite: 824]

// TODO: Ambil data produk existing
$data = $produkObj->readOne($id); // [cite: 523, 825]

// Redirect jika produk tidak ditemukan
if (!$data) { // [cite: 826]
    header("Location: index.php"); // [cite: 827]
    exit(); // [cite: 827]
}

$message = ''; // [cite: 829]
$error = ''; // [cite: 830]

// TODO: Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // [cite: 441, 832]
    // Set data ke object
    $produkObj->setNama($_POST['nama']); // [cite: 443, 833]
    $produkObj->setDeskripsi($_POST['deskripsi']);
    $produkObj->setHarga($_POST['harga']);

    // Variabel untuk menyimpan nama foto baru/lama
    $foto_untuk_update = $data['foto'];

    // Handle upload foto (jika ada foto baru)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) { // [cite: 369, 834, 938]
        // Upload foto baru
        $new_foto = $produkObj->uploadFoto($_FILES['foto']); // [cite: 444, 940]

        if ($new_foto) {
            // Hapus foto lama jika ada
            if ($data['foto'] && file_exists('uploads/' . $data['foto'])) { // [cite: 943]
                unlink('uploads/' . $data['foto']); // [cite: 945]
            }
            // Set foto baru
            $foto_untuk_update = $new_foto; // [cite: 948]
        } else {
            $error = "Gagal upload foto. Pastikan file adalah gambar (JPG, PNG, GIF) dan ukuran < 2MB"; // [cite: 374, 375]
        }
    } else {
        // Tetap pakai foto lama jika tidak ada upload foto baru
        $foto_untuk_update = $data['foto']; // [cite: 951, 952]
    }

    // Set foto yang akan digunakan
    $produkObj->setFoto($foto_untuk_update);

    // Panggil method update
    if (empty($error)) {
        if ($produkObj->update($id)) { // [cite: 835]
            $message = "Produk berhasil diupdate!";
            // Redirect ke detail.php setelah berhasil
            header("refresh:2;url=detail.php?id=" . $id); // [cite: 836]
        } else {
            $error = "Gagal mengupdate produk!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1> Edit Produk</h1> <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div> <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div> <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="nama">Nama Produk: </label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi"
                    rows="5"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp):</label>
                <input type="number" id="harga" name="harga" step="0.01" value="<?php echo $data['harga']; ?>" required>
            </div>

            <div class="form-group">
                <label>Foto saat ini:</label> <?php if ($data['foto']): ?> <img
                        src="uploads/<?php echo $data['foto']; ?>"
                        style="max-width: 200px; display: block; margin: 10px 0;"> <?php else: ?>
                    <p>Tidak ada foto saat ini.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="foto">Ubah Foto Produk: </label>
                <input type="file" id="foto" name="foto" accept="image/*">
                <small>Kosongkan jika tidak ingin mengubah. Format: JPG, PNG, GIF. Maksimal 2MB</small>
            </div>

            <div class="form-actions"> <button type="submit" class="btn btn-primary">Update</button> <a
                    href="detail.php?id=<?php echo $id; ?>" class="btn btn-secondary"> Batal</a> </div>
        </form>
    </div>
</body>

</html>