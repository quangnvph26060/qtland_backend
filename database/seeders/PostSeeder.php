<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Post::create([
            'title' => 'Chung cư cao cấp, view sông, Landmark 81, đã có sổ, chỉ từ 850tr dọn vào ở ngay, full NT 0948 162 ***',
            'description' => 'Chỉ cần thanh toán đủ 30% trong 3 đợt.
Vào ở ngay.
Tặng full nội thất cao cấp.
Không lấy nội thất được ck 4,5% và được tặng ngay 3 chỉ vàng 9999.
Vị trí: Mặt tiền QL13, Thuận An - Bình Dương(cách bệnh viện Hạnh Phúc 5p).
Cam kết thuê lại.
8 triệu.
10 triệu.
3PN: 12 triệu.
Tất cả đều bao PQL 1 năm.
Full tiện ích miễn phí: Hồ bơi, gym, yoga, bbq, sport indoor, kidsroom.
Giáp Thủ Đức, nên rất thuận tiện di chuyển vào Thủ Đức, Bình Thạnh, Gò Vấp, Q1,...
A/C cần quan tâm đầu tư hay mua ở.',
            'address' => 'Phường An Phú, Quận 2, Thành phố Hồ Chí Minh',
            'address_detail' => 'Vinhomes Central Park, 208 Nguyễn Hữu Cảnh',
            'area' => '71',
            'price' => '1900000000',
            'unit' => 1,
            'status_id' => 3,
            'user_id' => 1,
        ]);


        Post::create([
            'title' => 'Quỹ căn độc quyền giá tốt nhà phố, biệt thự Vinhomes Grand Park T3/2024. LH 0903 040 *** Mr Đức',
            'description' => 'Giỏ hàng nhà phố biệt thự tốt nhất thị trường Tháng 2/2024, cập nhật liên tục, thông tin rõ ràng, chính xác, uy tín với đội ngũ nhân viên kinh doanh đã giao dịch hơn 60 căn nhà phố, biệt thự tại dự án.
Giỏ hàng giá ngộp bank, bán lỗ so với giá gốc, cơ hội mua giá CK tới 25% so với thời kì cao điểm.

Liên hệ 0903 040 *** hoặc (zalo) để được hỗ trợ. Minh An Homes luôn có các sản phẩm giá tốt hơn so với thị trường đang giao dịch.
Nhà phố liền kề.
- Loại 84m² Manhattan bán giá 13.5 Gần Vincom, công viên.
- Loại 126m² Manhattan bán giá 17.5 tỷ (bán lỗ 2 tỷ), gần vincom, công viên.
- Loại 84m² Glory bán giá: 12 tỷ.
- Loại 96m² Glory bán giá: 13.9 tỷ (bán lỗ). Gần D2a.
- Loại 126m² Glory 15.9 tỷ bao phí (vị trí đẹp).
- Loại 144m² Glory bán giá 17.5 tỷ. Bán lỗ 5.1 tỷ so với giá gốc (Siêu rẻ).
- Loại 180m² Glory Giá gốc 26 tỷ, bán 22.5 tỷ bao phí (lỗ 4.5 tỷ).',
            'address' => 'Phường Quảng An, Quận Tây Hồ, Thành phố Hà Nội',
            'address_detail' => 'Vinhomes West Point, 206 Lạc Long Quân',
            'area' => '96',
            'price' => '139000000000',
            'unit' => 1,
            'status_id' => 4,
            'sold_status' => true,
            'user_id' => 1,
        ]);

        Post::create([
            'title' => 'Chính sách giảm 3 tỷ mới nhất- Tổng hợp quỹ hàng giá tốt nhất biệt thự Nam Cường - có căn góc 3 mặt',
            'description' => 'Trực tiếp ban quản lý kinh doanh cđt Nam Cường.
- Mở bán biệt thự Nam Cường khu A - B.
- Giá từ 23 tỷ.
- Hỗ trợ ls 0% 24 tháng.
- Được chọn căn đẹp.
- Thông tin đón sóng đường Lê Quang Đạo kéo dài.
Tư vấn chuyên sâu: 0906 228 ***quản lý dự án).',
            'address' => 'Phường Đông Xuân, Quận Hoàn Kiếm, Thành phố Hà Nội',
            'address_detail' => 'Nam Cường, 1 Lê Quang Đạo',
            'area' => '180',
            'price' => '23000000000',
            'unit' => 1,
            'status_id' => 5,
            'user_id' => 2,
        ]);

        Post::create([
            'title' => 'Giỏ hàng chuyển nhượng căn hộ Vinhomes Ba Son 1PN, 2PN, 3PN, 4PN những căn giá tốt tháng T03/2024',
            'description' => 'Cơ hội cuối cùng để quý khách hàng sở hữu căn hộ đúng giá trị thực.

* Siêu hot giá tốt nhất dự án.
- 1PN DT 50m² OT full NT, chốt nhanh 4,7 tỷ.
- 1PN DT 50m² SHLD full NT, đã có Sổ hồng, chốt nhanh 6 tỷ.
- 2PN DT 68m² OT NTCB view L81, chốt nhanh 7 tỷ.
- 2PN DT 74m² OT full NT, chốt nhanh 7.1 tỷ.
- 2PN DT 78m² OT NTCB, chốt nhanh 7.4 tỷ.
- 2PN DT 72m² OT full NT, view L81 giá 8 tỷ.
- 2PN 1WC DT 61m² SHLD full NT, còn HĐMB, giá 7.3 tỷ.
- 2PN DT 68m² SHLD full NT, đã có Sổ hồng, chốt 7,7 tỷ.
- 2PN DT 78.5m² SHLD, full NT view L81 đã có sổ giá 9.2 tỷ.',
            'address' => 'Phường Bình Trưng Tây, Quận 2, Thành phố Hồ Chí Minh',
            'address_detail' => 'Vinhomes Ba Son, 2 Tôn Đức Thắng',
            'area' => '99',
            'price' => '14000000000',
            'unit' => 1,
            'status_id' => 4,
            'sold_status' => true,
            'user_id' => 2,
        ]);
    }
}
