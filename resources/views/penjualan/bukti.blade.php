<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Setoran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            font-size: 11px;
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
            margin: 8px 0;
        }
        .subtitle { 
            font-size: 14px; 
            margin: 5px 0; 
            color: #333;
            font-weight: bold;
        }
        
        .info-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 5px;
            font-size: 11px;
        }
        .info-box .label {
            font-weight: bold;
            width: 150px;
        }
        
        table.detail { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            font-size: 10px;
        }
        table.detail th, 
        table.detail td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: left;
        }
        table.detail th { 
            background-color: #2e8b57;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table.detail td.text-center {
            text-align: center;
        }
        table.detail td.text-right {
            text-align: right;
        }
        table.detail tfoot {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .kategori-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 8px;
        }
        
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        
        .footer { 
            margin-top: 40px; 
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center; 
            font-size: 9px; 
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #6c757d;
            color: white;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-primary {
            background-color: #0d6efd;
        }
        
        .badge-success {
            background-color: #198754;
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
    <div class="subtitle">BUKTI SETORAN SAMPAH</div>
</div>

<!-- Info Transaksi -->
<div class="info-box">
    <table>
        <tr>
            <td class="label">Tanggal Transaksi</td>
            <td>: {{ $penjualan->tanggal_transaksi->format('d F Y') }}</td>
            <td class="label">Tipe Pembayaran</td>
            <td>: 
                @if($penjualan->tipe_pembayaran == 'tabungan')
                    <span class="badge badge-primary">TABUNGAN</span>
                @else
                    <span class="badge badge-success">TUNAI</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Nama Nasabah</td>
            <td>: <strong>{{ $penjualan->nasabah->nama_nasabah ?? '-' }}</strong></td>
            <td class="label">No Induk</td>
            <td>: {{ $penjualan->nasabah->no_induk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td colspan="3">: {{ $penjualan->nasabah->alamat ?? '-' }}</td>
        </tr>
    </table>
</div>

<!-- Detail Sampah -->
<h3 style="margin: 20px 0 10px; font-size: 13px;">DETAIL SAMPAH</h3>

<table class="detail">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="25%">Kategori</th>
            <th width="30%">Sub-Jenis & Produk</th>
            <th width="12%">Berat/Jumlah</th>
            <th width="8%">Satuan</th>
            <th width="12%">Harga</th>
            <th width="13%">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @php
            $groupedDetails = $penjualan->details->groupBy(function($detail) {
                return $detail->jenisSampah->kategori->id ?? 0;
            });
            $no = 1;
        @endphp

        @foreach($groupedDetails as $kategoriId => $details)
            @php
                $kategori = $details->first()->jenisSampah->kategori ?? null;
            @endphp
            
            @if($kategori)
                <tr>
                    <td colspan="7" class="kategori-header">
                        {{ strtoupper($kategori->nama) }}
                    </td>
                </tr>
            @endif

            @foreach($details as $detail)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $kategori->nama ?? '-' }}</td>
                    <td>
                        <strong>{{ $detail->jenisSampah->nama ?? '-' }}</strong>
                        @if($detail->nama_produk)
                            <br><small style="color: #666;">({{ $detail->nama_produk }})</small>
                        @endif
                        <br><small style="color: #999;">Kode: {{ $detail->jenisSampah->kode ?? '-' }}</small>
                    </td>
                    <td class="text-center">
                        {{ number_format($detail->berat_kg, 2) }}
                    </td>
                    <td class="text-center">
                        <strong>{{ strtoupper($detail->satuan) }}</strong>
                    </td>
                    <td class="text-right">
                        {{ number_format($detail->harga_per_kg, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right">TOTAL</td>
            <td colspan="2" class="text-center">
                @php
                    $beratPerSatuan = $penjualan->details->groupBy('satuan')->map(function($items) {
                        return $items->sum('berat_kg');
                    });
                @endphp
                @foreach($beratPerSatuan as $satuan => $totalBerat)
                    {{ number_format($totalBerat, 2) }} {{ strtoupper($satuan) }}
                    @if(!$loop->last)<br>@endif
                @endforeach
            </td>
            <td></td>
            <td class="text-right">Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

<!-- Total Section -->
<div class="total-section">
    <table style="width: 100%;">
        <tr>
            <td style="font-size: 14px; font-weight: bold;">TOTAL BERAT:</td>
            <td style="text-align: right; font-size: 14px; font-weight: bold;">
                @foreach($beratPerSatuan as $satuan => $totalBerat)
                    {{ number_format($totalBerat, 2) }} {{ strtoupper($satuan) }}
                    @if(!$loop->last) + @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="font-size: 16px; font-weight: bold; color: #2e8b57;">TOTAL NILAI:</td>
            <td style="text-align: right; font-size: 16px; font-weight: bold; color: #2e8b57;">
                Rp {{ number_format($penjualan->total_jual, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</div>

<!-- Tanda Tangan -->
<table style="width: 100%; margin-top: 40px;">
    <tr>
        <td style="width: 50%; text-align: center;">
            <p>Nasabah,</p>
            <br><br><br>
            <p style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </p>
            <br>
            <small>{{ $penjualan->nasabah->nama_nasabah ?? '' }}</small>
        </td>
        <td style="width: 50%; text-align: center;">
            <p>Petugas,</p>
            <br><br><br>
            <p style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </p>
            <br>
            <small>(............................)</small>
        </td>
    </tr>
</table>

<div class="footer">
    <p>Bukti ini dicetak secara otomatis oleh sistem Bank Sampah Unit Lamber</p>
    <p>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WITA</p>
</div>

</body>
</html>