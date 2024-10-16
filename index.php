<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'tambah':
                $gambar = uploadGambar($_FILES['gambar']);
                tambahBuku($_POST['judul'], $_POST['pengarang'], $_POST['kategori'], $_POST['status'], $gambar);
                break;
            case 'update':
                $gambar = ($_FILES['gambar']['size'] > 0) ? uploadGambar($_FILES['gambar']) : $_POST['gambar_lama'];
                editBuku($_POST['id'], $_POST['judul'], $_POST['pengarang'], $_POST['kategori'], $_POST['status'], $gambar);
                break;
            case 'hapus':
                hapusBuku($_POST['id']);
                break;
        }
    }
    header('Location: index.php');
    exit;
}

$buku_edit = null;
if (isset($_GET['edit'])) {
    $buku_edit = ambilBuku($_GET['edit']);
}

buatTabel();
$buku = ambilSemuaBuku();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Buku Perpustakaan</title>
    <link rel="stlesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Pengelolaan Buku Perpustakaan</h1>
        
        <!-- Form Tambah/Edit -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo $buku_edit ? 'update' : 'tambah'; ?>">
            <?php if ($buku_edit): ?>
                <input type="hidden" name="id" value="<?php echo $buku_edit['id']; ?>">
                <input type="hidden" name="gambar_lama" value="<?php echo $buku_edit['gambar_buku']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" value="<?php echo $buku_edit ? htmlspecialchars($buku_edit['judul_buku']) : ''; ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="pengarang">Pengarang</label>
                <input type="text" id="pengarang" name="pengarang" value="<?php echo $buku_edit ? htmlspecialchars($buku_edit['pengarang']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" id="kategori" name="kategori" value="<?php echo $buku_edit ? htmlspecialchars($buku_edit['kategori']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="Tersedia" <?php echo ($buku_edit && $buku_edit['status'] == 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                    <option value="Tidak Tersedia" <?php echo ($buku_edit && $buku_edit['status'] == 'Tidak Tersedia') ? 'selected' : ''; ?>>Tidak Tersedia</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gambar">Gambar Buku</label>
                <input type="file" id="gambar" name="gambar" accept="image/*" <?php echo $buku_edit ? '' : 'required'; ?>>
            </div>
            
            <button type="submit"><?php echo $buku_edit ? 'Update' : 'Tambah'; ?> Buku</button>
        </form>

        <!-- Tabel Daftar Buku -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Gambar</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buku as $b): ?>
                    <tr>
                        <td data-label="Judul Buku"><?php echo htmlspecialchars($b['judul_buku']); ?></td>
                        <td data-label="Pengarang"><?php echo htmlspecialchars($b['pengarang']); ?></td>
                        <td data-label="Gambar"><img src="<?php echo $b['gambar_buku']; ?>" alt="Gambar Buku" class="buku-gambar"></td>
                        <td data-label="Kategori"><?php echo htmlspecialchars($b['kategori']); ?></td>
                        <td data-label="Status"><?php echo $b['status']; ?></td>
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <a href="?edit=<?php echo $b['id']; ?>"><button type="button">Edit</button></a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" class="hapus" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>