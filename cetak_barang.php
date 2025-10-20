<?php
// ============================================================
// File   : cetak_data_barang.php
// Fungsi : Mencetak laporan data barang dalam format PDF
// Dibuat : Menggunakan library FPDF
// ============================================================

// Memanggil library FPDF
require('assets/lib/fpdf.php');

// ============================================================
// CLASS PDF UTAMA
// ============================================================
class PDF extends FPDF
{
	// ------------------------------
	// Bagian HEADER (Kop Laporan)
	// ------------------------------
	function Header()
	{
		// Judul toko
		$this->SetFont('Arial', 'B', 30);
		$this->Cell(30, 10, 'RESNLIGHT'); // Nama toko

		$this->Ln(10); // Pindah baris
		$this->SetFont('Arial', 'I', 10);
		$this->Cell(30, 10, 'JL Amin Jasuta No.1069'); // Alamat toko

		$this->Ln(5);
		$this->Cell(30, 10, 'Telp/Fax : 087771111761'); // Kontak toko

		// Garis pemisah
		$this->Line(10, 40, 200, 40);

		$this->Ln(5);
		$this->SetFont('Arial', 'I', 10);
		$this->Cell(30, 10, 'Data Barang'); // Judul laporan

		// Posisi tanggal di kanan atas
		$this->Cell(130);
		$this->SetFont('Arial', '', 10);
		$this->Cell(4, 10, 'Serang, ' . date("d-m-Y"));

		// Garis kedua (penegas header)
		$this->Line(10, 40, 200, 40);

		$this->Ln(15); // Jarak ke isi tabel
	}

	// ------------------------------
	// Mengambil data dari database
	// ------------------------------
	function data_barang()
	{
		// Koneksi ke database
		$con = mysqli_connect("localhost", "root", "", "resnlight");

		// Query untuk mengambil data barang beserta kategorinya
		$data = mysqli_query($con, "
			SELECT 
				barang.id_barang,
				barang.nama_barang,
				kategori.nama_kategori,
				barang.stok,
				barang.harga_beli,
				barang.harga_jual,
				barang.date_added 
			FROM barang 
			INNER JOIN kategori 
				ON barang.id_kategori = kategori.id_kategori 
			ORDER BY barang.id_barang ASC
		");

		// Menyimpan hasil ke array
		$hasil = [];
		while ($r = mysqli_fetch_array($data)) {
			$hasil[] = $r;
		}

		return $hasil;
	}

	// ------------------------------
	// Membuat tabel laporan barang
	// ------------------------------
	function set_table($header, $data)
	{
		// Header tabel
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(10, 7, "No", 1);                 // Kolom nomor
		$this->Cell(25, 7, $header[1], 1);          // Kode Barang
		$this->Cell(35, 7, $header[2], 1);          // Nama Barang
		$this->Cell(12, 7, $header[0], 1);          // Stock
		$this->Cell(24, 7, $header[3], 1);          // Kategori
		$this->Cell(27, 7, $header[4], 1);          // Harga Modal
		$this->Cell(27, 7, $header[5], 1);          // Harga Jual
		$this->Cell(30, 7, $header[6], 1);          // Tanggal Ditambahkan
		$this->Ln();

		// Isi tabel
		$this->SetFont('Arial', '', 9);
		$no = 1;

		foreach ($data as $row) {
			$this->Cell(10, 7, $no++, 1);
			$this->Cell(25, 7, $row['id_barang'], 1);
			$this->Cell(35, 7, $row['nama_barang'], 1);
			$this->Cell(12, 7, $row['stok'], 1);
			$this->Cell(24, 7, $row['nama_kategori'], 1);
			$this->Cell(27, 7, "Rp. " . number_format($row['harga_beli'], 0, ',', '.'), 1);
			$this->Cell(27, 7, "Rp. " . number_format($row['harga_jual'], 0, ',', '.'), 1);
			$this->Cell(30, 7, date("d-m-Y", strtotime($row['date_added'])), 1);
			$this->Ln();
		}
	}
}

// ============================================================
// BAGIAN EKSEKUSI / OUTPUT PDF
// ============================================================

// Membuat objek PDF baru
$pdf = new PDF();

// Judul file PDF
$pdf->SetTitle('Cetak Data Barang');

// Header tabel
$header = array('Stock', 'Kode Barang', 'Nama Barang', 'Kategori', 'Harga Modal', 'Harga Jual', 'Tgl Ditambahkan');

// Ambil data dari database
$data = $pdf->data_barang();

// Buat halaman baru
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(1);

// Cetak tabel ke PDF
$pdf->set_table($header, $data);

// Simpan file ke folder / tampilkan di browser
$pdf->Output('', 'resnlight/Barang/' . date("d-m-Y") . '.pdf');
