<script type="text/javascript">
	// Set judul halaman dan tandai menu aktif
	document.title = "Edit Barang";
	document.getElementById('barang').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Barang</h3>

				<?php
				// Ambil data barang berdasarkan ID
				$f = $root->edit_barang($_GET['id_barang']);
				?>

				<!-- Form edit data barang -->
				<form class="form-input" method="post" action="handler.php?action=edit_barang" style="padding-top: 30px;">

					<!-- ID Barang (hidden untuk dikirim ke server) -->
					<input type="hidden" name="id_barang" value="<?= $f['id_barang'] ?>">

					<!-- ID Barang (hanya ditampilkan, tidak bisa diubah) -->
					<input type="text" placeholder="ID Kategori" disabled value="ID barang : <?= $f['id_barang'] ?>">

					<!-- Nama Barang -->
					<label>Nama Barang :</label>
					<input type="text" name="nama_barang" placeholder="Nama Barang" required value="<?= $f['nama_barang'] ?>">

					<!-- Stok -->
					<label>Stok :</label>
					<input type="number" name="stok" placeholder="Stok" required value="<?= $f['stok'] ?>">

					<!-- Harga Modal -->
					<label>Harga Modal :</label>
					<input type="number" name="harga_beli" placeholder="Harga Modal" required value="<?= $f['harga_beli'] ?>">

					<!-- Harga Jual -->
					<label>Harga Jual :</label>
					<input type="number" name="harga_jual" placeholder="Harga Jual" required value="<?= $f['harga_jual'] ?>">

					<!-- Pilihan Kategori -->
					<label>Kategori :</label>
					<select style="width: 372px; cursor: pointer;" required name="kategori">
						<option value="">Pilih Kategori :</option>
						<?php $root->tampil_kategori3($_GET['id_barang']); ?>
					</select>

					<!-- Tombol Simpan & Batal -->
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