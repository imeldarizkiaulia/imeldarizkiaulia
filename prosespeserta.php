<?php
include "../config.php";

$id_peserta = @$_POST['id_peserta'];
$nim = @$_POST['nim'];
$nama = @$_POST['nama'];
$instansi = @$_POST['instansi'];
$jurusan = @$_POST['jurusan'];
$tgl_mulai = @$_POST['tgl_mulai'];
$tgl_selesai = @$_POST['tgl_selesai'];
$gender = @$_POST['gender'];
$agama = @$_POST['agama'];
$email = @$_POST['email'];
$wa = @$_POST['wa'];

$status = "aktif";
$status_edit = @$_POST['status_edit'];

function alert($success, $message) {
    if ($success) {
        echo "<script> 
                alert('INPUT BERHASIL!'); 
                document.location.href = 'peserta.php'; 
              </script>";
    } else {
        echo "<script> 
                alert('INPUT GAGAL! Error: $message'); 
                document.location.href = 'peserta.php'; 
              </script>";
    }
}

// **HANDLE TAMBAH DATA PESERTA**
if (isset($_POST['konfirmasi'])) {
    $query1 = mysqli_query($koneksi, "INSERT INTO peserta(nim, nama, instansi, jurusan, tgl_mulai, tgl_selesai, gender, agama, email, no_wa, status) 
                                      VALUES ('$nim', '$nama', '$instansi', '$jurusan', '$tgl_mulai', '$tgl_selesai', '$gender', '$agama', '$email', '$wa', '$status')");
    
    $query2 = mysqli_query($koneksi, "INSERT INTO nilai(nim, nama) VALUES ('$nim', '$nama')");

    // Periksa apakah kedua query berhasil
    if ($query1 && $query2) {
        alert(true, "");
    } else {
        alert(false, mysqli_error($koneksi));
    }
}

// **HANDLE UPDATE DATA PESERTA**
elseif (isset($_POST['simpan'])) {
    $query1 = mysqli_query($koneksi, "UPDATE peserta SET nim='$nim', nama='$nama', instansi='$instansi', jurusan='$jurusan', 
                                        tgl_mulai='$tgl_mulai', tgl_selesai='$tgl_selesai', gender='$gender', agama='$agama', 
                                        email='$email', no_wa='$wa', status='$status_edit' 
                                      WHERE id_peserta='$id_peserta'");

    $query2 = mysqli_query($koneksi, "UPDATE nilai SET nama='$nama' WHERE nim='$nim'");

    // Periksa apakah kedua query berhasil
    if ($query1 && $query2) {
        alert(true, "");
    } else {
        alert(false, mysqli_error($koneksi));
    }
}

// **HANDLE HAPUS DATA PESERTA**
elseif (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    $nim_hapus = $_GET['nim'];

    // Cek apakah peserta memiliki makalah
    $query = mysqli_query($koneksi, "SELECT * FROM makalah WHERE nim='$nim_hapus'");
    if ($query && mysqli_num_rows($query) > 0) {
        $makalah = mysqli_fetch_array($query);
        $makalah_hapus = $makalah['berkas'];
        unlink("../public/berkas/makalah/$makalah_hapus");
    }

    $query3 = mysqli_query($koneksi, "DELETE FROM peserta WHERE id_peserta='$id_hapus'");
    $query4 = mysqli_query($koneksi, "DELETE FROM nilai WHERE nim='$nim_hapus'");

    // Periksa apakah kedua query berhasil
    if ($query3 && $query4) {
        header("location:peserta.php");
    } else {
        alert(false, mysqli_error($koneksi));
    }
}
?>
