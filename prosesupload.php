<?php
include "../config.php";
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
    header("Location: ../login.php");
    exit();
}

// Cek apakah form sudah dikirim
if (isset($_POST['konfirmasi'])) {
    $id_pendaftar = mysqli_real_escape_string($koneksi, $_POST['id_pendaftar']);

    // Cek apakah file diunggah
    if (!isset($_FILES['file_pdf']) || $_FILES['file_pdf']['error'] != 0) {
        echo "<script>alert('Gagal upload file!'); window.history.back();</script>";
        exit();
    }

    $file_name = $_FILES['file_pdf']['name'];
    $file_tmp = $_FILES['file_pdf']['tmp_name'];
    $file_size = $_FILES['file_pdf']['size'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

    // Validasi ekstensi file (hanya PDF)
    $allowed_ext = ['pdf'];
    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        echo "<script>alert('Hanya file PDF yang diperbolehkan!'); window.history.back();</script>";
        exit();
    }

    // Validasi ukuran file (maksimal 2MB)
    if ($file_size > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran file terlalu besar (Maks: 2MB)!'); window.history.back();</script>";
        exit();
    }

    // Tentukan folder penyimpanan
    $upload_dir = "../public/berkas/surat-balasan/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Buat nama file unik
    $new_file_name = time() . "_" . $id_pendaftar . "." . $file_ext;
    $file_path = $upload_dir . $new_file_name;

    // Pindahkan file ke folder penyimpanan
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Gunakan Prepared Statements untuk menghindari SQL Injection
        $query = "INSERT INTO surat_balasan (id_pendaftar, file_pdf) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $id_pendaftar, $new_file_name);
        
        $update_status = "UPDATE pendaftar SET status='aktif' WHERE id_pendaftaran=?";
        $stmt2 = mysqli_prepare($koneksi, $update_status);
        mysqli_stmt_bind_param($stmt2, "i", $id_pendaftar);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_execute($stmt2)) {
            echo "<script>alert('Surat berhasil diunggah dan pendaftar dikonfirmasi!'); window.location.href = 'pendaftar.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Gagal menyimpan file!'); window.history.back();</script>";
    }
} else {
    header("Location: pendaftar.php");
    exit();
}
?>
