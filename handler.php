<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include koneksi & fungsi utama
include "root.php";

// Pastikan parameter 'action' tersedia
if (isset($_GET['action'])) {
	$action = $_GET['action'];

	// ===============================
	// LOGIN USER
	// ===============================
	if ($action == "login") {
		$root->login($_POST['username'], $_POST['pass'], $_POST['loginas']);
	}

	// ===============================
	// LOGOUT USER
	// ===============================
	elseif ($action == "logout") {
		session_start();
		session_destroy();
		$root->redirect("index.php");
	}

	// ===============================
	// TAMBAH BARANG
	// ===============================
	elseif ($action == "tambah_barang") {
		$root->tambah_barang(
			$_POST['id_barang'],
			$_POST['nama_barang'],
			$_POST['stok'],
			$_POST['harga_beli'],
			$_POST['harga_jual'],
			$_POST['kategori']
		);
	}

	// ===============================
	// KATEGORI BARANG
	// ===============================
	elseif ($action == "tambah_kategori") {
		$root->tambah_kategori($_POST['nama_kategori']);
	} elseif ($action == "hapus_kategori") {
		$root->hapus_kategori($_GET['id_kategori']);
	} elseif ($action == "edit_kategori") {
		$root->aksi_edit_kategori($_POST['id_kategori'], $_POST['nama_kategori']);
	}

	// ===============================
	// BARANG (EDIT / HAPUS)
	// ===============================
	elseif ($action == "hapus_barang") {
		$root->hapus_barang($_GET['id_barang']);
	} elseif ($action == "edit_barang") {
		$root->aksi_edit_barang(
			$_POST['id_barang'],
			$_POST['nama_barang'],
			$_POST['stok'],
			$_POST['harga_beli'],
			$_POST['harga_jual'],
			$_POST['kategori']
		);
	}

	// ===============================
	// KASIR & ADMIN
	// ===============================
	elseif ($action == "tambah_kasir") {
		$root->tambah_kasir($_POST['nama_kasir'], $_POST['password']);
	} elseif ($action == "hapus_user") {
		$root->hapus_user($_GET['id_user']);
	} elseif ($action == "edit_kasir") {
		$root->aksi_edit_kasir($_POST['nama_kasir'], $_POST['password'], $_POST['id']);
	} elseif ($action == "edit_admin") {
		$root->aksi_edit_admin($_POST['username'], $_POST['password']);
	}

	// ===============================
	// RESET ADMIN (USERNAME & PASSWORD)
	// ===============================
	elseif ($action == "reset_admin") {
		$pass = sha1("admin");
		$q = $root->con->query("
            UPDATE user 
            SET username='admin', password='$pass', date_created=date_created 
            WHERE id='1'
        ");
		if ($q === TRUE) {
			$root->alert("Admin berhasil direset, username & password = 'admin'");
			session_start();
			session_destroy();
			$root->redirect("index.php");
		}
	}

	// ===============================
	// TEMPO (TAMBAH / HAPUS)
	// ===============================
	elseif ($action == "tambah_tempo") {
		$root->tambah_tempo($_POST['id_barang'], $_POST['jumlah'], $_POST['trx']);
	} elseif ($action == "hapus_tempo") {
		$root->hapus_tempo($_GET['id_tempo'], $_GET['id_barang'], $_GET['jumbel']);
	}

	// ===============================
	// SCAN BARCODE & TAMBAH KE TEMPO
	// ===============================
	elseif ($action == "tambah_scan") {
		session_start();
		$barcode = $_POST['barcode'];
		$trx = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y");

		// Cek barang berdasarkan ID barcode
		$cek = $root->con->query("SELECT * FROM barang WHERE id_barang='$barcode' LIMIT 1");

		if ($cek->num_rows > 0) {
			$barang = $cek->fetch_assoc();
			$id_barang = $barang['id_barang'];
			$harga_jual = $barang['harga_jual'];

			// Cek apakah barang sudah ada di tempo
			$cekTempo = $root->con->query("SELECT * FROM tempo WHERE id_barang='$id_barang' AND trx='$trx' LIMIT 1");

			if ($cekTempo->num_rows > 0) {
				// Jika sudah ada, update jumlah beli +1 dan total harga
				$tempo = $cekTempo->fetch_assoc();
				$jumlah_baru = $tempo['jumlah_beli'] + 1;
				$total_baru = $jumlah_baru * $harga_jual;

				$root->con->query("
                    UPDATE tempo 
                    SET jumlah_beli='$jumlah_baru', total_harga='$total_baru' 
                    WHERE id_barang='$id_barang' AND trx='$trx'
                ");
			} else {
				// Jika belum ada, tambahkan barang baru ke tempo
				$root->con->query("
                    INSERT INTO tempo 
                    SET id_barang='$id_barang', jumlah_beli='1', total_harga='$harga_jual', trx='$trx'
                ");
			}

			echo json_encode(["status" => "ok"]);
		} else {
			echo json_encode(["status" => "error", "msg" => "Barang tidak ditemukan"]);
		}
		exit;
	}

	// ===============================
	// SELESAI TRANSAKSI
	// ===============================
	elseif ($action == "selesai_transaksi") {
		session_start();

		// Buat kode transaksi unik
		$trx = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y/h/i/s");
		$trx2 = date("d") . "/AF/" . $_SESSION['id'] . "/" . date("y");

		// Ambil diskon & bayar dari form
		$diskon = isset($_POST['diskon']) ? (int)$_POST['diskon'] : 0;
		$bayar  = isset($_POST['bayar']) ? (int)$_POST['bayar'] : 0;

		// Hitung total dari tempo
		$get_total = $root->con->query("SELECT SUM(total_harga) as grand_total FROM tempo WHERE trx='$trx2'");
		$total_row = $get_total->fetch_assoc();
		$grand_total = (int)$total_row['grand_total'];

		// Pastikan diskon tidak lebih besar dari total
		if ($diskon > $grand_total) {
			$diskon = $grand_total;
		}

		$total_bayar = $grand_total - $diskon;

		// Simpan ke tabel transaksi
		$root->con->query("
            INSERT INTO transaksi 
            SET kode_kasir='$_SESSION[id]', total_bayar='$total_bayar', no_invoice='$trx', nama_pembeli='$_POST[nama_pembeli]'
        ");

		// Ambil ID transaksi baru
		$get1 = $root->con->query("SELECT * FROM transaksi WHERE no_invoice='$trx'");
		$datatrx = $get1->fetch_assoc();
		$id_transaksi2 = $datatrx['id_transaksi'];

		// Masukkan data ke sub_transaksi
		$query2 = $root->con->query("SELECT * FROM tempo WHERE trx='$trx2'");
		while ($f = $query2->fetch_assoc()) {
			$root->con->query("
                INSERT INTO sub_transaksi 
                SET id_barang='$f[id_barang]',
                    id_transaksi='$id_transaksi2',
                    jumlah_beli='$f[jumlah_beli]',
                    total_harga='$f[total_harga]',
                    no_invoice='$trx',
                    bayar='$bayar',
                    diskon='$diskon'
            ");
		}

		// Hapus data tempo setelah transaksi selesai
		$root->con->query("DELETE FROM tempo WHERE trx='$trx2'");

		$root->alert("Transaksi berhasil");
		$root->redirect("transaksi.php");
	}

	// ===============================
	// HAPUS TRANSAKSI
	// ===============================
	elseif ($action == "delete_transaksi") {
		$q1 = $root->con->query("DELETE FROM transaksi WHERE id_transaksi='$_GET[id]'");
		$q2 = $root->con->query("DELETE FROM sub_transaksi WHERE id_transaksi='$_GET[id]'");

		if ($q1 === TRUE && $q2 === TRUE) {
			$root->alert("Transaksi No $_GET[id] Berhasil Dihapus");
			$root->redirect("laporan.php");
		}
	}
} else {
	echo "No direct script access allowed";
}
