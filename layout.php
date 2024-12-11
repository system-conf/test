<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo $extraStyles ?? ''; ?>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="container mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">ADMIN PANEL</span>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="relative">
                        <input type="search" placeholder="Ara..." class="bg-gray-100 text-gray-700 rounded-lg py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition-all duration-300">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                    <span class="font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 px-4 transition duration-300 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>Çıkış
                    </a>
                </div>
            </div>
        </header>

        <div class="flex flex-1 h-[calc(100vh-4rem)]">
            <!-- Sidebar -->
            <aside class="bg-white border-r border-gray-200 w-64 flex-shrink-0 hidden md:block overflow-y-auto">
                <nav class="sticky top-0 mt-8 px-4 space-y-2">
                    <a href="javascript:void(0)" data-page="analysis" class="nav-link group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $currentPage === 'analysis' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?> mb-2">
                        <i class="fas fa-home mr-3"></i>
                        Analiz
                    </a>
                    <a href="javascript:void(0)" data-page="users" class="nav-link group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $currentPage === 'users' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?> mb-2">
                        <i class="fas fa-users mr-3"></i>
                        Kullanıcılar
                    </a>
                    <a href="javascript:void(0)" data-page="snake" class="nav-link group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $currentPage === 'snake' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?> mb-2">
                        <i class="fas fa-gamepad mr-3"></i>
                        Snake Oyunu
                    </a>
                    <a href="javascript:void(0)" data-page="products" class="nav-link group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $currentPage === 'products' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?> mb-2">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Ürünler
                    </a>
                    <a href="javascript:void(0)" data-page="settings" class="nav-link group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo $currentPage === 'settings' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'; ?> mb-2">
                        <i class="fas fa-cog mr-3"></i>
                        Ayarlar
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 relative">
                <?php echo $content ?? ''; ?>
            </main>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Varsayılan sayfa olarak 'analysis' sayfasını yükle
        loadContent('analysis');

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                loadContent(page);
                
                // Aktif link'i güncelle
                document.querySelectorAll('.nav-link').forEach(el => {
                    el.classList.remove('text-blue-600', 'bg-blue-50');
                    el.classList.add('text-gray-600', 'hover:bg-gray-50');
                });
                this.classList.add('text-blue-600', 'bg-blue-50');
                this.classList.remove('text-gray-600', 'hover:bg-gray-50');
            });
        });
    });

    function loadContent(page) {
        fetch(`${page}.php`)
            .then(response => response.text())
            .then(html => {
                document.querySelector('main').innerHTML = html;
            })
            .catch(error => {
                console.error('İçerik yüklenirken hata oluştu:', error);
            });
    }
    </script>
    <?php echo $extraScripts ?? ''; ?>
</body>
</html> 