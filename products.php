<?php
// Oturum kontrolü

// Ürün kategorileri
$categories = [
    'elektronik' => 'Elektronik',
    'giyim' => 'Giyim',
    'kitap' => 'Kitap',
    'ev' => 'Ev & Yaşam',
    'spor' => 'Spor & Outdoor',
    'kozmetik' => 'Kozmetik',
    'oyuncak' => 'Oyuncak & Hobi'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once 'config.php';
        
        $errors = [];
        
        // Form verilerini al
        $productName = trim($_POST['product_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // Veri doğrulama
        if (empty($productName) || strlen($productName) < 3) {
            $errors[] = "Ürün adı en az 3 karakter olmalıdır.";
        }
        if ($price <= 0) {
            $errors[] = "Geçerli bir fiyat giriniz.";
        }
        if ($stock < 0) {
            $errors[] = "Geçerli bir stok miktarı giriniz.";
        }

        // Görsel yükleme
        $imagePath = '';
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['product_image']['type'], $allowedTypes)) {
                $errors[] = "Sadece JPG, PNG ve WEBP formatları kabul edilir.";
            } elseif ($_FILES['product_image']['size'] > $maxSize) {
                $errors[] = "Dosya boyutu 5MB'dan küçük olmalıdır.";
            } else {
                $uploadDir = 'uploads/products/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = uniqid('product_') . '.' . pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                } else {
                    $errors[] = "Dosya yüklenirken bir hata oluştu.";
                }
            }
        }

        if (empty($errors)) {
            $sql = "INSERT INTO products (name, category, price, stock, description, image_path, created_at) 
                    VALUES (:name, :category, :price, :stock, :description, :image_path, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'name' => $productName,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'description' => $description,
                'image_path' => $imagePath
            ]);

            if ($result) {
                $_SESSION['success_message'] = "Ürün başarıyla eklendi.";
                header('Location: products.php');
                exit;
            } else {
                $errors[] = "Ürün eklenirken bir hata oluştu.";
            }
        }

        if (!empty($errors)) {
            $errorMessage = implode('<br>', $errors);
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>
                    <strong class='font-bold'>Hata!</strong><br>
                    <span class='block'>$errorMessage</span>
                  </div>";
        }
    } catch(PDOException $e) {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>
                <strong class='font-bold'>Sistem Hatası!</strong>
                <span class='block'>Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.</span>
              </div>";
        error_log($e->getMessage());
    }
}
?>

<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Yeni Ürün Ekle</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ürün Adı</label>
                <input type="text" name="product_name" required minlength="3" maxlength="100"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="category" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fiyat (TL)</label>
                <input type="number" name="price" step="0.01" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Stok Miktarı</label>
                <input type="number" name="stock" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ürün Görseli</label>
                <div class="mt-1 flex items-center">
                    <div class="preview-image hidden w-32 h-32 mb-4">
                        <img id="imagePreview" class="w-full h-full object-cover rounded">
                    </div>
                </div>
                <input type="file" name="product_image" accept="image/*" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                       file:rounded-full file:border-0 file:text-sm file:font-semibold
                       file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                       onchange="previewImage(this)">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ürün Açıklaması</label>
                <textarea name="description" rows="4" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ürün Ekle
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewContainer = document.querySelector('.preview-image');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>