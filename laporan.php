<?php include "head.php" ?>
<?php
// Mengecek apakah halaman diminta adalah detail transaksi
if (isset($_GET['action']) && $_GET['action'] == "detail_transaksi") {
	include "detail_transaksi.php"; // Jika iya, tampilkan halaman detail transaksi
} else {
?>
	<script type="text/javascript">
		// Mengubah judul tab dan menandai menu laporan sebagai aktif
		document.title = "Laporan Penjualan";
		document.getElementById('laporan').classList.add('active');
	</script>

	<div class="content">
		<div class="padding">
			<div class="bgwhite">
				<div class="padding">
					<!-- Bagian header laporan penjualan -->
					<div class="contenttop">
						<div class="left">
							<h3 class="jdl">Laporan Penjualan</h3>
						</div>

						<!-- Bagian kanan: filter dan tombol cetak laporan -->
						<div class="right">
							<script type="text/javascript">
								// Fungsi untuk berpindah halaman berdasarkan jenis laporan (harian/bulanan/tahunan)
								function gotojenis(val) {
									var value = val.options[val.selectedIndex].value;
									window.location.href = "laporan.php?jenis=" + value;
								}

								// Fungsi untuk memfilter laporan berdasarkan tanggal/bulan/tahun
								function gotofilter(val) {
									var value = val.options[val.selectedIndex].value;
									window.location.href = "laporan.php?jenis=<?php if (isset($_GET['jenis'])) echo $_GET['jenis']; ?>&filter_record=" + value;
								}
							</script>

							<!-- Form filter laporan -->
							<span style="float:left; padding:5px; margin-right:10px; color:#666;">Filter dan cetak :</span>
							<form action="cetak_laporan.php" style="display:inline;" target="_blank" method="post">
								<!-- Pilihan jenis laporan -->
								<select class="leftin1" onchange="gotojenis(this)" name="jenis_laporan" required>
									<option>Pilih Jenis</option>
									<option value="perhari" <?php if (isset($_GET['jenis']) && $_GET['jenis'] == 'perhari') echo "selected"; ?>>Perhari</option>
									<option value="perbulan" <?php if (isset($_GET['jenis']) && $_GET['jenis'] == 'perbulan') echo "selected"; ?>>Perbulan</option>
									<option value="pertahun" <?php if (isset($_GET['jenis']) && $_GET['jenis'] == 'pertahun') echo "selected"; ?>>Pertahun</option>
								</select>

								<!-- Pilihan filter tanggal/bulan/tahun -->
								<select class="leftin1" onchange="gotofilter(this)" required name="tgl_laporan">
									<?php
									if (isset($_GET['jenis']) && $_GET['jenis'] == 'perhari') {
										echo '<option>Pilih Hari</option>';
										$data = $root->con->query("SELECT DISTINCT DATE(tgl_transaksi) AS tgl_transaksi FROM transaksi ORDER BY id_transaksi DESC");
										while ($f = $data->fetch_assoc()) {
											$tgl = date('d-m-Y', strtotime($f['tgl_transaksi']));
											$selected = (isset($_GET['filter_record']) && $_GET['filter_record'] == $tgl) ? "selected" : "";
											echo "<option value='$tgl' $selected>$tgl</option>";
										}
									} elseif (isset($_GET['jenis']) && $_GET['jenis'] == 'perbulan') {
										echo '<option value="">Pilih Bulan</option>';
										$data = $root->con->query("SELECT DISTINCT EXTRACT(YEAR FROM tgl_transaksi) AS OrderYear, EXTRACT(MONTH FROM tgl_transaksi) AS OrderMonth FROM transaksi ORDER BY id_transaksi DESC");
										while ($f = $data->fetch_assoc()) {
											$val = str_pad($f['OrderMonth'], 2, '0', STR_PAD_LEFT) . "-" . $f['OrderYear'];
											$selected = (isset($_GET['filter_record']) && $_GET['filter_record'] == $val) ? "selected" : "";
											echo "<option value='$val' $selected>$val</option>";
										}
									} elseif (isset($_GET['jenis']) && $_GET['jenis'] == 'pertahun') {
										echo '<option value="">Pilih Tahun</option>';
										$data = $root->con->query("SELECT DISTINCT EXTRACT(YEAR FROM tgl_transaksi) AS OrderYear FROM transaksi ORDER BY id_transaksi DESC");
										while ($f = $data->fetch_assoc()) {
											$val = $f['OrderYear'];
											$selected = (isset($_GET['filter_record']) && $_GET['filter_record'] == $val) ? "selected" : "";
											echo "<option value='$val' $selected>$val</option>";
										}
									} else {
										echo "<option>Pilih Jenis Cetak terlebih dahulu</option>";
									}
									?>
								</select>

								<!-- Tombol Cetak -->
								<button class="btn-ctk"
									style="background:#41b3f9; color:#fff; border-radius:3px; border:1px solid #41b3f9"
									<?php if (!isset($_GET['filter_record'])) echo 'disabled title="Pilih jenis dan tanggal lebih dulu"'; ?>>
									Cetak
								</button>
							</form>
						</div>
						<div class="both"></div>
					</div>

					<!-- Tabel data laporan penjualan -->
					<table class="datatable" id="datatable">
						<thead>
							<tr>
								<th width="10px">#</th>
								<th>No Invoice</th>
								<th>Kasir</th>
								<th>Pembeli</th>
								<th>Tanggal Transaksi</th>
								<th>Modal</th>
								<th>Total Bayar</th>
								<th>Keuntungan</th>
								<th width="60px">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// Menampilkan laporan sesuai filter yang dipilih
							if (isset($_GET['filter_record'])) {
								if ($_GET['jenis'] == 'perhari') $aksi1 = 1;
								else if ($_GET['jenis'] == 'perbulan') $aksi1 = 2;
								else if ($_GET['jenis'] == 'pertahun') $aksi1 = 3;

								// Tampilkan laporan berdasarkan filter
								$root->filter_tampil_laporan($_GET['filter_record'], $aksi1);
							} else {
								// Jika belum difilter, tampilkan seluruh laporan
								$root->tampil_laporan();
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php
}
include "foot.php"; // Menyertakan footer
?>