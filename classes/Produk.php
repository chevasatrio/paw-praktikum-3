<?php
require_once 'Database.php';
/**
 * Class Produk
 * Menangani operasi CRUD untuk produk
 */
class Produk
{
  // Properties
  private $db;
  private $conn;
  // Properties produk
  private $id;
  private $nama;
  private $deskripsi;
  private $harga;
  private $foto;
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->db = new Database();
    $this->conn = $this->db->getConnection();
  }
  /**
   * Setter Methods
   * Untuk mengisi nilai properties dari luar class
   */
  public function setNama($nama)
  {
    $this->nama = $nama;
  }
  public function setDeskripsi($deskripsi)
  {
    $this->deskripsi = $deskripsi;
  }
  public function setHarga($harga)
  {
    $this->harga = $harga;
  }
  public function setFoto($foto)
  {
    $this->foto = $foto;
  }
  /**
   * Getter Methods
   * Untuk mendapatkan nilai properties dari luar class
   */
  public function getId()
  {
    return $this->id;
  }
  public function getNama()
  {
    return $this->nama;
  }
  public function getDeskripsi()
  {
    return $this->deskripsi;
  }
  public function getHarga()
  {
    return $this->harga;
  }
  public function getFoto()
  {
    return $this->foto;
  }
  /**
   * CREATE: Tambah produk baru
   */
  public function create()
  {
    $query = "INSERT INTO produk (nama, deskripsi, harga, foto)
VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param(
      "ssds",
      $this->nama,
      $this->deskripsi,
      $this->harga,
      $this->foto
    );
    if ($stmt->execute()) {
      return true;
    }
    return false;
  }
  /**
   * READ: Ambil semua produk
   */
  public function readAll()
  {
    $query = "SELECT * FROM produk ORDER BY created_at DESC";
    $result = $this->conn->query($query);
    return $result;
  }
  /**
   * READ: Ambil satu produk berdasarkan ID
   */
  public function readOne($id)
  {
    $query = "SELECT * FROM produk WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
  }
  /**
   * Method untuk upload foto
   */
  public function uploadFoto($file)
  {
    $target_dir = "uploads/";
    $file_extension = strtolower(pathinfo(
      $file["name"],
      PATHINFO_EXTENSION
    ));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    // Validasi file
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($file_extension, $allowed_types)) {
      return false;
    }
    // Validasi ukuran (max 2MB)
    if ($file["size"] > 2000000) {
      return false;
    }
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
      return $new_filename;
    }
    return false;
  }
  /**
   * UPDATE: Edit produk
   */
  public function update($id)
  {
    // Query UPDATE dengan prepared statement
    // HINT: UPDATE produk SET nama=?, deskripsi=?, harga=?, foto=? WHERE id=?
    $query = "UPDATE produk SET 
                nama = ?, 
                deskripsi = ?, 
                harga = ?, 
                foto = ? 
              WHERE id = ?";

    $stmt = $this->conn->prepare($query);

    // Bind parameter: s = string, d = double (harga), i = integer (id)
    // Urutan: nama, deskripsi, harga, foto, id
    $stmt->bind_param(
      "ssdsi",
      $this->nama,
      $this->deskripsi,
      $this->harga,
      $this->foto,
      $id
    );

    // Eksekusi statement dan return hasilnya (true/false)
    if ($stmt->execute()) {
      return true;
    }
    return false;
  }

  /**
   * DELETE: Hapus produk
   */
  public function delete($id)
  {
    // 1. Ambil data dulu untuk mendapatkan nama file foto
    $data = $this->readOne($id); // [cite: 791, 920]

    // 2. Hapus file foto jika ada
    if ($data['foto'] && file_exists('uploads/' . $data['foto'])) { // [cite: 792, 922]
      unlink('uploads/' . $data['foto']); // [cite: 797, 798, 925]
    }

    // 3. Hapus record dari database
    $query = "DELETE FROM produk WHERE id=?"; // [cite: 793, 796, 928]
    $stmt = $this->conn->prepare($query); // [cite: 929]
    $stmt->bind_param("i", $id); // [cite: 930]

    // 4. Return true jika berhasil, false jika gagal
    return $stmt->execute(); // [cite: 794, 931]
  }


}
