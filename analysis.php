<?php
// Örnek statik veriler
$userCount = 150;
$recentLogins = [
    ['username' => 'ahmet123', 'last_login' => '2024-01-15 14:30:00'],
    ['username' => 'mehmet456', 'last_login' => '2024-01-15 13:45:00'],
    ['username' => 'ayse789', 'last_login' => '2024-01-15 12:20:00'],
    ['username' => 'fatma321', 'last_login' => '2024-01-15 11:15:00'],
    ['username' => 'ali654', 'last_login' => '2024-01-15 10:30:00']
];

$weeklyRegistrations = [
    ['date' => '2024-01-09', 'count' => 5],
    ['date' => '2024-01-10', 'count' => 8],
    ['date' => '2024-01-11', 'count' => 12],
    ['date' => '2024-01-12', 'count' => 7],
    ['date' => '2024-01-13', 'count' => 15],
    ['date' => '2024-01-14', 'count' => 10],
    ['date' => '2024-01-15', 'count' => 6]
];
?>

<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Sistem Analizi</h1>
    
    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Toplam Kullanıcı</p>
                    <p class="text-2xl font-semibold text-gray-900"><?php echo $userCount; ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sistem Durumu</p>
                    <p class="text-2xl font-semibold text-gray-900">Aktif</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Sunucu Saati</p>
                    <p class="text-2xl font-semibold text-gray-900" id="serverTime"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik ve Tablo Bölümü -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Haftalık Kayıt Grafiği -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Haftalık Kayıtlar</h2>
            <canvas id="registrationChart" height="300"></canvas>
        </div>

        <!-- Son Giriş Yapanlar Tablosu -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Son Giriş Yapanlar</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Son Giriş</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentLogins as $login): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($login['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d.m.Y H:i', strtotime($login['last_login'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function updateServerTime() {
    const now = new Date();
    document.getElementById('serverTime').textContent = now.toLocaleTimeString('tr-TR');
}
setInterval(updateServerTime, 1000);
updateServerTime();

const ctx = document.getElementById('registrationChart').getContext('2d');
const registrationData = <?php echo json_encode($weeklyRegistrations, JSON_NUMERIC_CHECK); ?>;

const formattedData = registrationData.map(item => ({
    date: new Date(item.date).toLocaleDateString('tr-TR'),
    count: parseInt(item.count)
}));

new Chart(ctx, {
    type: 'line',
    data: {
        labels: formattedData.map(item => item.date),
        datasets: [{
            label: 'Yeni Kayıtlar',
            data: formattedData.map(item => item.count),
            borderColor: 'rgb(59, 130, 246)',
            tension: 0.1,
            fill: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0
                }
            }
        }
    }
});
</script> 