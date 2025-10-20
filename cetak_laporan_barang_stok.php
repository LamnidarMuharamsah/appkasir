<?php
require('assets/lib/fpdf.php');

class PDF extends FPDF
{
	// ===========================================
	// BAGIAN HEADER PDF (Judul dan Identitas Toko)
	// ===========================================
	function Header()
	{
		// Judul utama
		$this->SetFont('Arial', 'B', 30);
		$this->Cell(30, 10, 'RESNLIGHT');
		$this->Ln(10);

		// Alamat toko
		$this->SetFont('Arial', 'I', 10);
		$this->Cell(30, 10, 'JL Amin Jasuta No.1069');
		$this->Ln(5);

		// Kontak
		$this->Cell(30, 10, 'Telp/Fax : 087771111761');
		$this->Line(10, 40, 200, 40);
		$this->Ln(5);

		// Subjudul laporan
		$this->SetFont('Arial', 'I', 10);
		$this->Cell(30, 10, 'Data Stok Modal Barang');

		// Posisi tanggal di sebelah kanan
		$this->Cell(130);
		$this->SetFont('Arial', '', 10);
		$this->Cell(4, 10, 'Serang, ' . date("d-m-Y"));

		// Garis bawah header
		$this->Line(10, 40, 200, 40);
		$this->Ln(15);
	}

	// ===========================================
	// AMBIL DATA BARANG DARI DATABASE
	// ===========================================
	function data_barang()
	{
		// Koneksi ke database
		$con = mysqli_connect("localhost", "root", "", "resnlight");

		// Query ambil data barang dan kategori
		$query = "SELECT 
				barang.id_barang,
				barang.nama_barang,
				kategori.nama_kategori,
				barang.stok,
				barang.harga_beli,
				barang.harga_jual,
				barang.date_added
			FROM barang
			INNER JOIN kategori ON barang.id_kategori = kategori.id_kategori
			ORDER BY barang.id_barang ASC
		";

		// Eksekusi query
		$data = mysqli_query($con, $query);

		// Simpan hasil query ke array
		while ($r = mysqli_fetch_array($data)) {
			$hasil[] = $r;
		}

		return $hasil;
	}

	// ===========================================
	// BUAT TABEL DATA STOK BARANG DI PDF
	// ===========================================
	function set_table($header, $data)
	{
		// Header tabel
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(10, 7, "No", 1);
		$this->Cell(30, 7, $header[1], 1);
		$this->Cell(40, 7, $header[2], 1);
		$this->Cell(15, 7, $header[0], 1);
		$this->Cell(25, 7, $header[3], 1);
		$this->Cell(30, 7, $header[4], 1);
		$this->Cell(39, 7, $header[5], 1);
		$this->Ln();

		// Isi tabel
		$this->SetFont('Arial', '', 9);
		$no = 1;
		$sum_stok = 0;
		$sum_modal = 0;

		foreach ($data as $row) {
			// Baris data
			$this->Cell(10, 7, $no++, 1);
			$this->Cell(30, 7, $row['id_barang'], 1);
			$this->Cell(40, 7, $row['nama_barang'], 1);
			$this->Cell(15, 7, $row['stok'], 1);
			$this->Cell(25, 7, $row['nama_kategori'], 1);
			$this->Cell(30, 7, "Rp. " . number_format($row['harga_beli']), 1);
			$this->Cell(39, 7, "Rp. " . number_format($row['stok'] * $row['harga_beli']), 1);
			$this->Ln();

			// Total keseluruhan
			$sum_stok += $row['stok'];
			$sum_modal += ($row['stok'] * $row['harga_beli']);
		}

		// Baris total di akhir tabel
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(10, 7);
		$this->Cell(30, 7);
		$this->Cell(40, 7, "Total", 1);
		$this->Cell(15, 7, number_format($sum_stok), 1);
		$this->Cell(25, 7, "", 1);
		$this->Cell(30, 7, "", 1);
		$this->Cell(39, 7, "Rp. " . number_format($sum_modal), 1);
		$this->Ln(25);
	}
}

// ===========================================
// EKSEKUSI PEMBUATAN FILE PDF
// ===========================================
$pdf = new PDF();
$pdf->SetTitle('Cetak Data Stok Barang');

// Header kolom tabel
$header = array(
	'Stock',
	'Kode Barang',
	'Nama Barang',
	'Kategori',
	'Harga Modal',
	'Total Aset Modal',
	'Tgl Ditambahkan'
);

// Ambil data dari database
$data = $pdf->data_barang();

// Buat halaman baru
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(1);

// Cetak tabel ke PDF
$pdf->set_table($header, $data);

// Cek folder output, buat jika belum ada
$outputDir = 'resnlight/Stok_Barang/';
if (!is_dir($outputDir)) {
	mkdir($outputDir, 0777, true);
}

// Simpan hasil PDF ke folder dengan nama tanggal
$pdf->Output('', $outputDir . date("d-m-Y") . '.pdf');
