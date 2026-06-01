<?php
/**
 * shopcard_service.php
 * Logic nghiệp vụ: giỏ hàng, đặt hàng, lịch sử đơn hàng.
 */

require_once __DIR__ . '/shopcard_dao.php';
require_once __DIR__ . '/shopcard_model.php';

class ShopcardService {

    private ShopcardDAO $dao;

    public function __construct(ShopcardDAO $dao) {
        $this->dao = $dao;
        $this->dao->ensureTables();
    }

    // ── Thêm sản phẩm vào giỏ ────────────────────────────────────────────────
    public function addToCart(int $productId): bool {
        $p = $this->dao->getProductById($productId);
        if (!$p) return false;

        $stock = (int)($p['so_luong'] ?? 0);
        if ($stock <= 0) return false;

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = [
                'id'    => $p['id'],
                'name'  => $p['name'],
                'price' => $p['price'],
                'img'   => $p['img'],
                'qty'   => 1,
                'stock' => $stock,
            ];
        } elseif ((int)$_SESSION['cart'][$productId]['qty'] < $stock) {
            $_SESSION['cart'][$productId]['qty']++;
        } else {
            return false;
        }
        return true;
    }

    // ── Xóa sản phẩm ─────────────────────────────────────────────────────────
    public function removeFromCart(int $productId): void {
        unset($_SESSION['cart'][$productId]);
    }

    // ── Cập nhật số lượng ────────────────────────────────────────────────────
    public function updateQty(array $qtyMap): void {
        foreach ($qtyMap as $id => $qty) {
            $id  = (int)$id;
            $qty = max(1, (int)$qty);
            if (isset($_SESSION['cart'][$id])) {
                $p = $this->dao->getProductById($id);
                if (!$p || (int)($p['so_luong'] ?? 0) <= 0) {
                    unset($_SESSION['cart'][$id]);
                    continue;
                }
                $qty = min($qty, (int)$p['so_luong']);
                $_SESSION['cart'][$id]['qty'] = $qty;
                $_SESSION['cart'][$id]['stock'] = (int)$p['so_luong'];
            }
        }
    }

    // ── Lấy giỏ hàng ─────────────────────────────────────────────────────────
    public function getCart(): array {
        if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return [];
        }

        foreach ($_SESSION['cart'] as $id => $item) {
            $id = (int)$id;
            $p = $this->dao->getProductById($id);

            if (!$p || (int)($p['so_luong'] ?? 0) <= 0) {
                unset($_SESSION['cart'][$id]);
                continue;
            }

            $stock = (int)$p['so_luong'];
            $qty = min(max(1, (int)($item['qty'] ?? 1)), $stock);

            $_SESSION['cart'][$id] = [
                'id'    => (int)$p['id'],
                'name'  => $p['name'],
                'price' => (float)$p['price'],
                'img'   => $p['img'],
                'qty'   => $qty,
                'stock' => $stock,
            ];
        }

        return $_SESSION['cart'];
    }

    // ── Tính tổng ────────────────────────────────────────────────────────────
    public function calcTotal(array $cart): float {
        return array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
    }

    // ── Đặt hàng ─────────────────────────────────────────────────────────────
    /**
     * @return array ['success'=>bool, 'error'=>string, 'order_data'=>array|null]
     */
    public function placeOrder(array $post, ?int $userId = null): array {
        $ho_ten      = trim($post['ho_ten']      ?? '');
        $sdt         = trim($post['sdt']         ?? '');
        $dia_chi_tt  = trim($post['dia_chi_tt']  ?? '');
        $phuong_thuc = trim($post['phuong_thuc'] ?? '');

        if (!$ho_ten || !$sdt || !$dia_chi_tt || !$phuong_thuc) {
            return ['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin bắt buộc!', 'order_data' => null];
        }

        $cart = $this->getCart();
        if (empty($cart)) {
            return ['success' => false, 'error' => 'Giỏ hàng của bạn đang trống!', 'order_data' => null];
        }

        try {
            $postedQty = is_array($post['item_qty'] ?? null) ? $post['item_qty'] : [];
            foreach ($postedQty as $pid => $qty) {
                $pid = (int)$pid;
                if (isset($cart[$pid])) {
                    $cart[$pid]['qty'] = max(1, (int)$qty);
                }
            }

            $this->dao->beginTransaction();

            $tong       = 0;
            $cart_items = [];
            foreach ($cart as $pid => $item) {
                $row = $this->dao->getProductPriceById((int)$pid, true);
                if (!$row) {
                    throw new RuntimeException('San pham trong gio hang khong con ton tai.');
                }

                $qty   = max(1, (int)$item['qty']);
                $stock = (int)($row['so_luong'] ?? 0);
                if ($stock < $qty) {
                    throw new RuntimeException('San pham "' . $row['ten_san_pham'] . '" chi con ' . $stock . ' san pham.');
                }

                $sub  = (float)$row['gia'] * $qty;
                $tong += $sub;
                $cart_items[] = [
                    'id'  => (int)$row['id'],
                    'ten' => $row['ten_san_pham'],
                    'gia' => (float)$row['gia'],
                    'qty' => $qty,
                    'tt'  => $sub,
                ];
            }

            if (empty($cart_items)) {
                throw new RuntimeException('Gio hang khong co san pham hop le.');
            }
            $order    = new Order($ho_ten, $sdt, $dia_chi_tt, $phuong_thuc, $tong);
            // Truyền userId để gắn đơn với tài khoản
            $order_id = $this->dao->insertOrder($order, $userId);

            foreach ($cart_items as $item) {
                $detail = new OrderDetail($order_id, $item['id'], $item['ten'], $item['gia'], $item['qty']);
                $this->dao->insertOrderDetail($detail);
                if (!$this->dao->decreaseProductStock($item['id'], $item['qty'])) {
                    throw new RuntimeException('Khong the tru ton kho cho san pham "' . $item['ten'] . '".');
                }
            }

            $this->dao->commit();

            $order_data = [
                'id'          => $order_id,
                'ho_ten'      => $ho_ten,
                'sdt'         => $sdt,
                'dia_chi_tt'  => $dia_chi_tt,
                'phuong_thuc' => $phuong_thuc,
                'tong'        => $tong,
                'items'       => $cart_items,
            ];
            $_SESSION['last_order'] = $order_data;
            $_SESSION['cart']       = [];

            return ['success' => true, 'error' => '', 'order_data' => $order_data];

        } catch (Throwable $e) {
            $this->dao->rollBack();
            return [
                'success'    => false,
                'error'      => 'Lỗi hệ thống, vui lòng thử lại! (' . $e->getMessage() . ')',
                'order_data' => null,
            ];
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // LỊCH SỬ ĐƠN HÀNG
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Lấy toàn bộ lịch sử đơn hàng của user,
     * kèm chi tiết sản phẩm từng đơn.
     *
     * @return OrderHistory[]
     */
    public function updateOrderInfo(array $post, int $userId): array {
        $orderId = (int)($post['order_id'] ?? 0);
        $hoTen   = trim($post['edit_ho_ten'] ?? '');
        $sdt     = trim($post['edit_sdt'] ?? '');
        $diaChi  = trim($post['edit_dia_chi'] ?? '');

        if ($orderId <= 0 || !$hoTen || !$sdt || !$diaChi) {
            return ['success' => false, 'message' => 'Vui long dien day du thong tin don hang.'];
        }

        $order = $this->dao->getOrderByIdForUser($orderId, $userId);
        if (!$order) {
            return ['success' => false, 'message' => 'Khong tim thay don hang cua ban.'];
        }

        if (empty($order['can_edit_info'])) {
            return ['success' => false, 'message' => 'Chi co the sua thong tin don hang trong vong 24 gio sau khi dat va don chua bi huy.'];
        }

        $updated = $this->dao->updateOrderInfoWithin24Hours($orderId, $userId, $hoTen, $sdt, $diaChi);
        if (!$updated) {
            return ['success' => false, 'message' => 'Khong the cap nhat don hang. Vui long tai lai trang va thu lai.'];
        }

        return ['success' => true, 'message' => 'Da cap nhat thong tin don hang #' . $orderId . '.'];
    }

    public function getOrderHistory(int $userId): array {
        $rows = $this->dao->getOrdersByUserId($userId);
        $orders = [];
        foreach ($rows as $row) {
            $order = OrderHistory::fromArray($row);
            // Lấy chi tiết từng sản phẩm trong đơn
            $itemRows = $this->dao->getOrderItemsByOrderId($order->id);
            foreach ($itemRows as $itemRow) {
                $order->items[] = OrderHistoryItem::fromArray($itemRow);
            }
            $orders[] = $order;
        }
        return $orders;
    }
}
