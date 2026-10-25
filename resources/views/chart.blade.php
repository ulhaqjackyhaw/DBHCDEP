<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard & Analitik Karyawan</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide-react@0.292.0/dist/umd/lucide.min.js"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* slate-50 */
        }
        .tab-active {
            border-bottom-color: #3b82f6; /* blue-500 */
            color: #3b82f6;
        }
        .tab-inactive {
            border-bottom-color: transparent;
            color: #64748b; /* slate-500 */
        }
    </style>
</head>
<body class="p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Dashboard Kepegawaian</h1>
            <p class="text-slate-500 mt-1">Analisis dan kelola data karyawan Anda di satu tempat.</p>
        </header>

        <!-- Pesan Sukses (Contoh) -->
        <!-- <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
            <p class="font-bold">Sukses</p>
            <p>File Excel berhasil diunggah dan data telah diperbarui.</p>
        </div> -->

        <!-- Panel Upload -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg mb-6">
             <h2 class="text-lg font-semibold text-slate-700 mb-4">Upload Data Baru</h2>
             <!-- Form Upload (Ganti action ke URL backend Anda) -->
             <form action="#" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4">
                <input type="file" name="file" class="flex-grow w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors" required>
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-full shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75 transition-transform transform hover:scale-105">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                    Upload Excel
                </button>
            </form>
        </div>

        <!-- Konten Utama dengan Tabs -->
        <div>
            <!-- Navigasi Tabs -->
            <div class="border-b border-slate-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button id="tab-table" class="tab-active group inline-flex items-center py-4 px-1 border-b-2 font-semibold text-sm transition-colors" aria-current="page">
                        <i data-lucide="table-2" class="mr-2 w-5 h-5"></i>
                        Tabel Data Karyawan
                    </button>
                    <button id="tab-chart" class="tab-inactive group inline-flex items-center py-4 px-1 border-b-2 font-semibold text-sm hover:border-slate-300 hover:text-slate-700 transition-colors">
                        <i data-lucide="pie-chart" class="mr-2 w-5 h-5"></i>
                        Analitik & Grafik
                    </button>
                </nav>
            </div>

            <!-- Konten Tab -->
            <div class="mt-6">
                <!-- Konten Tabel -->
                <div id="content-table" class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Gender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jabatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kelompok Jabatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Grade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="employee-table-body" class="bg-white divide-y divide-slate-200">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Konten Grafik -->
                <div id="content-chart" class="hidden bg-white p-4 sm:p-6 rounded-2xl shadow-lg">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Distribusi Karyawan Organik Berdasarkan Kelompok Jabatan</h3>
                    <div class="relative h-96 w-full">
                        <canvas id="jobGroupChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- DATA CONTOH (Ganti dengan data dari backend Anda) ---
        const employees = [
            { NIK: '112233', Nama: 'Budi Santoso', GENDER: 'Laki-laki', UNIT_KERJA: 'Teknologi Informasi', KELOMPOK_KELAS_JABATAN: 'BOD-1', GRADE: '15', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '112244', Nama: 'Ani Yudhoyono', GENDER: 'Perempuan', UNIT_KERJA: 'Sumber Daya Manusia', KELOMPOK_KELAS_JABATAN: 'BOD-2', GRADE: '14', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '112255', Nama: 'Citra Kirana', GENDER: 'Perempuan', UNIT_KERJA: 'Pemasaran', KELOMPOK_KELAS_JABATAN: 'BOD-2', GRADE: '14', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '112266', Nama: 'Doni Salmanan', GENDER: 'Laki-laki', UNIT_KERJA: 'Keuangan', KELOMPOK_KELAS_JABATAN: 'BOD-3', GRADE: '12', STATUS_KEPEGAWAIAN: 'Non-Organik' },
            { NIK: '112277', Nama: 'Eka Wijaya', GENDER: 'Laki-laki', UNIT_KERJA: 'Operasional', KELOMPOK_KELAS_JABATAN: 'BOD-3', GRADE: '13', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '112288', Nama: 'Fira Basuki', GENDER: 'Perempuan', UNIT_KERJA: 'Teknologi Informasi', KELOMPOK_KELAS_JABATAN: 'BOD-4', GRADE: '10', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '112299', Nama: 'Gita Gutawa', GENDER: 'Perempuan', UNIT_KERJA: 'Sumber Daya Manusia', KELOMPOK_KELAS_JABATAN: 'BOD-2', GRADE: '15', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '113300', Nama: 'Hari Purnomo', GENDER: 'Laki-laki', UNIT_KERJA: 'Pemasaran', KELOMPOK_KELAS_JABATAN: 'BOD-1', GRADE: '16', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '113311', Nama: 'Indra Bekti', GENDER: 'Laki-laki', UNIT_KERJA: 'Keuangan', KELOMPOK_KELAS_JABATAN: 'BOD-3', GRADE: '12', STATUS_KEPEGAWAIAN: 'Organik' },
            { NIK: '113322', Nama: 'Joko Widodo', GENDER: 'Laki-laki', UNIT_KERJA: 'Operasional', KELOMPOK_KELAS_JABATAN: 'BOD-1', GRADE: '17', STATUS_KEPEGAWAIAN: 'Non-Organik' },
        ];

        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi ikon Lucide
            lucide.createIcons();

            // --- FUNGSI TAB ---
            const tabTable = document.getElementById('tab-table');
            const tabChart = document.getElementById('tab-chart');
            const contentTable = document.getElementById('content-table');
            const contentChart = document.getElementById('content-chart');

            function switchTab(activeTab) {
                if (activeTab === 'table') {
                    tabTable.classList.remove('tab-inactive');
                    tabTable.classList.add('tab-active');
                    tabChart.classList.remove('tab-active');
                    tabChart.classList.add('tab-inactive');
                    
                    contentTable.classList.remove('hidden');
                    contentChart.classList.add('hidden');
                } else {
                    tabChart.classList.remove('tab-inactive');
                    tabChart.classList.add('tab-active');
                    tabTable.classList.remove('tab-active');
                    tabTable.classList.add('tab-inactive');

                    contentChart.classList.remove('hidden');
                    contentTable.classList.add('hidden');
                }
            }

            tabTable.addEventListener('click', () => switchTab('table'));
            tabChart.addEventListener('click', () => switchTab('chart'));

            // --- POPULASI TABEL DATA ---
            const tableBody = document.getElementById('employee-table-body');
            if (employees.length > 0) {
                employees.forEach((emp, index) => {
                    const row = `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">${index + 1}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.NIK}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.Nama}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.GENDER}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.JABATAN}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.KELOMPOK_KELAS_JABATAN}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${emp.GRADE}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${emp.STATUS_KEPEGAWAIAN === 'Organik' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                    ${emp.STATUS_KEPEGAWAIAN}
                                </span>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-slate-500">Tidak ada data untuk ditampilkan.</td></tr>`;
            }

            // --- PERSIAPAN DATA & RENDER GRAFIK ---
            const organicEmployees = employees.filter(emp => emp.STATUS_KEPEGAWAIAN === 'Organik');
            const jobGroups = ['BOD-1', 'BOD-2', 'BOD-3', 'BOD-4'];
            const jobGroupCounts = jobGroups.map(group => 
                organicEmployees.filter(emp => emp.KELOMPOK_KELAS_JABATAN === group).length
            );

            const chartData = {
                labels: jobGroups,
                datasets: [{
                    label: 'Jumlah Karyawan Organik',
                    data: jobGroupCounts,
                    backgroundColor: [
                        '#3b82f6', // blue-500
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                        '#ef4444', // red-500
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            };

            const ctx = document.getElementById('jobGroupChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 25,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 14,
                                    family: "'Inter', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return ` ${label}: ${value} orang (${percentage})`;
                                }
                            },
                            padding: 12,
                            boxPadding: 4,
                            titleFont: { size: 16 },
                            bodyFont: { size: 14 }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
