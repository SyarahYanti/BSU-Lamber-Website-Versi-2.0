@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Title & Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #1f5121;">Dashboard</h2>
        
        <!-- Filter Bulan & Tahun -->
        <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2">
            <select name="bulan" class="form-select form-select-sm" style="width: 150px;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            
            <select name="tahun" class="form-select form-select-sm" style="width: 120px;">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm" title="Reset ke bulan ini">
                <i class="fas fa-redo"></i>
            </a>
        </form>
    </div>

    <!-- Row 1: Cards Statistik (4 Kolom) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Jumlah Nasabah Aktif -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Nasabah Aktif</h6>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users fa-lg text-success"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-0" style="font-size: 2.5rem; color: #1f5121;">
                        {{ $nasabahAktifSekarang }}
                    </h2>
                    <small class="text-muted">Total aktif</small>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pemasukan -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Total Pemasukan</h6>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-money-bill-wave fa-lg text-primary"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 1.5rem; color: #0d6efd;">
                        Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
                    </h2>
                    <small class="text-muted">{{ $bulanTerpilih }}</small>
                </div>
            </div>
        </div>

        <!-- Card 3: Sampah Terbanyak (KG) -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Top Sampah (KG)</h6>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-weight-hanging fa-lg text-warning"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-size: 1.3rem; color: #ffc107;">
                        {{ $jenisSampahTerbanyakKG }}
                    </h5>
                    <small class="text-muted d-block mb-1">{{ $totalBeratTerbanyakKG }} kg</small>
                    <small class="text-muted">{{ $bulanTerpilih }}</small>
                </div>
            </div>
        </div>

        <!-- Card 4: Sampah Terbanyak (PCS) -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Top Sampah (PCS)</h6>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-boxes fa-lg text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2" style="font-size: 1.3rem; color: #17a2b8;">
                        {{ $jenisSampahTerbanyakPCS }}
                    </h5>
                    <small class="text-muted d-block mb-1">{{ $totalBeratTerbanyakPCS }} pcs</small>
                    <small class="text-muted">{{ $bulanTerpilih }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Tabel & Charts -->
    <div class="row g-3">
        <!-- Tabel Penjualan Terakhir -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-list text-primary"></i> Setoran Terakhir
                        </h5>
                        <span class="badge bg-primary">{{ $bulanTerpilih }}</span>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Sampah</th>
                                    <th style="white-space: nowrap;">Berat</th>
                                    <th style="min-width: 110px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penjualanTerakhir as $p)
                                <tr>
                                    <td>
                                        <strong>{{ $p->nasabah->nama_nasabah }}</strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        {{ $p->tanggal_transaksi->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @foreach($p->details->take(2) as $detail)
                                            <span class="badge bg-secondary mb-1">
                                                {{ $detail->jenisSampah->nama }}
                                            </span>
                                        @endforeach
                                        @if($p->details->count() > 2)
                                            <span class="badge bg-light text-dark">+{{ $p->details->count() - 2 }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap;">
                                        @php
                                            $beratPerSatuan = $p->details->groupBy('satuan')->map(function($items) {
                                                return $items->sum('berat_kg');
                                            });
                                        @endphp
                                        <strong>
                                            @foreach($beratPerSatuan as $satuan => $totalBerat)
                                                {{ number_format($totalBerat, 1) }} {{ strtolower($satuan) }}@if(!$loop->last) + @endif
                                            @endforeach
                                        </strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <strong class="text-success">
                                            Rp {{ number_format($p->total_jual, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Belum ada transaksi di bulan {{ $bulanTerpilih }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column untuk Charts -->
        <div class="col-lg-6">
            <!-- Chart Jenis Sampah (KG) -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-weight-hanging text-success"></i> Top Sampah (KG)
                        </h5>
                        <span class="badge bg-success">{{ $bulanTerpilih }}</span>
                    </div>
                    @if($chartJenisSampahKG->count() > 0)
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartJenisSampahKG"></canvas>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada data KG</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart Jenis Sampah (PCS) -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-boxes text-info"></i> Top Sampah (PCS)
                        </h5>
                        <span class="badge bg-info">{{ $bulanTerpilih }}</span>
                    </div>
                    @if($chartJenisSampahPCS->count() > 0)
                        <div style="position: relative; height: 200px;">
                            <canvas id="chartJenisSampahPCS"></canvas>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada data PCS</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart Statistik Setoran -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-line text-danger"></i> Statistik Setoran
                        </h5>
                        <span class="badge bg-danger">Tahun {{ $tahunTerpilih }}</span>
                    </div>
                    <div style="position: relative; height: 200px;">
                        <canvas id="chartStatistikPenjualan"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Data dari Backend
    const chartKGData = @json($chartJenisSampahKG);
    const chartPCSData = @json($chartJenisSampahPCS);
    const statistikData = @json($statistikPenjualan);
    const tahunTerpilih = @json($tahunTerpilih);

    // Chart 1: Top Sampah (KG)
    @if($chartJenisSampahKG->count() > 0)
    const ctxKG = document.getElementById('chartJenisSampahKG').getContext('2d');
    new Chart(ctxKG, {
        type: 'bar',
        data: {
            labels: chartKGData.map(item => item.nama),
            datasets: [{
                label: 'Berat (kg)',
                data: chartKGData.map(item => parseFloat(item.total_berat)),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => context.parsed.x.toFixed(2) + ' kg'
                    }
                }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                y: { 
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        callback: function(value, index) {
                            const label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substring(0, 17) + '...' : label;
                        }
                    }
                }
            }
        }
    });
    @endif

    // Chart 2: Top Sampah (PCS)
    @if($chartJenisSampahPCS->count() > 0)
    const ctxPCS = document.getElementById('chartJenisSampahPCS').getContext('2d');
    new Chart(ctxPCS, {
        type: 'bar',
        data: {
            labels: chartPCSData.map(item => item.nama),
            datasets: [{
                label: 'Jumlah (pcs)',
                data: chartPCSData.map(item => parseFloat(item.total_berat)),
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => context.parsed.x.toFixed(0) + ' pcs'
                    }
                }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                y: { 
                    grid: { display: false },
                    ticks: {
                        font: { size: 11 },
                        callback: function(value, index) {
                            const label = this.getLabelForValue(value);
                            return label.length > 20 ? label.substring(0, 17) + '...' : label;
                        }
                    }
                }
            }
        }
    });
    @endif

    // Chart 3: Statistik Penjualan (Line Chart)
    const ctxStatistik = document.getElementById('chartStatistikPenjualan').getContext('2d');
    new Chart(ctxStatistik, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: tahunTerpilih + ' Report',
                data: statistikData,
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: 'rgb(239, 68, 68)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 15, font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => context.dataset.label + ': ' + context.parsed.y + ' transaksi'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        stepSize: 1,
                        callback: (value) => Math.floor(value)
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<style>
    .card {
        border-radius: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }
    .table thead th {
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
        background-color: #f8f9fa;
    }
    .table tbody tr {
        transition: background-color 0.2s;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.7em;
        font-size: 0.75rem;
    }
    .form-select-sm {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 0.875rem;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection