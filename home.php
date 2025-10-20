<?php include "head.php"; ?>

<!-- ================== SCRIPT UNTUK HALAMAN DASHBOARD ================== -->
<script type="text/javascript">
	// Set judul halaman di browser
	document.title = "Dashboard";
	// Tambahkan class 'active' pada menu Dashboard di sidebar
	document.getElementById('dash').classList.add('active');
</script>

<!-- ================== KONTEN UTAMA DASHBOARD ================== -->
<div class="content">
	<div class="padding">

		<!-- ====== BOX: INFORMASI LOGIN ====== -->
		<div class="box">
			<div class="padding">
				<i class="fa fa-user"></i> Login sebagai
				<span class="status greend">
					<?php
					// Menampilkan status login: Admin atau Kasir
					if ($_SESSION['status'] == 1) {
						echo "Admin";
					} else {
						echo "Kasir";
					}
					?>
				</span>
			</div>
		</div>

		<!-- ====== BOX: WAKTU SEKARANG ====== -->
		<div class="box">
			<div class="padding">
				<i class="fa fa-clock-o"></i> Waktu
				<span class="status blued"><?= date("d-m-Y") ?></span>
			</div>
		</div>

		<!-- ====== BOX: JUMLAH DATA BARANG ====== -->
		<div class="box">
			<div class="padding">
				<i class="fa fa-bars"></i> Data Barang
				<span class="status blued"><?= $root->show_jumlah_barang() ?></span>
			</div>
		</div>

		<!-- ====== BOX: JUMLAH LAPORAN PENJUALAN ====== -->
		<div class="box">
			<div class="padding">
				<i class="fa fa-book"></i> Laporan
				<span class="status blued"><?= $root->show_jumlah_trans2() ?></span>
			</div>
		</div>

		<!-- ====== GAMBAR DASHBOARD ====== -->
		<img src="assets/img/dasbord.jpg" height="452px" align="left">

	</div>
</div>

<?php include "foot.php"; ?>