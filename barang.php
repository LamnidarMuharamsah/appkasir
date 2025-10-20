<?php include "head.php"; ?>

<?php
// Mengecek apakah ada parameter 'action' pada URL
if (isset($_GET['action']) && $_GET['action'] == "tambah_barang") {
	include "tambah_barang.php";
} else if (isset($_GET['action']) && $_GET['action'] == "edit_barang") {
	include "edit_barang.php";
} else {
?>

	<!-- ==================== SETTING HALAMAN BARANG ==================== -->
	<script type="text/javascript">
		// Mengatur judul halaman
		document.title = "Barang";

		// Menambahkan class 'active' pada menu Barang di sidebar
		document.getElementById('barang').classList.add('active');
	</script>

	<!-- ==================== LOAD SEMUA FILE DATATABLE JS ==================== -->
	<script type="text/javascript" src="assets/datatable/js/jquery.js"></script>
	<script type="text/javascript" src="assets/datatable/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/buttons.html5.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/buttons.flash.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/jszip.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/pdfmake.min.js"></script>
	<script type="text/javascript" src="assets/datatable/js/vfs_fonts.js"></script>
	<script type="text/javascript" src="assets/datatable/js/moment.js"></script>

	<!-- ==================== SCRIPT TAMBAHAN JIKA DIPERLUKAN ==================== -->
	<script type="text/javascript">
		$(function() {
			// Placeholder untuk script tambahan di masa depan
		});
	</script>

	<!-- ==================== BAGIAN ISI KONTEN ==================== -->
	<div class="content">
		<div class="padding">
			<div class="bgwhite">
				<div class="padding">

					<!-- ==================== HEADER KONTEN ==================== -->
					<div class="contenttop">
						<div class="left">
							<!-- Tombol tambah barang -->
							<a href="?action=tambah_barang" class="btnblue">
								<i class="fa fa-plus"></i> Tambah Barang
							</a>

							<!-- Tombol cetak data (dinonaktifkan sementara) -->
							<!-- <a href="cetak_barang.php" class="btnblue" target="_blank"><i class="fa fa-print"></i> Cetak</a> -->
						</div>

						<div class="right">
							<!-- ==================== FILTER KATEGORI ==================== -->
							<script type="text/javascript">
								function gotocat(val) {
									var value = val.options[val.selectedIndex].value;
									// Redirect ke halaman dengan parameter id_cat (id kategori)
									window.location.href = "barang.php?id_cat=" + value;
								}
							</script>

							<!-- Dropdown Filter Kategori -->
							<select class="leftin1" onchange="gotocat(this)">
								<option value="">Filter kategori</option>
								<?php
								// Ambil semua kategori dari database
								$data = $root->con->query("SELECT * FROM kategori");
								while ($f = $data->fetch_assoc()) {
								?>
									<option
										<?php
										// Menandai kategori yang sedang dipilih
										if (isset($_GET['id_cat']) && $_GET['id_cat'] == $f['id_kategori']) {
											echo "selected";
										}
										?>
										value="<?= $f['id_kategori'] ?>">
										<?= $f['nama_kategori'] ?>
									</option>
								<?php } ?>
							</select>

							<!-- ==================== FORM PENCARIAN BARANG ==================== -->
							<form class="leftin">
								<input type="search"
									name="q"
									placeholder="Cari Barang..."
									value="<?php echo $keyword = isset($_GET['q']) ? $_GET['q'] : ''; ?>">
								<button><i class="fa fa-search"></i></button>
							</form>
						</div>
						<div class="both"></div>
					</div>

					<!-- ==================== JUMLAH BARANG ==================== -->
					<span class="label">
						Jumlah Barang : <?= $root->show_jumlah_barang(); ?>
					</span>

					<!-- ==================== TABEL DATA BARANG ==================== -->
					<table class="datatable" id="datatable">
						<thead>
							<tr>
								<th width="10px">#</th>
								<th style="cursor: pointer;">Kode Barang <i class="fa fa-sort"></i></th>
								<th style="cursor: pointer;">Nama Barang <i class="fa fa-sort"></i></th>
								<th style="cursor: pointer;" width="100px">Kategori <i class="fa fa-sort"></i></th>
								<th>Stok</th>
								<th width="120px">Harga Modal</th>
								<th width="120px">Harga Jual</th>
								<th width="150px">Tanggal Ditambahkan</th>
								<th width="60px">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// Jika kategori dipilih → tampilkan barang sesuai kategori
							if (isset($_GET['id_cat']) && $_GET['id_cat']) {
								$root->tampil_barang_filter($_GET['id_cat']);
							} else {
								// Jika tidak ada filter kategori → tampilkan semua barang (bisa juga berdasarkan pencarian)
								$keyword = isset($_GET['q']) ? $_GET['q'] : "null";
								$root->tampil_barang($keyword);
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

<?php
} // Penutup blok if utama
include "foot.php"; // Footer halaman
?>