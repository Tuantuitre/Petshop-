<?php

require_once __DIR__ . '/Services_model.php';

class ServiceService {

    /**
     * Trả về danh sách dịch vụ dưới dạng mảng Service object.
     * Dữ liệu tĩnh — không cần DB.
     *
     * @return Service[]
     */
    public function getAllServices(): array {
        $raw = [
            [
                'id'    => 1,
                'name'  => 'Tắm & Sấy',
                'desc'  => 'Tắm sạch, sấy khô, khử mùi chuyên sâu bằng sản phẩm cao cấp an toàn cho da và lông thú cưng.',
                'price' => 150000,
                'icon'  => 'fa-shower',
            ],
            [
                'id'    => 2,
                'name'  => 'Cắt Tỉa Lông',
                'desc'  => 'Tạo kiểu lông theo yêu cầu, cắt móng, vệ sinh tai và mắt bởi thợ có tay nghề cao.',
                'price' => 200000,
                'icon'  => 'fa-cut',
            ],
            [
                'id'    => 3,
                'name'  => 'Khám Sức Khỏe',
                'desc'  => 'Kiểm tra tổng quát sức khỏe, tư vấn dinh dưỡng và lịch tiêm phòng phù hợp cho thú cưng.',
                'price' => 250000,
                'icon'  => 'fa-stethoscope',
            ],
            [
                'id'    => 4,
                'name'  => 'Tiêm Phòng',
                'desc'  => 'Tiêm vắc-xin đầy đủ theo phác đồ chuẩn, bảo vệ thú cưng khỏi các bệnh nguy hiểm.',
                'price' => 300000,
                'icon'  => 'fa-syringe',
            ],
            [
                'id'    => 5,
                'name'  => 'Gửi Thú Cưng',
                'desc'  => 'Dịch vụ trông giữ thú cưng theo ngày / tuần trong không gian sạch sẽ, an toàn, vui chơi đầy đủ.',
                'price' => 180000,
                'icon'  => 'fa-home',
            ],
            [
                'id'    => 6,
                'name'  => 'Huấn Luyện',
                'desc'  => 'Huấn luyện thú cưng nghe lệnh cơ bản, sửa hành vi xấu và phát triển kỹ năng xã hội.',
                'price' => 400000,
                'icon'  => 'fa-graduation-cap',
            ],
        ];

        return array_map(fn($s) => new Service(
            $s['id'],
            $s['name'],
            $s['desc'],
            $s['price'],
            $s['icon']
        ), $raw);
    }
}