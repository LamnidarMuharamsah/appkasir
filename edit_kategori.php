<script type="text/javascript">
	// Set judul halaman dan tandai menu kategori sebagai aktif
	document.title = "Edit Kategori Barang";
	document.getElementById('kategori').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Kategori</h3>

				<?php
				// Ambil data kategori berdasarkan ID yang dikirim lewat URL
				$f = $root->edit_kategori($_GET['id_kategori']);
				?>

				<!-- Form untuk mengedit data kategori -->
				<form class="form-input" method="post" action="handler.php?action=edit_kategori">

					<!-- ID kategori (hanya ditampilkan, tidak bisa diubah) -->
					<input
						type="text"
						placeholder="ID Kategori"
						disabled
						value="ID kategori : <?= $f['id_kategori'] ?>">

					<!-- Input nama kategori -->
					<label>Nama Kategori :</label>
					<input
						type="text"
						name="nama_kategori"
						placeholder="Nama Kategori"
						required
						value="<?= $f['nama_kategori'] ?>">

					<!-- ID kategori (hidden) untuk dikirim ke handler -->
					<input
						type="hidden"
						name="id_kategori"
						value="<?= $f['id_kategori'] ?>">

					<!-- Tombol aksi -->
					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Update
					</button>
					<a href="kategori.php" class="btnblue" style="background: #f33155">
						<i class="fa fa-close"></i> Batal
					</a>
				</form>

			</div>
		</div>
	</div>
</div>