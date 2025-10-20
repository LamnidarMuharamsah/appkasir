<?php
require('assets/lib/fpdf.php');

class PDF extends FPDF
{
    // ========================================================
    // BAGIAN HEADER PDF (Judul, Informasi Toko, dan Laporan)
    // ========================================================
    function Header()
    {
        // Judul utama
        $this->SetFont('Arial', 'B', 30);
        $this->Cell(30, 10, 'RESNLIGHT');
        $this->Ln(10);

        // Alamat
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(30, 10, 'JL Amin Jasuta No.1069');
        $this->Ln(5);

        // Kontak
        $this->Cell(30, 10, 'Telepon : 087771111761');
        $this->Ln(5);

        // Tanggal laporan dan jenis laporan dari form POST
        $this->Cell(30, 10, 'Data Laporan Tanggal : ' . $_POST['tgl_laporan']);
        $this->Ln(5);
        $this->Cell(30, 10, 'Jenis : ' . $_POST['jenis_laporan']);

        // Tanggal cetak di sebelah kanan
        $this->Cell(130);
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 10, 'Serang, ' . date("d-m-Y"));

        // Garis bawah header
        $this->Line(10, 45, 200, 45);
        $this->Ln(15);
    }

    // ========================================================
    // FUNGSI AMBIL DATA TRANSAKSI DARI DATABASE
    // ========================================================
    function data_barang()
    {
        $con = mysqli_connect("localhost", "root", "", "resnlight");
        $tanggal = $_POST['tgl_laporan'];

        // Jenis laporan: perhari
        if ($_POST['jenis_laporan'] == "perhari") {
            $split1 = explode('-', $tanggal);
            $tanggal = $split1[2] . "-" . $split1[1] . "-" . $split1[0];
        }
        // Jenis laporan: perbulan
        else if ($_POST['jenis_laporan'] == "perbulan") {
            $split1 = explode('-', $tanggal);
            $tanggal = $split1[1] . "-" . $split1[0];
        }
        // Jenis laporan: pertahun
        else if ($_POST['jenis_laporan'] == "pertahun") {
            $split1 = explode('-', $tanggal);
            $tanggal = $split1[0];
        }

        // Query utama ambil transaksi berdasarkan tanggal
        $query = mysqli_query($con, "SELECT 
                t.id_transaksi,
                t.tgl_transaksi,
                t.no_invoice,
                SUM(b.harga_beli * st.jumlah_beli) AS modal,
                t.total_bayar,
                t.nama_pembeli,
                user.username 
            FROM transaksi t
            JOIN sub_transaksi st ON t.id_transaksi = st.id_transaksi
            JOIN barang b ON b.id_barang = st.id_barang
            JOIN user ON t.kode_kasir = user.id 
            WHERE t.tgl_transaksi LIKE '%$tanggal%' 
            GROUP BY st.no_invoice ASC
        ");

        // Simpan hasil query ke array
        while ($r = mysqli_fetch_array($query)) {
            $hasil[] = $r;
        }

        return $hasil;
    }

    // ========================================================
    // FUNGSI UNTUK MEMBUAT TABEL DI PDF
    // ========================================================
    function set_table($data)
    {
        // Header kolom tabel
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(7, 7, "No", 1);
        $this->Cell(32, 7, "No Invoice", 1);
        $this->Cell(20, 7, "Kasir", 1);
        $this->Cell(25, 7, "Nama Pembeli", 1);
        $this->Cell(32, 7, "Tanggal Transaksi", 1);
        $this->Cell(24, 7, "Modal", 1);
        $this->Cell(24, 7, "Total Bayar", 1);
        $this->Cell(25, 7, "Keuntungan", 1);
        $this->Ln();

        // Isi tabel
        $this->SetFont('Arial', '', 9);
        $no = 1;
        $sum_modal = 0;
        $sum_total = 0;
        $sum_keuntungan = 0;

        foreach ($data as $row) {
            $this->Cell(7, 7, $no++, 1);
            $this->Cell(32, 7, $row['no_invoice'], 1);
            $this->Cell(20, 7, $row['username'], 1);
            $this->Cell(25, 7, $row['nama_pembeli'], 1);
            $this->Cell(32, 7, date("d-m-Y H:i:s", strtotime($row['tgl_transaksi'])), 1);
            $this->Cell(24, 7, "Rp. " . number_format($row['modal']), 1);
            $this->Cell(24, 7, "Rp. " . number_format($row['total_bayar']), 1);
            $this->Cell(25, 7, "Rp. " . number_format($row['total_bayar'] - $row['modal']), 1);
            $this->Ln();

            // Total perhitungan keseluruhan
            $sum_modal += $row['modal'];
            $sum_total += $row['total_bayar'];
            $sum_keuntungan += ($row['total_bayar'] - $row['modal']);
        }

        // Baris total di akhir tabel
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(7, 7);
        $this->Cell(32, 7);
        $this->Cell(20, 7);
        $this->Cell(25, 7);
        $this->Cell(32, 7, "Total", 1);
        $this->Cell(24, 7, "Rp. " . number_format($sum_modal), 1);
        $this->Cell(24, 7, "Rp. " . number_format($sum_total), 1);
        $this->Cell(25, 7, "Rp. " . number_format($sum_keuntungan), 1);
        $this->Ln(25);
    }
}

// ========================================================
// EKSEKUSI CETAK LAPORAN PDF
// ========================================================
$pdf = new PDF();
$pdf->SetTitle('Laporan Penjualan');

// Ambil data dari fungsi data_barang()
$data = $pdf->data_barang();

// Tambah halaman baru
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(1);

// Tampilkan tabel di PDF
$pdf->set_table($data);

// Cek dan buat folder penyimpanan jika belum ada
$outputDir = 'resnlight/Barang/';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Simpan file PDF ke folder dengan nama tanggal
$pdf->Output('', $outputDir . date("d-m-Y") . '.pdf');
