<?php
include "../config.php";
session_start();

if ($_SESSION['username'] == '') {
  header("Location: ../login.php");
  exit();
}

$id_pendaftar = $_GET['konfirmasi'];
$pendaftar = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE id_pendaftaran='$id_pendaftar'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pendaftar</title>
  <link href="../public/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">Upload Surat Balasan (PDF)</div>
      <div class="card-body">
        <?php foreach($pendaftar as $row){ ?>
        <form action="prosesupload.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id_pendaftar" value="<?php echo $id_pendaftar; ?>">
          <div class="mb-3">
            <label for="file_pdf" class="form-label">Upload File PDF</label>
            <input type="file" class="form-control" name="file_pdf" id="file_pdf" accept="application/pdf" required>
          </div>
          <button type="submit" class="btn btn-success" name="konfirmasi">Konfirmasi</button>
          <a href="pendaftar.php" class="btn btn-secondary">Batal</a>
        </form>
        <?php } ?>
      </div>
    </div>
  </div>
  <script src="../public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
