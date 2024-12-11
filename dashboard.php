<?php
$pageTitle = 'Dashboard - Admin Panel';
$currentPage = 'dashboard';

ob_start();
?>

<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>
    <!-- Dashboard içeriği buraya -->
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
