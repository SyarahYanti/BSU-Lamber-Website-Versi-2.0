<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Setoran Sampah {{ $judulPeriode }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            font-size: 10px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2e8b57;
        }
        .logo-img { 
            width: 60px; 
            height: auto; 
            margin-bottom: 8px;
        }
        .title { 
            font-size: 20px; 
            font-weight: bold; 
            color: #2e8b57; 
            margin: 8px 0 5px;
        }
        .subtitle { 
            font-size: 16px; 
            margin: 5px 0; 
            color: #333;
            font-weight: bold;
        }
        .periode {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .statistik {
            margin: 20px 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .statistik table {
            width: 100%;
        }
        .statistik td {
            padding: 5px;
            font-size: 11px;
        }
        .statistik .label {
            font-weight: bold;
            width: 200px;
        }
        
        table.data { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            font-size: 9px;
        }
        table.data th, 
        table.data td { 
            border: 1px solid #333; 
            padding: 5px; 
            text-align: left;
        }
        table.data th { 
            background-color: #2e8b57;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table.data td.text-center {
            text-align: center;
        }
        table.data td.text-right {
            text-align: right;
        }
        table.data tfoot {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .rekap-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .rekap-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px;
            color: #2e8b57;
            border-bottom: 2px solid #2e8b57;
            padding-bottom: 5px;
        }
        
        table.rekap {
            width: 70%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        table.rekap th,
        table.rekap td {
            border: 1px solid #333;
            padding: 8px;
        }
        table.rekap th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        table.rekap td.text-right {
            text-align: right;
        }
        table.rekap td.text-center {
            text-align: center;
        }
        
        .footer { 
            margin-top: 40px; 
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center; 
            font-size: 8px; 
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        
        .detail-item {
            font-size: 8px;
            line-height: 1.3;
            margin: 2px 0;
        }
    </style>
</head>
<body>

<div class="header">
    @php
        $logoPath = public_path('images/logo-BSU.png');
    @endphp
    
    @if(file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="Logo Bank Sampah Lamber" class="logo-img">
    @endif
    
    <div class="title">BANK SAMPAH UNIT LAMBER</div>
    <div class="subtitle">REKAPITULASI SETORAN SAMPAH</div>
    <div class="periode">{{ $judulPeriode }}</div>
</div>

<!-- Statistik -->
<div class="statistik">
    <table>
        <tr>
            <td class="label">Total Transaksi</td>
            <td>: {{ $jumlahTransaksi }} transaksi</td>
            <td class="label">Total Setoran</td>
            <td>: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Berat</td>
            <td>: 
                @php
                    $allDetails = collect();
                    foreach($penjualans as $p) {
                        $allDetails = $allDetails->merge($p->details);
                    }
                    $totalBeratPerSatuan = $allDetails->groupBy('satuan')->map(function($items) {
                        return $items->sum('berat_kg');
                    });
                @endphp
                @foreach($totalBeratPerSatuan as $satuan => $berat)
                    {{ number_format($berat, 2) }} {{ strtolower($satuan) }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td class="label">Tanggal Cetak</td>
            <td>: {{ now()->format('d/m/Y H:i') }} WITA</td>
        </tr>
    </table>
</div>

<!-- Tabel Data Penjualan -->
<h3 style="margin: 20px 0 10px; font-size: 12px;">DETAIL TRANSAKSI SETORAN</h3>

@if($penjualans->count() > 0)
    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="6%">ID</th>
                <th width="10%">Tanggal</th>
                <th width="15%">Nasabah</th>
                <th width="35%">Detail Sampah</th>
                <th width="12%">Berat</th>
                <th width="10%">Total (Rp)</th>
                <th width="8%">Tipe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualans as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">#{{ $p->id }}</td>
                <td class="text-center">{{ $p->tanggal_transaksi->format('d/m/Y') }}</td>
                <td>{{ $p->nasabah->nama_nasabah ?? '-' }}</td>
                <td>
                    @php
                        $groupedDetails = $p->details->groupBy(function($d) {
                            return $d->jenisSampah->kategori->nama ?? 'Lainnya';
                        });
                    @endphp
                    
                    @foreach($groupedDetails as $kategori => $details)
                        <div style="margin-bottom: 3px;">
                            <strong>{{ $kategori }}:</strong><br>
                            @foreach($details as $detail)
                                <div class="detail-item">
                                    • {{ $detail->jenisSampah->nama ?? '-' }}
                                    @if($detail->nama_produk)
                                        ({{ $detail->nama_produk }})
                                    @endif
                                    : {{ number_format($detail->berat_kg, 2) }} {{ $detail->satuan }}
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </td>
                <td class="text-right">
                    @php
                        $beratPerSatuan = $p->details->groupBy('satuan')->map(function($items) {
                            return $items->sum('berat_kg');
                        });
                    @endphp
                    @foreach($beratPerSatuan as $satuan => $berat)
                        {{ number_format($berat, 2) }} {{ strtolower($satuan) }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
                <td class="text-right">{{ number_format($p->total_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ ucfirst($p->tipe_pembayaran) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">
                    @foreach($totalBeratPerSatuan as $satuan => $berat)
                        {{ number_format($berat, 2) }} {{ strtolower($satuan) }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Rekap Per Kategori & Sub-Jenis -->
    <div class="rekap-section">
        <div class="rekap-title">REKAP PER KATEGORI & SUB-JENIS SAMPAH</div>
        
        <table class="rekap">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="18%">Kategori</th>
                    <th width="30%">Sub-Jenis</th>
                    <th width="10%">Satuan</th>
                    <th width="17%">Total Berat</th>
                    <th width="20%">Total Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $no = 1;
                    $rekapKategori = [];
                    
                    // Group data by kategori, sub-jenis, dan satuan
                    foreach($penjualans as $p) {
                        foreach($p->details as $detail) {
                            $kategoriNama = $detail->jenisSampah->kategori->nama ?? 'Lainnya';
                            $subJenisNama = $detail->jenisSampah->nama ?? 'Tidak diketahui';
                            $satuan = $detail->satuan;
                            
                            $key = $kategoriNama . '|' . $subJenisNama . '|' . $satuan;
                            
                            if (!isset($rekapKategori[$key])) {
                                $rekapKategori[$key] = [
                                    'kategori' => $kategoriNama,
                                    'sub_jenis' => $subJenisNama,
                                    'satuan' => $satuan,
                                    'berat' => 0,
                                    'total' => 0,
                                ];
                            }
                            
                            $rekapKategori[$key]['berat'] += $detail->berat_kg;
                            $rekapKategori[$key]['total'] += $detail->subtotal;
                        }
                    }
                    
                    // Sort by kategori
                    ksort($rekapKategori);
                @endphp
                
                @foreach($rekapKategori as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['sub_jenis'] }}</td>
                    <td class="text-center">{{ strtoupper($item['satuan']) }}</td>
                    <td class="text-right">{{ number_format($item['berat'], 2) }}</td>
                    <td class="text-right">{{ number_format($item['total'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right">
                        @foreach($totalBeratPerSatuan as $satuan => $berat)
                            {{ number_format($berat, 2) }} {{ strtolower($satuan) }}
                            @if(!$loop->last)<br>@endif
                        @endforeach
                    </td>
                    <td class="text-right">{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="no-data">
        <p>Tidak ada data penjualan untuk periode {{ $judulPeriode }}</p>
    </div>
@endif

<div class="footer">
    <p>Laporan ini dicetak secara otomatis oleh sistem Bank Sampah Unit Lamber</p>
    <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WITA</p>
</div>

</body>
</html>