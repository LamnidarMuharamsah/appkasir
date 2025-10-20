<?php include "head.php"; ?>

<!-- ==============================
     🔹 UBAH TITLE HALAMAN
     ============================== -->
<script type="text/javascript">
	document.title = "Setting akun admin";
</script>

<!-- ==============================
     🔹 KONTEN UTAMA
     ============================== -->
<div class="content">
	<div class="padding">
		<div class="bgwhite">
			<div class="padding">

				<!-- Judul Halaman -->
				<h3 class="jdl">Setting akun admin</h3>
				<span class="label">
					* Silakan ubah username atau password admin sesuai kebutuhan.
				</span>

				<!-- ==============================
				     🔹 FORM UBAH ADMIN
				     ============================== -->
				<form class="form-input" method="post" action="handler.php?action=edit_admin" style="padding-top: 30px;">

					<?php
					// Ambil data admin dari fungsi edit_admin()
					$f = $root->edit_admin();
					?>

					<!-- Input Username -->
					<label>Username :</label>
					<input type="text" name="username" value="<?= $f['username'] ?>">

					<!-- Input Password -->
					<label>Password Baru :</label>
					<input type="text" name="password">

					<!-- Keterangan Tambahan -->
					<label>* Password tidak bisa ditampilkan karena terenkripsi</label><br>
					<label>* Kosongkan kolom password jika tidak ingin mengubah password</label><br><br>

					<!-- Tombol Aksi -->
					<button class="btnblue" type="submit">
						<i class="fa fa-save"></i> Simpan
					</button>

					<!-- Tombol Reset Akun Admin -->
					<a onclick="return confirm('Yakin ingin reset akun admin?')"
						href="handler.php?action=reset_admin"
						class="btnblue"
						style="background: #f33155">
						<i class="fa fa-rotate-left"></i> Reset Akun
					</a>

					<!-- Tombol Batal -->
					<a href="home.php"
						class="btnblue"
						style="background: #f33155">
						<i class="fa fa-close"></i> Batal
					</a>

				</form>
				<!-- ============================== -->
			</div>
		</div>
	</div>
</div>

<?php include "foot.php"; ?>