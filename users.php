<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Kullanıcı silme işlemi
if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

// Kullanıcı ekleme işlemi
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Kullanıcı adı ve şifre gereklidir.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Bu kullanıcı adı zaten kullanılıyor.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())");
            
            if ($stmt->execute([$username, $hashedPassword])) {
                $_SESSION['success'] = "Kullanıcı başarıyla eklendi.";
                header("Location: users.php");
                exit;
            } else {
                $_SESSION['error'] = "Bir hata oluştu.";
            }
        }
    }
}

// Tüm kullanıcıları getir
$stmt = $conn->query("SELECT id, username, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcılar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <div class="container mx-auto px-6 py-8">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="flex justify-between items-center">
                <h3 class="text-gray-700 text-3xl font-medium">Kullanıcılar</h3>
                <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                    <i class="fas fa-plus mr-2"></i>Yeni Kullanıcı
                </button>
            </div>

            <!-- Kullanıcılar Tablosu -->
            <div class="mt-8">
                <div class="flex flex-col mt-6">
                    <div class="overflow-x-auto">
                        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Kullanıcı Adı</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Kayıt Tarihi</th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900"><?php echo $user['id']; ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <button onclick="editUser(<?php echo $user['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="" method="POST" class="inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="delete_user" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Ekleme Modalı -->
    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Yeni Kullanıcı Ekle</h3>
                <form class="mt-4" action="" method="POST">
                    <div class="mt-2 px-7 py-3">
                        <input type="text" name="username" placeholder="Kullanıcı Adı" 
                               class="px-3 py-2 border border-gray-300 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="mt-2 px-7 py-3">
                        <input type="password" name="password" placeholder="Şifre" 
                               class="px-3 py-2 border border-gray-300 rounded-md w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="submit" name="add_user"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            Kullanıcı Ekle
                        </button>
                    </div>
                </form>
            </div>
            <button onclick="document.getElementById('addUserModal').classList.add('hidden')" 
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        function editUser(userId) {
            alert('Düzenleme özelliği yakında eklenecek! User ID: ' + userId);
        }
    </script>
</body>
</html>
