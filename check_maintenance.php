<?php
// Tanggal terakhir maintenance (ubah di sini tiap selesai maintenance)
$lastMaintenance = strtotime("2025-10-10");

// Hitung tanggal berikutnya maintenance (3 bulan)
$nextMaintenance = strtotime("+3 months", $lastMaintenance);
$today = time();

// Jika sudah waktunya maintenance, redirect ke halaman maintenance
if ($today >= $nextMaintenance) {
    header("Location: maintenance.php");
    exit;
}
