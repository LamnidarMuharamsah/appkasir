<script type="text/javascript">
	// Set judul halaman dan tandai menu aktif
	document.title = "Edit Kasir";
	document.getElementById('users').classList.add('active');
</script>

<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">
				<h3 class="jdl">Edit Kasir</h3>

				<?php
				// Ambil data kasir berdasarkan ID
				$f = $root->edit_kasir($_GET['id_kasir']);
				?>

				<!-- Form untuk edit data kasir -->
				<form class="form-input" method="post" action="handler.php?action=edit_kasir">

					<!-- ID kasir (hidden input, dikirim ke handler untuk update data) -->
					<input type="hidden" name="id" value="<?= $f['id'] ?>">

					<!-- Username kasir -->
					<label>Username Kasir :</label>
					<input
						type="text"
						name="nama_kasir"
						placeholder="Username Kasir"
						required
						value="<?= $f['username'] ?>">

					<!-- Password kasir -->
					<label>Password :</label>
					<input
						type="text"
						name="password"
						placeholder="Password"
						autocomplete="off">

					<!-- Catatan mengenai password -->
					<label style="font-size: 12px; color: #555;">
						* Password tidak bisa ditampilkan karena terenkripsi
					</label><br>
					<label style="font-size: 12px; color: #555;">
						* Kosongkan form password jika tidak ingin mengubah password
					</label><br><br>

					<!-- Tombol aksi -->
					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Simpan
					</button>
					<a href="users.php" class="btnblue" style="background: #f33155">
						<i class="fa fa-close"></i> Batal
					</a>
				</form>

			</div>
		</div>
	</div>
</div>