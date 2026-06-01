<?php
include 'includes/db.php'; 

function getProducts($cat = 'all', $sort = 'default', $price_min = 0, $price_max = 9999999) {
    global $pdo;
    $sql = "SELECT id, ten_san_pham AS name, gia AS price, mo_ta AS `desc`,
                   danh_muc AS cat, hinh_anh AS img, so_luong
            FROM san_pham
            WHERE gia BETWEEN :pmin AND :pmax";
    $params = [':pmin' => $price_min, ':pmax' => $price_max];
    if ($cat !== 'all') {
        $sql .= " AND danh_muc = :cat";
        $params[':cat'] = $cat;
    }
    $sql .= match($sort) {
        'price_asc'  => " ORDER BY gia ASC",
        'price_desc' => " ORDER BY gia DESC",
        'name_az'    => " ORDER BY ten_san_pham ASC",
        'name_za'    => " ORDER BY ten_san_pham DESC",
        default      => " ORDER BY id ASC",
    };
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getServices() {
    return [
        ['id'=>1,'name'=>'Tắm gội cho thú cưng','price'=>100000,'icon'=>'fa-bath','desc'=>'Dịch vụ tắm gội chuyên nghiệp với sản phẩm cao cấp.'],
        ['id'=>2,'name'=>'Cắt tỉa móng tay móng chân','price'=>200000,'icon'=>'fa-cut','desc'=>'Cắt tỉa an toàn, giúp thú cưng thoải mái di chuyển.'],
        ['id'=>3,'name'=>'Cắt tóc, tạo kiểu lông','price'=>300000,'icon'=>'fa-scissors','desc'=>'Tạo kiểu lông đẹp, phù hợp với giống thú cưng.'],
        ['id'=>4,'name'=>'Massage cho thú cưng','price'=>800000,'icon'=>'fa-hand-holding-heart','desc'=>'Massage thư giãn, giảm stress và khỏe mạnh.'],
        ['id'=>5,'name'=>'Vệ sinh móng tay móng chân','price'=>150000,'icon'=>'fa-shower','desc'=>'Vệ sinh sạch sẽ, ngăn ngừa bệnh tật.'],
        ['id'=>6,'name'=>'Dịch vụ đặc biệt (Spa)','price'=>1000000,'icon'=>'fa-spa','desc'=>'Spa cao cấp toàn diện cho thú cưng yêu quý.'],
    ];
}

function sendMail($to, $subject, $message) {
    $headers  = "From: no-reply@petshop.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return mail($to, $subject, $message, $headers);
}

// ══════════════════════════════════════════════
// BLOG - LẤY TỪ DATABASE (không hardcode nữa)
// ══════════════════════════════════════════════

/**
 * Chuyển 1 row từ DB sang format dùng trong blog.php / blog-detail.php
 */
function _formatPost(array $row): array {
    return [
        'id'      => $row['id'],
        'title'   => $row['tieu_de'],
        'date'    => date('d/m/Y', strtotime($row['ngay_dang'])),
        // Đường dẫn ảnh: nếu có tiền tố assets/ rồi thì dùng nguyên, ngược lại thêm vào
        'img'     => !empty($row['hinh_anh_1'])
                     ? (str_starts_with($row['hinh_anh_1'], 'assets/') || str_starts_with($row['hinh_anh_1'], 'http')
                        ? $row['hinh_anh_1']
                        : 'assets/images/' . $row['hinh_anh_1'])
                     : '',
        'excerpt' => !empty($row['noi_dung_chinh'])
                     ? mb_substr(strip_tags($row['noi_dung_chinh']), 0, 160) . '...'
                     : '',
        // Truyền đủ các trường để blog-detail.php dùng
        'content'               => $row['noi_dung_chinh']      ?? '',
        'nguyen_nhan_pho_bien'  => $row['nguyen_nhan_pho_bien'] ?? '',
        'huong_dan'             => $row['huong_dan']            ?? '',
        'cach_cham'             => $row['cach_cham']            ?? '',
        'hinh_anh_2'            => !empty($row['hinh_anh_2'])
                                   ? (str_starts_with($row['hinh_anh_2'], 'assets/') ? $row['hinh_anh_2'] : 'assets/images/' . $row['hinh_anh_2'])
                                   : '',
        'hinh_anh_3'            => !empty($row['hinh_anh_3'])
                                   ? (str_starts_with($row['hinh_anh_3'], 'assets/') ? $row['hinh_anh_3'] : 'assets/images/' . $row['hinh_anh_3'])
                                   : '',
        'hinh_anh_4'            => !empty($row['hinh_anh_4'])
                                   ? (str_starts_with($row['hinh_anh_4'], 'assets/') ? $row['hinh_anh_4'] : 'assets/images/' . $row['hinh_anh_4'])
                                   : '',
    ];
}

/**
 * Lấy tất cả bài viết cho blog.php
 */
function getPetBlogPosts(): array {
    global $pdo;
    try {
        $rows = $pdo->query(
            "SELECT * FROM tin_tuc ORDER BY ngay_dang DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
        return array_map('_formatPost', $rows);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Lấy 1 bài viết theo ID cho blog-detail.php
 */
function getBlogPostById(int $id): ?array {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM tin_tuc WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? _formatPost($row) : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Lấy bài viết nổi bật cho sidebar (theo lượt đọc nếu có, không thì mới nhất)
 */
function getFeaturedPosts(int $limit = 4): array {
    global $pdo;
    try {
        // Thử lấy theo luot_doc nếu cột tồn tại
        $rows = $pdo->query(
            "SELECT * FROM tin_tuc ORDER BY luot_doc DESC, ngay_dang DESC LIMIT $limit"
        )->fetchAll(PDO::FETCH_ASSOC);
        return array_map('_formatPost', $rows);
    } catch (Exception $e) {
        // Fallback không có cột luot_doc
        try {
            $rows = $pdo->query(
                "SELECT * FROM tin_tuc ORDER BY ngay_dang DESC LIMIT $limit"
            )->fetchAll(PDO::FETCH_ASSOC);
            return array_map('_formatPost', $rows);
        } catch (Exception $e2) {
            return [];
        }
    }
}

/**
 * Top sản phẩm bán chạy
 */
function getTopProducts(int $limit = 4): array {
    global $pdo;
    $limit = (int)$limit;
    try {
        $stmt = $pdo->query(
            "SELECT id, ten_san_pham AS name, gia AS price, mo_ta AS `desc`,
                    danh_muc AS cat, hinh_anh AS img, luot_mua
             FROM san_pham ORDER BY luot_mua DESC LIMIT $limit"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $stmt = $pdo->query(
            "SELECT id, ten_san_pham AS name, gia AS price, mo_ta AS `desc`,
                    danh_muc AS cat, hinh_anh AS img
             FROM san_pham ORDER BY id DESC LIMIT $limit"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Top bài viết nhiều lượt đọc
 */
function getTopBlogPosts(int $limit = 3): array {
    global $pdo;
    $limit = (int)$limit;
    try {
        $stmt = $pdo->query(
            "SELECT id, tieu_de AS title, noi_dung_chinh AS excerpt,
                    hinh_anh_1 AS img, luot_doc,
                    DATE_FORMAT(ngay_dang, '%d/%m/%Y') AS date
             FROM tin_tuc ORDER BY luot_doc DESC LIMIT $limit"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Chuẩn hóa đường dẫn ảnh
        foreach ($rows as &$r) {
            if (!empty($r['img']) && !str_starts_with($r['img'], 'assets/') && !str_starts_with($r['img'], 'http')) {
                $r['img'] = 'assets/images/' . $r['img'];
            }
            if (!empty($r['excerpt'])) {
                $r['excerpt'] = mb_substr(strip_tags($r['excerpt']), 0, 120) . '...';
            }
        }
        return $rows;
    } catch (Exception $e) {
        return [];
    }
}