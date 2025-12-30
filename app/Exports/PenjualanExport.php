<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class PenjualanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $periode;
    protected $bulan;
    protected $tahun;
    protected $rowNumber = 0;

    public function __construct($periode, $bulan, $tahun)
    {
        $this->periode = $periode;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Penjualan::with(['nasabah', 'details.jenisSampah.kategori']);

        if ($this->periode == 'bulan') {
            $query->whereMonth('tanggal_transaksi', $this->bulan)
                  ->whereYear('tanggal_transaksi', $this->tahun);
        } else {
            $query->whereYear('tanggal_transaksi', $this->tahun);
        }

        return $query->orderBy('tanggal_transaksi', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'ID TRANSAKSI',
            'TANGGAL',
            'NAMA NASABAH',
            'NO INDUK',
            'KATEGORI & SUB-JENIS',
            'NAMA PRODUK',
            'BERAT',
            'SATUAN',
            'HARGA',
            'SUBTOTAL',
            'TOTAL BERAT',
            'TOTAL NILAI (Rp)',
            'TIPE PEMBAYARAN',
        ];
    }

    /**
     * @var Penjualan $penjualan
     */
    public function map($penjualan): array
    {
        $rows = [];
        $detailCount = $penjualan->details->count();

        foreach ($penjualan->details as $index => $detail) {
            $kategori = $detail->jenisSampah->kategori->nama ?? '-';
            $subJenis = $detail->jenisSampah->nama ?? '-';
            $kategoriSubJenis = $kategori . ' - ' . $subJenis;

            // Hitung total berat per satuan (hanya di row pertama)
            $totalBeratText = '';
            if ($index === 0) {
                $beratPerSatuan = $penjualan->details->groupBy('satuan')->map(function($items) {
                    return $items->sum('berat_kg');
                });
                
                $beratTexts = [];
                foreach ($beratPerSatuan as $satuan => $berat) {
                    $beratTexts[] = number_format($berat, 2) . ' ' . strtoupper($satuan);
                }
                $totalBeratText = implode(' + ', $beratTexts);
            }

            $row = [
                $index === 0 ? ++$this->rowNumber : '', // No (hanya di row pertama)
                $index === 0 ? $penjualan->id : '', // ID
                $index === 0 ? $penjualan->tanggal_transaksi->format('d/m/Y') : '', // Tanggal
                $index === 0 ? $penjualan->nasabah->nama_nasabah ?? '-' : '', // Nama
                $index === 0 ? $penjualan->nasabah->no_induk ?? '-' : '', // No Induk
                $kategoriSubJenis, // Kategori & Sub-Jenis (untuk setiap detail)
                $detail->nama_produk ?? '-', // Nama Produk
                number_format($detail->berat_kg, 2), // Berat
                strtoupper($detail->satuan), // Satuan
                'Rp ' . number_format($detail->harga_per_kg, 0, ',', '.'), // Harga
                'Rp ' . number_format($detail->subtotal, 0, ',', '.'), // Subtotal
                $totalBeratText, // Total Berat (per satuan)
                $index === 0 ? 'Rp ' . number_format($penjualan->total_jual, 0, ',', '.') : '', // Total Nilai
                $index === 0 ? ucfirst($penjualan->tipe_pembayaran) : '', // Tipe Pembayaran
            ];

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2e8b57']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if ($this->periode == 'bulan') {
            $namaBulan = Carbon::create($this->tahun, $this->bulan, 1)->translatedFormat('F');
            return 'Laporan ' . $namaBulan . ' ' . $this->tahun;
        }
        
        return 'Laporan Tahun ' . $this->tahun;
    }
}