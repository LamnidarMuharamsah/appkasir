<?php include "head.php"; ?>

<?php
// =========================================================
// CEK AKSI YANG DIKIRIM DARI URL (GET)
// =========================================================
if (isset($_GET['action']) && $_GET['action'] == "transaksi_baru") {

	// Jika action = transaksi_baru → tampilkan halaman transaksi baru
	include "transaksi_baru.php";
} else if (isset($_GET['action']) && $_GET['action'] == "detail_transaksi") {

	// Jika action = detail_transaksi → tampilkan halaman detail transaksi
	include "detail_transaksi.php";
} else {
	// Jika tidak ada action → tampilkan halaman daftar transaksi
?>

	<!-- ========================================================= -->
	<!-- SCRIPT UNTUK UBAH JUDUL DAN TANDAI MENU TRANSAKSI AKTIF -->
	<!-- ========================================================= -->
	<script type="text/javascript">
		document.title = "Transaksi";
		document.getElementById('transaksi').classList.add('active');
	</script>

	<!-- ========================================================= -->
	<!-- KONTEN HALAMAN DAFTAR TRANSAKSI -->
	<!-- ========================================================= -->
	<div class="content">
		<div class="padding">
			<div class="bgwhite">
				<div class="padding">

					<!-- Bagian Atas: Tombol Transaksi Baru -->
					<div class="contenttop">
						<div class="left">
							<a href="?action=transaksi_baru" class="btnblue">Transaksi Baru</a>
						</div>
						<div class="both"></div>
					</div>

					<!-- Menampilkan jumlah total transaksi -->
					<span class="label">Jumlah Transaksi : <?= $root->show_jumlah_trans(); ?></span>

					<!-- ========================================================= -->
					<!-- TABEL DAFTAR TRANSAKSI -->
					<!-- ========================================================= -->
					<table class="datatable">
						<thead>
							<tr>
								<th width="35px">NO</th>
								<th>Tanggal Transaksi</th>
								<th>Total Bayar</th>
								<th>Nama Pembeli</th>
								<th>No Invoice</th>
								<th>Aksi</th>
							</tr>
						</thead>

						<tbody>
							<?php
							// =========================================================
							// QUERY UNTUK MENAMPILKAN DATA TRANSAKSI
							// =========================================================
							$no = 1;
							$q = $root->con->query("
								SELECT * FROM transaksi 
								WHERE kode_kasir = '$_SESSION[id]' 
								ORDER BY id_transaksi DESC
							");

							// =========================================================
							// CEK APAKAH ADA DATA TRANSAKSI
							// =========================================================
							if ($q->num_rows > 0) {
								while ($f = $q->fetch_assoc()) {
							?>
									<tr>
										<td><?= $no++; ?></td>
										<td><?= date("d-m-Y", strtotime($f['tgl_transaksi'])); ?></td>
										<td>Rp. <?= number_format($f['total_bayar']); ?></td>
										<td><?= $f['nama_pembeli']; ?></td>
										<td><?= $f['no_invoice']; ?></td>
										<td>
											<!-- Tombol lihat detail transaksi -->
											<a
												href="?action=detail_transaksi&id_transaksi=<?= $f['id_transaksi']; ?>"
												class="btn bluetbl m-r-10">
												<span class="btn-edit-tooltip">Detail</span>
												<i class="fa fa-eye"></i>
											</a>

											<!-- Tombol cetak nota -->
											<a
												href="cetak_nota.php?oid=<?= base64_encode($f['id_transaksi']); ?>&id-uid=<?= base64_encode($f['nama_pembeli']); ?>&inf=<?= base64_encode($f['no_invoice']); ?>&tb=<?= base64_encode($f['total_bayar']); ?>&uuid=<?= base64_encode(date("d-m-Y", strtotime($f['tgl_transaksi']))); ?>"
												target="_blank"
												class="btn bluetbl">
												<span class="btn-hapus-tooltip">Cetak</span>
												<i class="fa fa-print"></i>
											</a>
										</td>
									</tr>
								<?php
								}
							} else {
								// Jika tidak ada transaksi
								?>
								<tr>
									<td><?= $no++; ?></td>
									<td colspan="5">Belum Ada Transaksi</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
					<!-- ========================================================= -->
					<!-- AKHIR TABEL TRANSAKSI -->
					<!-- ========================================================= -->

				</div>
			</div>
		</div>
	</div>

<?php
} // Akhir dari else utama
include "foot.php";
?>