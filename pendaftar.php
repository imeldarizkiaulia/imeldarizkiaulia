<?php
include "../config.php";
session_start();

if ($_SESSION['username'] == '') {
  header("Location: ../login.php");
}

$pendaftar_pending = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE status='pending'");
$pendaftar_aktif = mysqli_query($koneksi, "SELECT * FROM pendaftar WHERE status='aktif'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="../public/img/logoptpn.svg" rel="icon">
  <link href="../public/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../public/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap" rel="stylesheet">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">  
  <link rel="stylesheet" href="../public/css/style.css">

  <title>Dashboard | Pendaftar</title>
</head>
<body>
  <div class="fluid-container">
    <div class="row">
      <div class="col-2">
        <?php include "sidebar.php" ?>
      </div>
      <div class="col-10">
        <div class="col-12">
          <?php include "header.php" ?>
        </div>
        <div class="col-12 dashboard">
          <!-- DATA PENDAFTAR PENDING -->
          <div class="mx-3 mb-3 p-3 fw-bold daftar-body">
            <p style="color: #5a5a5a;"><i class="bi bi-list-ul me-1"></i> Data Pendaftar Belum Dikonfirmasi</p>
            <hr style="margin-top: -8px;">
            <table id="table_noconfirm" class="table" style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Instansi</th>
                  <th>Lampiran Surat</th>
                  <th>No Telp</th>
                  <th>Waktu</th>
                  <th>Opsi</th>
                </tr>
              </thead>
              <?php $no = 1; ?>
              <?php foreach($pendaftar_pending as $row){ ?>
                <tbody>
                  <tr>
                    <td><?php echo $no; ?>.</td>
                    <td><?php echo $row['instansi']; ?></td>
                    <td class="text-center">
                      <a href="../public/berkas/berkas-daftar/<?php echo $row['berkas']; ?>" target="_blank">
                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                      </a>
                    </td>
                    <td><?php echo $row['telpon']; ?></td>
                    <td><?php echo date('d-m-Y, H:i:s', strtotime($row['tgl_surat_masuk'])); ?></td>
                    <td>
                      <a href="prosespendaftar.php?hapus_pending=<?php echo $row['id_pendaftaran'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash3"></i></a>
                      <a href="konfirmasi_pendaftar.php?konfirmasi=<?php echo $row['id_pendaftaran'] ?>" class="btn btn-success fw-bold">Konfirmasi</a>
                    </td>
                  </tr>
                </tbody>
                <?php $no++; ?>
              <?php } ?>
            </table>
          </div>

          <!-- DATA PENDAFTAR SUDAH DIKONFIRMASI -->
          <div class="mx-3 p-3 fw-bold daftar-body">
            <p style="color: #5a5a5a;"><i class="bi bi-list-ul me-1"></i> Data Pendaftar Sudah Dikonfirmasi</p>
            <hr style="margin-top: -8px;">
            <table id="table_confirm" class="table" style="width:100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Instansi</th>
                  <th>Lampiran Surat</th>
                  <th>Tanggal Surat Masuk</th>
                  <th>Tanggal Konfirmasi</th>
                  <th>Surat Balasan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <?php $no = 1; ?>
              <?php foreach($pendaftar_aktif as $row_1){ ?>
                <tbody>
                  <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $row_1['instansi']; ?></td>
                    <td class="text-center">
                      <a href="../public/berkas/berkas-daftar/<?php echo $row_1['berkas']; ?>" target="_blank">
                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                      </a>
                    </td>
                    <td><?php echo date('d-m-Y, H:i:s', strtotime($row_1['tgl_surat_masuk'])); ?></td>
                    <td>
                      <?php echo isset($row_1['tgl_konfirmasi']) ? date('d-m-Y, H:i:s', strtotime($row_1['tgl_konfirmasi'])) : '-'; ?>
                    </td>
                    <td class="text-center">
                      <?php
                      $query_surat = mysqli_query($koneksi, "SELECT * FROM surat_balasan WHERE id_pendaftar = '".$row_1['id_pendaftaran']."'");
                      $data_surat = mysqli_fetch_assoc($query_surat);
                      if ($data_surat) {
                          echo '<a href="../public/berkas/surat-balasan/'.$data_surat['file_pdf'].'" target="_blank"><i class="bi bi-file-earmark-pdf text-primary fs-3"></i></a>';
                      } else {
                          echo 'Belum ada';
                      }
                      ?>
                    </td>
                    <td>
                      <a href="prosespendaftar.php?hapus_konfir=<?php echo $row_1['id_pendaftaran']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash3"></i></a>
                    </td>
                  </tr>
                </tbody>
                <?php $no++; ?>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready( function () {
      $('#table_noconfirm').DataTable();
      $('#table_confirm').DataTable();
    });
  </script>

  <script src="../public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
</body>
</html>
