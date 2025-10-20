<?php
// ============================================================
//  File: home.php
//  Deskripsi: Halaman utama setelah login, menampilkan sidebar
//  dan navigasi sesuai status pengguna (admin / kasir)
// ============================================================

// Include file koneksi utama
include "root.php";

// Memulai session
session_start();

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
	$root->redirect("index.php"); // Jika belum login, arahkan ke halaman login
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Dashboard</title>

	<!-- ================== STYLESHEET ================== -->
	<link rel="stylesheet" href="assets/index.css">
	<link rel="stylesheet" href="assets/awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/datatable/css/buttons.dataTables.min.css">
	<link rel="stylesheet" href="assets/datatable/css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="assets/datatable/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="assets/datatable/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="assets/datatable/css/select2.min.css">

	<!-- ================== JAVASCRIPT ================== -->
	<script src="assets/jquery-3.3.1.min.js"></script>
	<script src="assets/datatable/js/buttons.flash.min.js"></script>
	<script src="assets/datatable/js/buttons.html5.min.js"></script>
	<script src="assets/datatable/js/dataTables.bootstrap.js"></script>
	<script src="assets/datatable/js/dataTables.bootstrap.min.js"></script>
	<script src="assets/datatable/js/dataTables.buttons.min.js"></script>
	<script src="assets/datatable/js/jquery.dataTables.js"></script>
	<script src="assets/datatable/js/jquery.dataTables.min.js"></script>
	<script src="assets/datatable/js/jszip.min.js"></script>
	<script src="assets/datatable/js/moment.js"></script>
	<script src="assets/datatable/js/pdfmake.min.js"></script>
	<script src="assets/datatable/js/vfs_fonts.js"></script>
	<script src="assets/datatable/js/select2.min.js"></script>

	<!-- ================== RESPONSIVE STYLE ================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		@media (max-width: 768px) {
			body {
				font-size: 14px;
			}

			.sidebar {
				width: 100%;
				position: relative;
			}

			.sidebar ul li {
				display: inline-block;
				margin-right: 10px;
			}

			.sidebar h3 {
				text-align: center;
				font-size: 18px;
			}

			.nav ul {
				display: block;
				text-align: center;
			}

			.nav ul li {
				display: block;
				margin-bottom: 5px;
			}

			.nav ul li ul {
				position: static;
				margin: 5px 0;
			}

			.content,
			.bgwhite,
			.padding {
				padding: 10px !important;
			}

			table,
			thead,
			tbody,
			th,
			td,
			tr {
				display: block;
				width: 100%;
			}

			thead tr {
				display: none;
			}

			td {
				text-align: right;
				padding-left: 50%;
				position: relative;
			}

			td::before {
				position: absolute;
				left: 10px;
				width: 45%;
				white-space: nowrap;
				content: attr(data-label);
				text-align: left;
				font-weight: bold;
			}

			.form-input input,
			.form-input button,
			.form-input a.btnblue {
				width: 100%;
				margin-bottom: 10px;
			}

			img {
				max-width: 100%;
				height: auto;
			}
		}

		@media (min-width: 769px) and (max-width: 1024px) {
			.content {
				padding: 15px;
			}

			.form-input input,
			.form-input button {
				width: 100%;
			}
		}
	</style>
</head>

<body>

	<!-- ================== SIDEBAR ================== -->
	<div class="sidebar">
		<h3><i class="fa fa-shopping-cart"></i> RESNLIGHT</h3>
		<ul>
			<?php if ($_SESSION['status'] == 1): // Jika user adalah admin 
			?>
				<li class="admin-info">
					<img src="assets/img/logo.jpg" alt="Logo">
					<span>OWNER SHOP RULY</span>
				</li>
				<li><a id="dash" href="home.php"><i class="fa fa-home"></i> Dashboard</a></li>
				<li><a id="barang" href="barang.php"><i class="fa fa-bars"></i> Barang</a></li>
				<li><a id="kategori" href="kategori.php"><i class="fa fa-tags"></i> Kategori Barang</a></li>
				<li><a id="users" href="users.php"><i class="fa fa-users"></i> Kasir</a></li>
				<li><a id="laporan" href="laporan.php"><i class="fa fa-book"></i> Laporan Penjualan</a></li>
				<li><a id="laporan_stok" href="laporan_barang_stok.php"><i class="fa fa-book"></i> Laporan Stok Modal</a></li>
			<?php else: // Jika user adalah kasir 
			?>
				<li><a id="transaksi" href="transaksi.php"><i class="fa fa-money"></i> Transaksi</a></li>
			<?php endif; ?>
		</ul>
	</div>

	<!-- ================== NAVBAR ================== -->
	<div class="nav">
		<ul>
			<li>
				<a href="#"><i class="fa fa-user"></i> <?= $_SESSION['username'] ?></a>
				<ul>
					<?php if ($_SESSION['status'] == 1): ?>
						<li><a href="setting_akun.php"><i class="fa fa-cog"></i> Pengaturan Akun</a></li>
					<?php endif; ?>
					<li><a href="handler.php?action=logout"><i class="fa fa-sign-out"></i> Logout</a></li>
				</ul>
			</li>
		</ul>
	</div>

</body>

</html>