<x-layouts.app>
    <!-- START: Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0B130F] -tracking-tight">Dashboard</h1>
            <p class="text-xs sm:text-sm text-[#6C7E75] mt-0.5">An easy way to manage campus assets with care and precision.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center gap-2.5 bg-white border border-[#E9EFEF] px-4 py-2 rounded-full text-xs font-semibold text-[#0B130F] shadow-[0_2px_8px_rgba(11,19,15,0.02)] cursor-default">
                <x-heroicon-o-calendar-days class="w-4 h-4 text-slate-400" />
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </button>
        </div>
    </div>
    <!-- END: Page Header -->

    <!-- START: Main Layout Grid (2 Columns: Primary + Performance Pane) -->
    <div class="space-y-6">

        <!-- TOP AREA: 3 Bento Stat Cards (Spark Admin Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Alert Dark Navy Banner Card -->
            <div class="alert-spark-card">
                <!-- Inline SVG geometric decoration (Amber 6-pointed star with rounded caps) -->
                <svg class="alert-spark-bg-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(50,50)">
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(60)" />
                        <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(120)" />
                    </g>
                </svg>

                <div class="relative z-10">
                    <span class="alert-spark-badge">Informasi Aset</span>
                    <div class="alert-spark-date">{{ now()->format('d M Y') }} • ESL IPB</div>
                    <div class="alert-spark-text">Total estimasi nilai perolehan aset Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</div>
                </div>

                <div class="relative z-10">
                    <a href="{{ route('laporan.index') }}" class="alert-spark-link">
                        <span>Lihat Rekapitulasi</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </a>
                </div>
            </div>

            <!-- Card 2: Total Ruangan (Sparkline Net Income Style) -->
            <div class="card-spark flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="stat-label-spark">Total Ruangan</span>
                        <div class="text-slate-400">
                            <x-heroicon-o-ellipsis-horizontal class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="stat-value-spark">{{ $totalRuangan }} <span class="text-base font-bold text-[#6C7E75]">Ruang</span></div>
                    <div class="trend-badge-spark text-emerald-600">
                        <x-heroicon-o-arrow-up-right class="w-3.5 h-3.5" />
                        <span>100% Aktif & Terdata di DIR</span>
                    </div>
                </div>
                <div class="-mx-4 -mb-3 mt-2">
                    <div id="income-sparkline"></div>
                </div>
            </div>

            <!-- Card 3: Total Barang (Sparkline Total Return Style) -->
            <div class="card-spark flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="stat-label-spark">Total Barang Terdaftar</span>
                        <div class="text-slate-400">
                            <x-heroicon-o-ellipsis-horizontal class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="stat-value-spark">{{ number_format($totalBarang) }} <span class="text-base font-bold text-[#6C7E75]">Unit</span></div>
                    <div class="trend-badge-spark text-[#b45309]">
                        <x-heroicon-o-arrow-up-right class="w-3.5 h-3.5" />
                        <span>Inventaris Departemen & Ruangan</span>
                    </div>
                </div>
                <div class="-mx-4 -mb-3 mt-2">
                    <div id="return-sparkline"></div>
                </div>
            </div>
        </div>
        <!-- END: TOP AREA -->

        <!-- MAIN SECTION: 2 Columns Layout (~68% Left, ~32% Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT AREA: Revenue Bar Chart + Details (Transaction & Progress) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Primary Bar Chart: Distribusi Aset per Gedung (Revenue Chart Style) -->
                <div class="card-spark">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <h2 class="text-base font-bold text-[#0B130F]">Distribusi Aset per Gedung</h2>
                        <!-- Custom Static Legends -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-[#6C7E75]">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#0A0E2E]"></span>
                                <span>Total Barang</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-[#6C7E75]">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ffa916]"></span>
                                <span>Kondisi Baik</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-2xl font-extrabold text-[#0B130F] tracking-tight">{{ number_format($totalBarang) }} Unit</span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                            <x-heroicon-o-arrow-up-right class="w-3 h-3" />
                            100% Tersebar di Fasilitas
                        </span>
                    </div>
                    <div id="revenue-chart" class="w-full"></div>
                </div>

                <!-- Sub-grid: Transaction List + Product Overview Progress -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                    <!-- Column: Transaction List (Barang Ditambahkan Terbaru) -->
                    <div class="md:col-span-7 card-spark flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-[#E9EFEF]">
                                <h2 class="text-sm font-bold text-[#0B130F]">Barang Ditambahkan Terbaru</h2>
                                <a href="{{ route('barang.index') }}" class="text-xs font-bold text-[#120F8B] hover:text-[#ffa916] transition-colors">
                                    Lihat Semua
                                </a>
                            </div>

                            <div class="space-y-1">
                                @forelse ($barangTerbaru as $b)
                                    <div class="transaction-item-spark">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-[#0A0E2E] text-[#ffa916] flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                                {{ strtoupper(substr($b->nama_barang, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-[#0B130F] truncate">{{ $b->nama_barang }}</div>
                                                <div class="text-[11px] text-[#6C7E75] truncate">
                                                    {{ $b->created_at ? $b->created_at->format('d M Y') : 'Terdata' }} • {{ $b->ruangan?->nama_ruang ?? 'Gudang' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 ml-2">
                                            <div class="text-xs font-extrabold text-[#0B130F]">{{ $b->jumlah }} {{ $b->satuan }}</div>
                                            <div class="text-[10px] font-bold {{ $b->kondisi_baik > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                                {{ $b->kondisi_baik > 0 ? $b->kondisi_baik . ' Baik' : 'Butuh Cek' }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-xs text-[#6C7E75]">Belum ada data barang terbaru.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Column: Product Overview (Ringkasan Kelayakan Sarpras) -->
                    <div class="md:col-span-5 card-spark flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-[#E9EFEF]">
                                <h2 class="text-sm font-bold text-[#0B130F]">Status Kelayakan</h2>
                                <span class="text-[11px] font-bold text-[#6C7E75]">{{ $totalBarang }} Unit</span>
                            </div>

                            <div class="space-y-4">
                                <!-- Progress 1: Kondisi Baik -->
                                <div>
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <span class="text-[#0B130F]">Kondisi Baik</span>
                                        <span class="text-emerald-600">{{ $jumlahBaik }} ({{ $persenBaik }}%)</span>
                                    </div>
                                    <div class="spark-progress-track">
                                        <div class="spark-progress-fill bg-emerald-500" style="width: {{ $persenBaik }}%;"></div>
                                    </div>
                                </div>

                                <!-- Progress 2: Rusak Ringan -->
                                <div>
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <span class="text-[#0B130F]">Rusak Ringan (RR)</span>
                                        <span class="text-[#b45309]">{{ $jumlahRusakRingan }} ({{ $persenRR }}%)</span>
                                    </div>
                                    <div class="spark-progress-track">
                                        <div class="spark-progress-fill bg-[#ffa916]" style="width: {{ $persenRR }}%;"></div>
                                    </div>
                                </div>

                                <!-- Progress 3: Rusak Berat -->
                                <div>
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <span class="text-[#0B130F]">Rusak Berat (RB)</span>
                                        <span class="text-rose-600">{{ $jumlahRusakBerat }} ({{ $persenRB }}%)</span>
                                    </div>
                                    <div class="spark-progress-track">
                                        <div class="spark-progress-fill bg-rose-500" style="width: {{ $persenRB }}%;"></div>
                                    </div>
                                </div>

                                <!-- Progress 4: Ruangan Terdata -->
                                <div>
                                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                        <span class="text-[#0B130F]">Kapasitas Ruang DIR</span>
                                        <span class="text-[#0A0E2E]">{{ $totalRuangan }} Ruang</span>
                                    </div>
                                    <div class="spark-progress-track">
                                        <div class="spark-progress-fill bg-[#0A0E2E]" style="width: 100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- END: LEFT AREA -->

            <!-- RIGHT AREA: Total View Performance Donut + Promo CTA Card -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Donut Chart: Total View Performance Style -->
                <div class="card-spark flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-base font-bold text-[#0B130F]">Kondisi Fisik Barang</h2>
                            <div class="text-slate-400">
                                <x-heroicon-o-ellipsis-horizontal class="w-5 h-5" />
                            </div>
                        </div>

                        <!-- Donut Chart Canvas Container -->
                        <div id="views-chart" class="my-2 flex items-center justify-center"></div>

                        <!-- Custom Static Legends below the chart (Spark Admin Style) -->
                        <div class="grid grid-cols-1 gap-2 pt-4 border-t border-[#E9EFEF] text-xs font-semibold">
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-[#6C7E75]">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    Kondisi Baik
                                </span>
                                <span class="font-bold text-[#0B130F]">{{ number_format($jumlahBaik) }} ({{ $persenBaik }}%)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-[#6C7E75]">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#ffa916]"></span>
                                    Rusak Ringan
                                </span>
                                <span class="font-bold text-[#0B130F]">{{ number_format($jumlahRusakRingan) }} ({{ $persenRR }}%)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-[#6C7E75]">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                    Rusak Berat
                                </span>
                                <span class="font-bold text-[#0B130F]">{{ number_format($jumlahRusakBerat) }} ({{ $persenRB }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promo Banner CTA Card (Spark Admin Style) -->
                <div class="promo-banner-card">
                    <!-- Inline SVG geometric decoration (Amber 6-pointed star with rounded caps) -->
                    <svg class="promo-banner-bg-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g transform="translate(50,50)">
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(60)" />
                            <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#ffa916" transform="rotate(120)" />
                        </g>
                    </svg>

                    <h3 class="promo-title">Perlu perbaikan atau pengadaan fasilitas baru?</h3>
                    <p class="promo-desc">Ajukan sarpras dan fasilitas ruangan Anda dengan alur verifikasi resmi dan mudah dipantau.</p>
                    @if (in_array(auth()->user()->role, ['admin', 'dosen_tendik']))
                        <a href="{{ route('pengajuan.index') }}" class="btn-promo w-full">
                            Ajukan Sarpras Sekarang
                        </a>
                    @else
                        <a href="{{ route('barang.index') }}" class="btn-promo w-full">
                            Eksplor Data Barang
                        </a>
                    @endif
                </div>

            </div>
            <!-- END: RIGHT AREA -->

        </div>
        <!-- END: MAIN SECTION -->

    </div>

    <!-- START: ApexCharts Controller Scripts (Spark Admin Core Specs) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts library not loaded!');
                return;
            }

            // 1. Revenue Chart (Vertical Dual-Bar Chart: Total Barang vs Kondisi Baik)
            const revenueChartEl = document.querySelector('#revenue-chart');
            if (revenueChartEl) {
                const revenueChartOptions = {
                    series: [
                        {
                            name: 'Total Barang',
                            data: @json($dataGedung)
                        },
                        {
                            name: 'Kondisi Baik',
                            data: @json($dataGedungBaik)
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 230,
                        stacked: false,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#0A0E2E', '#ffa916'], // Deep Navy (Total), Amber (Baik)
                    states: {
                        hover: { filter: { type: 'none' } }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    legend: { show: false },
                    grid: {
                        borderColor: '#E9EFEF',
                        strokeDashArray: 4,
                        yaxis: { lines: { show: true } },
                        xaxis: { lines: { show: false } },
                        padding: { top: 0, right: 0, bottom: 0, left: 0 }
                    },
                    xaxis: {
                        categories: @json($labelGedung),
                        labels: {
                            style: {
                                colors: '#6C7E75',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: { show: false } // Sembunyikan angka absolut sumbu Y agar bersih seperti Spark
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " Unit";
                            }
                        },
                        theme: 'dark'
                    }
                };
                new ApexCharts(revenueChartEl, revenueChartOptions).render();
            }

            // 2. Views Chart (Total View Performance Donut Chart with Center Label)
            const viewsChartEl = document.querySelector('#views-chart');
            if (viewsChartEl) {
                const viewsChartOptions = {
                    series: [{{ $jumlahBaik }}, {{ $jumlahRusakRingan }}, {{ $jumlahRusakBerat }}],
                    chart: {
                        type: 'donut',
                        height: 250,
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    labels: ['Kondisi Baik', 'Rusak Ringan', 'Rusak Berat'],
                    colors: ['#10B981', '#ffa916', '#EF4444'], // Green Baik, Amber RR, Red RB
                    states: {
                        hover: { filter: { type: 'none' } }
                    },
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '74%',
                                background: 'transparent',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        color: '#6C7E75',
                                        offsetY: -8
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '26px',
                                        fontWeight: 800,
                                        color: '#0B130F',
                                        offsetY: 8,
                                        formatter: function (val) {
                                            return val;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total Aset',
                                        fontSize: '11px',
                                        fontWeight: 600,
                                        color: '#6C7E75',
                                        formatter: function () {
                                            return '{{ $totalBarang }}';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: { theme: 'dark' }
                };
                new ApexCharts(viewsChartEl, viewsChartOptions).render();
            }

            // 3. Income Sparkline (Area sparkline smooth wave - Green)
            const incomeSparkEl = document.querySelector('#income-sparkline');
            if (incomeSparkEl) {
                const incomeSparkOptions = {
                    series: [{
                        name: 'Ruangan',
                        data: [1, 2, 2, 3, 3, 4, 3, 4, {{ max(1, $totalRuangan) }}]
                    }],
                    chart: {
                        type: 'area',
                        height: 48,
                        sparkline: { enabled: true },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 2.5 },
                    fill: { opacity: 0.15, type: 'solid' },
                    colors: ['#10B981'],
                    tooltip: { enabled: false }
                };
                new ApexCharts(incomeSparkEl, incomeSparkOptions).render();
            }

            // 4. Return Sparkline (Area sparkline smooth wave - Amber #ffa916)
            const returnSparkEl = document.querySelector('#return-sparkline');
            if (returnSparkEl) {
                const returnSparkOptions = {
                    series: [{
                        name: 'Barang',
                        data: [10, 18, 15, 25, 22, 35, 30, 38, {{ max(1, $totalBarang) }}]
                    }],
                    chart: {
                        type: 'area',
                        height: 48,
                        sparkline: { enabled: true },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    stroke: { curve: 'smooth', width: 2.5 },
                    fill: { opacity: 0.15, type: 'solid' },
                    colors: ['#ffa916'],
                    tooltip: { enabled: false }
                };
                new ApexCharts(returnSparkEl, returnSparkOptions).render();
            }
        });
    </script>
    <!-- END: ApexCharts Controller Scripts -->
</x-layouts.app>