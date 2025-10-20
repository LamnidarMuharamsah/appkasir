<script type="text/javascript">
	<?php if ($_SESSION['status'] == 1) { ?>
		// Jika user adalah admin
		document.title = "Detail Laporan";
		document.getElementById('laporan').classList.add('active');
	<?php } else { ?>
		// Jika user adalah kasir
		document.title = "Detail Transaksi";
		document.getElementById('transaksi').classList.add('active');
	<?php } ?>
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">

				<?php if ($_SESSION['status'] == 1) { ?>
					<h3 class="jdl">Detail Laporan</h3>
				<?php } else { ?>
					<h3 class="jdl">Detail Transaksi</h3>
				<?php } ?>

				<?php
				// Ambil data header transaksi berdasarkan ID transaksi
				$getqheader = $root->con->query("SELECT * FROM transaksi WHERE id_transaksi='$_GET[id_transaksi]'");
				$getqheader = $getqheader->fetch_assoc();
				?>

				<!-- Informasi umum transaksi -->
				<table>
					<tr>
						<td><span class="label">Nama Pembeli</span></td>
						<td><span class="label">:</span></td>
						<td><span class="label"><?= $getqheader['nama_pembeli'] ?></span></td>
					</tr>
					<tr>
						<td><span class="label">Tanggal Transaksi</span></td>
						<td><span class="label">:</span></td>
						<td><span class="label"><?= date("d-m-Y", strtotime($getqheader['tgl_transaksi'])) ?></span></td>
					</tr>
					<tr>
						<td><span class="label">No Invoice</span></td>
						<td><span class="label">:</span></td>
						<td><span class="label"><?= $getqheader['no_invoice'] ?></span></td>
					</tr>
				</table>

				<!-- Detail barang yang dibeli -->
				<table class="datatable" style="width: 100%;">
					<thead>
						<tr>
							<th width="35px">NO</th>
							<th>Nama Barang</th>
							<th>Jumlah Beli</th>
							<th>Harga</th>
							<th>Total Harga</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Ambil semua data barang berdasarkan id_transaksi
						$data = $root->con->query("
							SELECT 
								barang.nama_barang,
								barang.harga_jual,
								sub_transaksi.jumlah_beli,
								sub_transaksi.total_harga,
								sub_transaksi.bayar,
								sub_transaksi.diskon
							FROM sub_transaksi
							INNER JOIN barang ON barang.id_barang = sub_transaksi.id_barang
							WHERE sub_transaksi.id_transaksi = '$_GET[id_transaksi]'
						");

						// Hitung total keseluruhan transaksi
						$getsum = $root->con->query("
							SELECT 
								SUM(total_harga) AS grand_total,
								SUM(jumlah_beli) AS jumlah_beli,
								diskon
							FROM sub_transaksi
							WHERE id_transaksi = '$_GET[id_transaksi]'
						");

						$getsum1 = $getsum->fetch_assoc();
						$bayar = 0;
						$no = 1;

						// Tampilkan setiap barang
						while ($f = $data->fetch_assoc()) {
						?>
							<tr>
								<td><?= $no++ ?></td>
								<td><?= $f['nama_barang'] ?></td>
								<td><?= $f['jumlah_beli'] ?></td>
								<td>Rp. <?= number_format($f['harga_jual']) ?></td>
								<td>Rp. <?= number_format($f['total_harga']) ?></td>
							</tr>
						<?php
							$bayar = $f['bayar'];
							$diskon = $f['diskon'];
						}
						?>

						<!-- Total keseluruhan transaksi -->
						<tr>
							<td colspan="3"></td>
							<td>Total :</td>
							<td>Rp. <?= number_format($getsum1['grand_total']) ?></td>
						</tr>

						<!-- Potongan harga -->
						<tr>
							<td colspan="3"></td>
							<td>(Diskon) Potongan Harga :</td>
							<td>Rp. <?= number_format($getsum1['diskon']) ?></td>
						</tr>

						<!-- Grand total setelah diskon -->
						<tr>
							<td colspan="3"></td>
							<td>Grand Total :</td>
							<td>Rp. <?= number_format($getsum1['grand_total'] - $getsum1['diskon']) ?></td>
						</tr>

						<!-- Jumlah bayar -->
						<tr>
							<td colspan="3"></td>
							<td>Bayar :</td>
							<td>Rp. <?= number_format($bayar) ?></td>
						</tr>

						<!-- Kembalian -->
						<tr>
							<td colspan="3"></td>
							<td>Kembalian :</td>
							<td>Rp. <?= number_format($bayar - ($getsum1['grand_total'] - $getsum1['diskon'])) ?></td>
						</tr>
					</tbody>
				</table>

				<br>

				<!-- Tombol navigasi -->
				<div class="left">
					<?php
					// Tentukan link kembali berdasarkan status user
					$link = ($_SESSION['status'] == 1) ? "laporan.php" : "transaksi.php";
					?>
					<a href="<?= $link ?>" class="btnblue" style="background: #f33155">
						<i class="fa fa-mail-reply"></i> Kembali
					</a>

					<?php if ($_SESSION['status'] == 2) { ?>
						<!-- Tombol cetak nota hanya untuk kasir -->
						<a href="cetak_nota.php?oid=<?= base64_encode($_GET['id_transaksi']) ?>&id-uid=<?= base64_encode($getqheader['nama_pembeli']) ?>&inf=<?= base64_encode($getqheader['no_invoice']) ?>&tb=<?= base64_encode($f['total_bayar']) ?>&uuid=<?= base64_encode(date('d-m-Y', strtotime($getqheader['tgl_transaksi']))) ?>"
							class="btnblue" target="_blank">
							<i class="fa fa-print"></i> Cetak Nota
						</a>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>
</div>