<script type="text/javascript">
	// ======== SET JUDUL HALAMAN & NAV ACTIVE =========
	document.title = "Transaksi Baru";
	document.getElementById('transaksi').classList.add('active');
</script>

<script type="text/javascript">
	// ======== CEK APAKAH ADA DATA TRANSAKSI =========
	// Jika belum ada barang ditambahkan, tombol proses dinonaktifkan
	$(document).ready(function() {
		if ($.trim($('#contenth').text()) == "") {
			$('#prosestran').attr("disabled", "disabled");
			$('#prosestran').attr("title", "Tambahkan barang terlebih dahulu");
			$('#prosestran').css({
				"background": "#ccc",
				"cursor": "not-allowed"
			});
		}
	});
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Entry Transaksi Baru</h3>

				<!-- ======== INPUT BARCODE (HIDDEN) UNTUK SCANNER ========= -->
				<input type="text" id="barcode-input" autocomplete="off" style="position:absolute;left:-9999px" />

				<!-- ======== FORM TAMBAH BARANG KE TEMPO ========= -->
				<form class="form-input" method="post" action="handler.php?action=tambah_tempo" style="padding-top: 30px;">
					<label>Pilih Barang : </label><br><br>

					<select style="width: 372px;cursor: pointer;" required name="id_barang" class="select2-barang">
						<?php
						$data = $root->con->query("SELECT * FROM barang");
						while ($f = $data->fetch_assoc()) {
							echo "<option value='$f[id_barang]'>Id Barang: $f[id_barang] | $f[nama_barang] (Stok: $f[stok])</option>";
						}
						?>
					</select>
					<br><br>

					<label>Jumlah Beli :</label>
					<input required type="number" name="jumlah">

					<!-- Nomor transaksi unik -->
					<input type="hidden" name="trx" value="<?php echo date('d') . '/AF/' . $_SESSION['id'] . '/' . date('y'); ?>">

					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Simpan
					</button>
				</form>
			</div>
		</div>

		<br>

		<!-- ======== TABEL DATA TRANSAKSI ========= -->
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Data Transaksi</h3>

				<table class="datatable" style="width: 100%;">
					<thead>
						<tr>
							<th width="35px">NO</th>
							<th>ID Barang</th>
							<th>Nama Barang</th>
							<th>Jumlah Beli</th>
							<th>Total Harga</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody id="contenth">
						<?php
						$trx = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y");
						$data = $root->con->query("
							SELECT 
								barang.nama_barang,
								tempo.id_subtransaksi,
								tempo.id_barang,
								tempo.jumlah_beli,
								tempo.total_harga 
							FROM tempo 
							INNER JOIN barang ON barang.id_barang = tempo.id_barang 
							WHERE trx = '$trx'
						");

						$getsum = $root->con->query("SELECT SUM(total_harga) AS grand_total FROM tempo WHERE trx='$trx'");
						$getsum1 = $getsum->fetch_assoc();

						$no = 1;
						while ($f = $data->fetch_assoc()) {
						?>
							<tr>
								<td><?= $no++ ?></td>
								<td><?= $f['id_barang'] ?></td>
								<td><?= $f['nama_barang'] ?></td>
								<td><?= $f['jumlah_beli'] ?></td>
								<td>Rp. <?= number_format($f['total_harga']) ?></td>
								<td>
									<a href="handler.php?action=hapus_tempo&id_tempo=<?= $f['id_subtransaksi'] ?>&id_barang=<?= $f['id_barang'] ?>&jumbel=<?= $f['jumlah_beli'] ?>" class="btn redtbl">
										<span class="btn-hapus-tooltip">Cancel</span>
										<i class="fa fa-close"></i>
									</a>
								</td>
							</tr>
						<?php } ?>
					</tbody>

					<!-- ======== RINGKASAN TOTAL TRANSAKSI ========= -->
					<tr>
						<td colspan="3"></td>
						<td>Total :</td>
						<td>Rp. <?= number_format($getsum1['grand_total']) ?></td>
					</tr>

					<tr>
						<td colspan="3"></td>
						<td>(Diskon) Potongan Harga :</td>
						<td><input type="number" id="dis" placeholder="Masukkan Nominal"></td>
					</tr>

					<tr>
						<?php if ($getsum1['grand_total'] > 0) { ?>
							<td colspan="3"></td>
							<td>Grand Total :</td>
							<td id="text_total">Rp. <?= number_format($getsum1['grand_total']) ?></td>
							<input type="hidden" id="total" value="<?= $getsum1['grand_total'] ?>">
							<input type="hidden" id="temp_total" value="<?= $getsum1['grand_total'] ?>">
							<td></td>
						<?php } else { ?>
							<td colspan="6">Data masih kosong</td>
						<?php } ?>
					</tr>

					<tr>
						<td colspan="3"></td>
						<td>Bayar :</td>
						<td><input type="number" id="cash"></td>
					</tr>

					<tr>
						<td colspan="3"></td>
						<td>Kembalian :</td>
						<td>
							<p id="kembalian">Rp.</p>
						</td>
					</tr>
				</table>

				<br>

				<!-- ======== FORM PROSES TRANSAKSI ========= -->
				<form class="form-input" action="handler.php?action=selesai_transaksi" method="post">
					<label>Nama Pembeli :</label>
					<input required type="text" name="nama_pembeli">
					<input type="hidden" name="bayar" value="0">
					<input type="hidden" name="diskon" value="0">
					<input type="hidden" name="total_bayar" value="<?= $getsum1['grand_total'] ?>">
					<button class="btnblue" id="prosestran" type="submit">
						<i class="fa fa-save"></i> Proses Transaksi
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- ======== SCRIPT INTERAKTIF TRANSAKSI ========= -->
<script type="text/javascript">
	$(document).ready(function() {

		// Aktifkan Select2 untuk pencarian barang
		$(".select2-barang").select2({
			minimumInputLength: 1
		});

		$('#prosestran').prop('disabled', true);
		var change = 0;

		// ======== EVENT DISKON (UPDATE TOTAL) =========
		$('#dis').on('input', function() {
			let diskon = parseInt($('#dis').val()) || 0;
			let totalAwal = parseInt($('#temp_total').val());
			let total = totalAwal - diskon;

			if (diskon > totalAwal) {
				diskon = totalAwal;
				total = 0;
			}

			$('#text_total').text("Rp. " + total.toLocaleString());
			$('#total').val(total);
			$('input[name=total_bayar]').val(total);
			$('input[name=diskon]').val(diskon);
		});

		// ======== EVENT BAYAR (VALIDASI & HITUNG KEMBALIAN) =========
		$('#cash').change(function() {
			if (parseInt($('#total').val()) > parseInt($('#cash').val())) {
				alert('UANG KURANG! Mohon ulangi.');
				$('#cash').val(0);
				change = 0;
				$('#prosestran').prop('disabled', true);
			} else {
				change = $('#cash').val() - $('#total').val();
				$('input[name=bayar]').val($('#cash').val());
				$('#prosestran').prop('disabled', false);
			}
			$('#kembalian').text("Rp. " + change.toLocaleString());
		});

		// ======== FUNGSI SCANNER BARCODE =========
		document.addEventListener("DOMContentLoaded", function() {
			const barcodeInput = document.getElementById('barcode-input');

			// Fokus terus di input barcode agar scanner bisa input otomatis
			function keepFocus() {
				barcodeInput.focus();
				setTimeout(keepFocus, 500);
			}
			keepFocus();

			// Saat scanner kirim "Enter", proses datanya
			barcodeInput.addEventListener("keydown", function(e) {
				if (e.key === "Enter") {
					e.preventDefault();
					const kode = barcodeInput.value.trim();
					barcodeInput.value = "";
					if (kode !== "") {
						tambahBarangByScan(kode);
					}
				}
			});

			// Fungsi AJAX untuk menambah barang berdasarkan barcode
			function tambahBarangByScan(barcode) {
				$.ajax({
					url: "handler.php?action=tambah_scan",
					method: "POST",
					data: {
						barcode: barcode
					},
					success: function(res) {
						try {
							const data = JSON.parse(res);
							if (data.status === "ok") {
								location.reload(); // reload tabel transaksi
							} else {
								alert(data.msg || "Barang tidak ditemukan!");
							}
						} catch (e) {
							console.log(res);
							alert("Terjadi kesalahan saat memproses scan!");
						}
					},
					error: function() {
						alert("Gagal terhubung ke server!");
					}
				});
			}
		});

		console.log(change);
	});
</script>

<?php include "foot.php"; ?>