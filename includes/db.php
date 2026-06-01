<?php
/**
 * db.php - Kết nối Database (PDO - an toàn nhất)
 * Dùng cho toàn bộ website
 */

$host     = 'localhost';
$dbname   = 'petshop_db';        
$username = 'root';
$password = '123456';                  // XAMPP mặc định là rỗng

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Cài đặt để hiển thị lỗi rõ ràng khi debug
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // ==================== FIX QUAN TRỌNG ====================
    // Ngăn PDO tự động thêm dấu nháy vào LIMIT và OFFSET
    // Đây là nguyên nhân gây lỗi "near ''10' OFFSET '0''"
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // =======================================================

    // echo "Kết nối database thành công! ";

} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>