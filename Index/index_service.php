<?php

require_once __DIR__ . '/index_dao.php';
require_once __DIR__ . '/index_model.php';

class IndexService {
    private IndexDAO $dao;

    public function __construct(IndexDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Service trả JSON để controller decode và truyền cho UI.
     */
    public function getHomeDataJson(): string {
        $data = [
            'topProducts' => array_map(
                fn(HomeProduct $product) => $product->toArray(),
                $this->dao->getTopProducts(4)
            ),
            'featuredServices' => array_map(
                fn(HomeServiceItem $service) => $service->toArray(),
                array_slice($this->getServiceItems(), 0, 3)
            ),
            'topBlogPosts' => array_map(
                fn(HomeBlogPost $post) => $post->toArray(),
                $this->dao->getTopBlogPosts(3)
            ),
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    /**
     * Dữ liệu dịch vụ đang là dữ liệu cấu hình vì bảng dich_vu chưa có giá, mô tả và icon.
     *
     * @return HomeServiceItem[]
     */
    private function getServiceItems(): array {
        return [
            new HomeServiceItem(1, 'Tắm gội cho thú cưng', 'Dịch vụ tắm gội chuyên nghiệp với sản phẩm cao cấp.', 100000, 'fa-bath'),
            new HomeServiceItem(2, 'Cắt tỉa móng tay móng chân', 'Cắt tỉa an toàn, giúp thú cưng thoải mái di chuyển.', 200000, 'fa-cut'),
            new HomeServiceItem(3, 'Cắt tóc, tạo kiểu lông', 'Tạo kiểu lông đẹp, phù hợp với giống thú cưng.', 300000, 'fa-scissors'),
            new HomeServiceItem(4, 'Massage cho thú cưng', 'Massage thư giãn, giảm stress và khỏe mạnh.', 800000, 'fa-hand-holding-heart'),
            new HomeServiceItem(5, 'Vệ sinh móng tay móng chân', 'Vệ sinh sạch sẽ, ngăn ngừa bệnh tật.', 150000, 'fa-shower'),
            new HomeServiceItem(6, 'Dịch vụ đặc biệt (Spa)', 'Spa cao cấp toàn diện cho thú cưng yêu quý.', 1000000, 'fa-spa'),
        ];
    }
}
