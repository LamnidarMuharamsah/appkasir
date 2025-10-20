<?php
include "root.php";
include "check_maintenance.php";
session_start();

// Jika user sudah login, langsung arahkan ke home
if (isset($_SESSION['username'])) {
	$root->redirect("home.php");
}
?>
<!DOCTYPE html>
<html>

<head>
	<title>Login | Aplikasi Penjualan</title>

	<!-- ================== STYLE LOGIN PAGE ================== -->
	<style type="text/css">
		/* Import font utama */
		@font-face {
			font-family: titillium;
			src: url(assets/TitilliumWeb-SemiBold.ttf);
		}

		/* Reset default */
		* {
			margin: 0;
			padding: 0;
			font-family: titillium;
		}

		/* Efek animasi muncul */
		@keyframes muncul {
			0% {
				opacity: 0;
			}

			100% {
				opacity: 1;
			}
		}

		/* Animasi fade-in pada body */
		body {
			overflow: hidden;
			animation-name: muncul;
			animation-duration: 2s;
		}

		.both {
			clear: both;
		}

		/* Background halaman login */
		.loginpage {
			position: fixed;
			background: url("assets/img/bg2.jpg") no-repeat;
			height: 100%;
			width: 100%;
			background-size: cover;
		}

		.padding {
			padding: 80px 25px;
		}

		/* Container form login */
		.login {
			float: right;
			width: 400px;
			background: #fff;
			height: 100%;
		}

		/* Style untuk input, select, dan button */
		.login input,
		.login select,
		.login button {
			width: 100%;
			box-sizing: border-box;
			margin-bottom: 20px;
			border: 0px;
			padding: 10px;
			border-bottom: 1px solid #e4e7ea;
			outline: 0;
			color: #565656;
			font-size: 14px;
		}

		/* Efek focus pada input */
		.login input:focus,
		.login select:focus {
			border-bottom: 1px solid #707cd2;
			transition: 0.2s;
		}

		.login select {
			cursor: pointer;
		}

		/* Tombol login */
		.login button {
			cursor: pointer;
			background: #41b3f9;
			color: #fff;
			font-size: 20px;
			border-radius: 3px;
		}

		.login button:hover {
			background: #5bc0de;
		}

		form {
			margin-top: 70px;
		}

		h3 {
			text-align: center;
		}

		/* Status pesan login (error/sukses) */
		#status {
			width: 100%;
			color: #565656;
			font-size: 15px;
			display: none;
			box-sizing: border-box;
			border-radius: 3px;
		}

		.red {
			color: #c7254e;
			background: #f9f2f4;
			padding: 10px;
			border-radius: 3px;
		}

		.green {
			color: rgb(1, 186, 56);
			background: rgb(230, 255, 230);
			padding: 10px;
			border-radius: 3px;
		}

		.link-forgot {
			color: #565656;
			padding: 0px 0px 20px 0px;
			display: inline-block;
		}
	</style>

	<!-- ================== IMPORT ASSETS ================== -->
	<link rel="stylesheet" type="text/css" href="assets/awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/wow/animate.css">
	<script type="text/javascript" src="assets/jquery.js"></script>

	<!-- ================== SCRIPT LOGIN AJAX ================== -->
	<script type="text/javascript">
		$(document).ready(function() {
			$("#loginapp").submit(function() {
				$.ajax({
					type: 'POST',
					url: 'handler.php?action=login',
					data: $(this).serialize(),
					success: function(data) {
						// Tampilkan pesan status login
						$("#status").fadeIn(100).html(data);

						// Sembunyikan pesan setelah 3 detik
						window.setTimeout(function() {
							$('#status').fadeOut(100);
						}, 3000);
					}
				});
				return false; // Cegah form submit biasa
			});
		});
	</script>
</head>

<body>
	<div class="loginpage">
		<div class="login">
			<div class="padding">
				<h3>Login Aplikasi</h3>
				<h3><b>RESNLIGHT CLOTHING</b></h3>

				<!-- ================== FORM LOGIN ================== -->
				<form id="loginapp">
					<input type="text" name="username" placeholder="Username" required>
					<input type="password" name="pass" placeholder="Password" required>

					<select name="loginas" required>
						<option value="1">Admin</option>
						<option value="2">Kasir</option>
					</select>

					<button type="submit">
						<i class="fa fa-sign-in"></i> Login
					</button>

					<div class="both"></div>
				</form>

				<!-- Status hasil login (error / sukses) -->
				<div id="status"></div>
			</div>
		</div>
	</div>
</body>

</html>