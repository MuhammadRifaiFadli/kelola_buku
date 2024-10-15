
<?php
function koneksiDatabase() {
    $db = new SQLite3('perpustakaan.db');
    return $db;
}

function buatTabel() {
    $db = koneksiDatabase();
    $db->exec('CREATE TABLE IF NOT EXISTS buku (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        judul_buku TEXT,
        pengarang TEXT,
        kategori TEXT,
        status TEXT,
        gambar_buku TEXT
    )');
}

function tambahBuku($judul, $pengarang, $kategori, $status, $gambar) {
    $db = koneksiDatabase();
    $stmt = $db->prepare('INSERT INTO buku (judul_buku, pengarang, kategori, status, gambar_buku) VALUES (:judul, :pengarang, :kategori, :status, :gambar)');
    $stmt->bindValue(':judul', $judul, SQLITE3_TEXT);
    $stmt->bindValue(':pengarang', $pengarang, SQLITE3_TEXT);
    $stmt->bindValue(':kategori', $kategori, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':gambar', $gambar, SQLITE3_TEXT);
    return $stmt->execute();
}

function ambilSemuaBuku() {
    $db = koneksiDatabase();
    $result = $db->query('SELECT * FROM buku');
    $buku = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $buku[] = $row;
    }
    return $buku;
}

function ambilBuku($id) {
    $db = koneksiDatabase();
    $stmt = $db->prepare('SELECT * FROM buku WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

function editBuku($id, $judul, $pengarang, $kategori, $status, $gambar) {
    $db = koneksiDatabase();
    $stmt = $db->prepare('UPDATE buku SET judul_buku = :judul, pengarang = :pengarang, kategori = :kategori, status = :status, gambar_buku = :gambar WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->bindValue(':judul', $judul, SQLITE3_TEXT);
    $stmt->bindValue(':pengarang', $pengarang, SQLITE3_TEXT);
    $stmt->bindValue(':kategori', $kategori, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':gambar', $gambar, SQLITE3_TEXT);
    return $stmt->execute();
}

function hapusBuku($id) {
    $db = koneksiDatabase();
    $stmt = $db->prepare('DELETE FROM buku WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function uploadGambar($file) {
    $target_dir = "gambar_buku/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        die("File is not an image.");
    }
    
    // Check file size
    if ($file["size"] > 500000) {
        die("Sorry, your file is too large.");
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        die("Sorry, there was an error uploading your file.");
    }
}