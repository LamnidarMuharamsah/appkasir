<!-- ==============================
     🔹 SETTING TITLE & ACTIVE MENU
     ============================== -->
<script type="text/javascript">
	document.title = "Tambah Barang";
	document.getElementById('barang').classList.add('active');
</script>

<!-- ==============================
     🔹 KONTEN UTAMA
     ============================== -->
<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Tambah Barang</h3>

				<?php
				// ==========================================
				// 🔹 Membuat Kode Barang Otomatis (ex: B0001)
				// ==========================================
				$query = $root->con->query("SELECT MAX(id_barang) FROM barang");
				$kode_faktur = mysqli_fetch_array($query);

				if ($kode_faktur) {
					// Ambil nilai angka setelah huruf "B"
					$nilai = substr($kode_faktur[0], 1);
					$kode = (int)$nilai;

					// Tambah 1 untuk ID baru
					$kode++;

					// Format ulang jadi "B000X"
					$auto_kode = "B" . str_pad($kode, 4, "0", STR_PAD_LEFT);
				} else {
					// Jika tabel masih kosong
					$auto_kode = "B0001";
				}
				?>

				<!-- ==============================
				     🔹 FORM TAMBAH BARANG
				     ============================== -->
				<form class="form-input" method="post" action="handler.php?action=tambah_barang">

					<!-- ID Barang (Otomatis) -->
					<input type="text"
						name="id_barang"
						value="<?= $auto_kode; ?>"
						required
						readonly>

					<!-- Nama Barang -->
					<input type="text"
						name="nama_barang"
						placeholder="Nama Barang"
						required>

					<!-- Stok -->
					<input type="number"
						name="stok"
						placeholder="Stok"
						required>

					<!-- Harga Modal -->
					<input type="number"
						name="harga_beli"
						placeholder="Harga Modal"
						required>

					<!-- Harga Jual -->
					<input type="number"
						name="harga_jual"
						placeholder="Harga Jual"
						required>

					<!-- Kategori Barang -->
					<select name="kategori" required style="width: 372px; cursor: pointer;">
						<option value="">Pilih Kategori :</option>
						<?php $root->tampil_kategori2(); ?>
					</select>

					<!-- Tombol Aksi -->
					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Simpan
					</button>

					<a href="barang.php" class="btnblue" style="background: #f33155">
						<i class="fa fa-close"></i> Batal
					</a>
				</form>

			</div>
		</div>
	</div>
</div>