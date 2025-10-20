<?php
require('assets/lib/fpdf.php'); // Import library FPDF untuk generate PDF

class PDF extends FPDF
{
    // =========================
    // HEADER INVOICE
    // =========================
    function Header()
    {
        // Judul toko
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(10, 0, 'RESNLIGHT');

        // Alamat toko
        $this->Ln(3);
        $this->SetFont('Arial', 'i', 3.5);
        $this->Cell(10, 0.5, 'Jl Amin Jasuta No.1069 (BRIMOB) Serang-Banten');

        // Tanggal transaksi
        $this->Ln(2);
        $this->SetFont('Arial', '', 4);
        $this->Cell(10, 0.5, 'Tanggal');
        $this->Cell(1, 0.5, ':');
        $this->Cell(10, 0.5, base64_decode($_GET['uuid']));

        // Nomor invoice
        $this->Ln(2);
        $this->Cell(10, 0.5, 'No Invoice');
        $this->Cell(1, 0.5, ':');
        $this->Cell(10, 0.5, base64_decode($_GET['inf']));

        // Nama konsumen
        $this->Ln(1.5);
        $this->Cell(10, 0.5, 'Konsumen');
        $this->Cell(1, 0.5, ':');
        $this->Cell(10, 0.5, base64_decode($_GET['id-uid']));

        // Kasir (ambil dari session aktif)
        $this->Ln(1.5);
        if (!isset($_SESSION)) session_start();
        $this->Cell(10, 0.5, 'Kasir');
        $this->Cell(1, 0.5, ':');
        $this->Cell(10, 0.5, $_SESSION['username']);

        // Instagram toko
        $this->Ln(1.5);
        $this->Cell(10, 0.5, 'Instagram');
        $this->Cell(1, 0.5, ':');
        $this->Cell(10, 0.5, 'resnlight_official');

        // Garis pembatas bawah header
        $this->Line(1, 17, 34, 17);
    }

    // =========================
    // LOAD DATA TRANSAKSI DARI DATABASE
    // =========================
    function LoadData()
    {
        // Koneksi ke database
        $con = mysqli_connect("localhost", "root", "", "resnlight");

        // Ambil ID transaksi dari parameter URL (didecode dari base64)
        $id = base64_decode($_GET['oid']);

        // Query untuk ambil data barang dan transaksi
        $query = "
            SELECT 
                sub_transaksi.jumlah_beli, 
                barang.nama_barang, 
                barang.harga_jual, 
                sub_transaksi.total_harga, 
                sub_transaksi.bayar, 
                sub_transaksi.diskon 
            FROM sub_transaksi 
            INNER JOIN barang 
                ON barang.id_barang = sub_transaksi.id_barang 
            WHERE sub_transaksi.id_transaksi = '$id'
        ";

        $data = mysqli_query($con, $query);
        $hasil = [];

        // Simpan hasil query ke array
        while ($r = mysqli_fetch_array($data)) {
            $hasil[] = $r;
        }

        return $hasil;
    }

    // =========================
    // TABEL UTAMA TRANSAKSI
    // =========================
    function BasicTable($header, $data)
    {
        // Header kolom tabel
        $this->SetFont('Arial', 'B', 3.5);
        $this->Cell(10.5, 2, $header[0]);
        $this->Cell(7.5, 2, $header[1]);
        $this->Cell(2.5, 2, $header[2]);
        $this->Cell(7, 2, $header[3]);
        $this->Ln();

        // Variabel penampung sementara
        $bayar = 0;
        $diskon = 0;

        // Isi data tabel
        $this->SetFont('Arial', '', 3.5);
        foreach ($data as $row) {
            $this->Cell(10.5, 2, $row['nama_barang']);
            $this->Cell(7.5, 2, "Rp " . number_format($row['harga_jual'], 0, ',', '.'));
            $this->Cell(2.5, 2, $row['jumlah_beli']);
            $this->Cell(7, 2, "Rp " . number_format($row['total_harga'], 0, ',', '.'));
            $this->Ln();

            $bayar = $row['bayar']; // update nominal bayar terakhir
            $diskon += $row['diskon']; // akumulasi diskon semua item
        }

        // =========================
        // HITUNG TOTAL, DISKON, DAN KEMBALIAN
        // =========================
        $con = mysqli_connect("localhost", "root", "", "resnlight");
        $id = base64_decode($_GET['oid']);

        // Query total semua harga dan diskon
        $getsum = mysqli_query($con, "
            SELECT 
                SUM(total_harga) AS grand_total, 
                diskon
            FROM sub_transaksi 
            WHERE id_transaksi = '$id'
        ");
        $getsum1 = mysqli_fetch_array($getsum);

        // Perhitungan total dan kembalian
        $grand_total = $getsum1['grand_total'];
        $diskon = $getsum1['diskon'];
        $grand_after_diskon = $grand_total - $diskon;

        $this->Ln(2);
        $this->SetFont('Arial', 'B', 4);

        // Total harga semua barang
        $this->Cell(15, 0.5, 'Total ');
        $this->Cell(10, 0.5, ': Rp ' . number_format($grand_total, 0, ',', '.'));

        // Diskon nominal
        $this->Ln(2);
        $this->Cell(15, 0.5, 'Potongan Harga ');
        $this->Cell(10, 0.5, ': Rp ' . number_format($diskon, 0, ',', '.'));

        // Grand total (setelah diskon)
        $this->Ln(2);
        $this->Cell(15, 0.5, 'Grand Total ');
        $this->Cell(10, 0.5, ': Rp ' . number_format($grand_after_diskon, 0, ',', '.'));

        // Bayar
        $this->Ln(2);
        $this->Cell(15, 0.5, 'Bayar ');
        $this->Cell(10, 0.5, ': Rp ' . number_format($bayar, 0, ',', '.'));

        // Kembalian
        $this->Ln(2);
        $this->Cell(15, 0.5, 'Kembalian ');
        $this->Cell(10, 0.5, ': Rp ' . number_format($bayar - $grand_after_diskon, 0, ',', '.'));

        // Catatan kecil di bawah struk
        $this->Ln(2);
        $this->SetFont('Arial', 'I', 3);
        $this->Cell(0, 0.5, '* Barang yang sudah dibeli tidak dapat dikembalikan.', 0, 1, 'C');
    }
}

// =========================
// INISIALISASI PDF
// =========================
$pdf = new PDF("p", "mm", array(85, 35)); // Format kertas kecil (struk)
$pdf->SetMargins(2, 4, 10);
$pdf->SetTitle('Invoice : ' . base64_decode($_GET['inf']));
$pdf->AliasNbPages();

// Header tabel
$header = array('Nama Barang', 'Harga', 'Qty', 'Total');

// Load data transaksi
$data = $pdf->LoadData();

// Buat halaman baru & tampilkan tabel transaksi
$pdf->AddPage();
$pdf->Ln(2);
$pdf->BasicTable($header, $data);

// Simpan hasil PDF dengan nama file berdasarkan no invoice
$filename = base64_decode($_GET['inf']);
$pdf->Output('', 'RESNLIGHT/' . $filename . '.pdf');
