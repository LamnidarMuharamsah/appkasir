<!-- ========================================= -->
<!-- Script untuk atur judul halaman dan menu aktif -->
<!-- ========================================= -->
<script type="text/javascript">
	document.title = "Tambah Kasir"; // Ubah judul tab browser
	document.getElementById('users').classList.add('active'); // Tambahkan class 'active' ke menu Users
</script>

<!-- ========================================= -->
<!-- Konten Halaman Tambah Kasir -->
<!-- ========================================= -->
<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">

				<!-- Judul Halaman -->
				<h3 class="jdl">Tambah Kasir</h3>

				<!-- ========================================= -->
				<!-- Form Input Data Kasir Baru -->
				<!-- ========================================= -->
				<form class="form-input" method="post" action="handler.php?action=tambah_kasir">

					<!-- Input username kasir -->
					<input
						type="text"
						name="nama_kasir"
						placeholder="Username Kasir"
						required="required">

					<!-- Input password kasir -->
					<input
						autocomplete="off"
						type="text"
						name="password"
						placeholder="Password"
						required="required">

					<!-- Tombol Simpan Data -->
					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Simpan
					</button>

					<!-- Tombol Batal (kembali ke halaman users) -->
					<a href="users.php" class="btnblue" style="background: #f33155">
						<i class="fa fa-close"></i> Batal
					</a>

				</form>
				<!-- ========================================= -->
				<!-- Akhir dari Form Tambah Kasir -->
				<!-- ========================================= -->

			</div>
		</div>
	</div>
</div>