<?php
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
include "../config.php";

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['nim']) || empty($_GET['nim'])) {
    die("NIM tidak ditemukan.");
}

$nim = $_GET['nim'];

$nilai_result = mysqli_query($koneksi, "SELECT * FROM nilai WHERE nim='$nim'");
$peserta_result = mysqli_query($koneksi, "SELECT * FROM peserta WHERE nim='$nim'");

$peserta = mysqli_fetch_assoc($peserta_result);
$nilai = mysqli_fetch_assoc($nilai_result);

if (!$peserta || !$nilai) {
    die("Data tidak ditemukan.");
}

function predikat($angka) {
    if ($angka >= 90) return "A";
    if ($angka >= 75) return "B";
    if ($angka >= 60) return "C";
    if ($angka >= 45) return "D";
    return "E";
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

$html = '<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Magang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; padding: 8px; text-align: center; }
        .judul { text-align: center; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <h3 class="judul">REKAP NILAI MAGANG SISWA</h3>
    <table>
        <tr><td><b>Nama</b></td><td>' . htmlspecialchars($peserta['nama']) . '</td></tr>
        <tr><td><b>NIM</b></td><td>' . htmlspecialchars($peserta['nim']) . '</td></tr>
        <tr><td><b>Program Studi</b></td><td>' . htmlspecialchars($peserta['jurusan']) . '</td></tr>
        <tr><td><b>Instansi</b></td><td>' . htmlspecialchars($peserta['instansi']) . '</td></tr>
        <tr><td><b>Periode</b></td><td>' . date('Y', strtotime($peserta['tgl_mulai'])) . '</td></tr>
    </table>
    <table>
        <tr><th>No</th><th>Unsur Dinilai</th><th>Nilai</th><th>Predikat</th></tr>';

$aspek_nilai = ['Disiplin', 'Tanggung Jawab', 'Loyalitas', 'Kerjasama', 'Kepemimpinan', 'Kepedulian', 'Integritas', 'Etika / Moral', 'Kerapian', 'Makalah'];

foreach ($aspek_nilai as $key => $unsur) {
    $kolom = strtolower(str_replace(' ', '', $unsur));
    $nilai_angka = $nilai[$kolom] ?? 0;
    $html .= '<tr><td>' . ($key + 1) . '</td><td>' . $unsur . '</td><td>' . $nilai_angka . '</td><td>' . predikat($nilai_angka) . '</td></tr>';
}

$html .= '<tr><td colspan="2"><b>Nilai Rata-rata</b></td><td>' . htmlspecialchars($nilai['nilai_rata'] ?? 0) . '</td><td></td></tr>
    </table>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Nilai_$nim.pdf", ["Attachment" => true]);
?>
