<?php
/**
 * Kiến thức du lịch Việt Nam — dùng khi API nguồn mở chậm/thiếu dữ liệu.
 * Có thể mở rộng thêm địa danh theo thời gian.
 */
return [
    'hai duong' => [
        'name' => 'Hải Dương',
        'lat' => 20.9409,
        'lon' => 106.333,
        'vibe' => 'ẩm thực địa phương, phố phường yên, hồ sen, chùa cổ',
        'intro' => 'Hải Dương hợp chuyến ngắn 1–2 ngày: ăn đặc sản, dạo phố, chụp ảnh đời thường, không cần lịch trình quá dày.',
        'spots' => [
            'Hồ Bạch Đằng (công viên ven hồ, đi bộ buổi chiều)',
            'Chùa Côn Sơn – Kiếp Bạc (di tích, không khí thanh tịnh)',
            'Khu phố cổ / trung tâm thành phố (chụp phố, cafe nhỏ)',
            'Làng nghề hoặc vườn trái vùng ven (nếu đi xe máy)',
        ],
        'food' => [
            'Bún cá / chả cá Hải Dương',
            'Bánh đa cua (nếu ghé vùng lân cận)',
            'Quán cơm bình dân đông khách địa phương',
            'Trà đá vỉa hè + đồ ăn vặt buổi tối',
        ],
        'photo' => 'Sáng 6h–8h hoặc chiều 16h30–18h; hồ và phố ít người hơn buổi trưa.',
    ],
    'da nang' => [
        'name' => 'Đà Nẵng',
        'lat' => 16.0544,
        'lon' => 108.2022,
        'vibe' => 'biển, cầu, cafe view, ăn hải sản',
        'intro' => 'Đà Nẵng là điểm đến cân bằng: biển + núi Ngũ Hành Sơn + ẩm thực; 3N2D là lịch phổ biến.',
        'spots' => [
            'Bãi biển Mỹ Khê / Phạm Văn Đồng',
            'Cầu Rồng / Cầu Sông Hàn (tối cuối tuần có nhạc lửa nếu đúng lịch)',
            'Ngũ Hành Sơn (chùa, hang, view)',
            'Bán đảo Sơn Trà / Linh ứng (view toàn thành phố)',
            'Phố đi bộ / chợ đêm Helio',
        ],
        'food' => [
            'Mì Quảng',
            'Bánh tráng cuốn thịt heo',
            'Hải sản chợ Cồn / nhà hàng ven biển',
            'Bê thui Cầu Mống (nếu có thời gian ra ngoại ô)',
        ],
        'photo' => 'Bình minh biển; hoàng hôn cầu; tránh 11h–14h nắng gắt.',
    ],
    'da lat' => [
        'name' => 'Đà Lạt',
        'lat' => 11.9404,
        'lon' => 108.4583,
        'vibe' => 'lạnh nhẹ, thông, hồ, cafe, săn mây',
        'intro' => 'Đà Lạt phù hợp nghỉ dưỡng, chụp ảnh, cafe và đồ ăn ấm; nên mang áo khoác buổi tối.',
        'spots' => [
            'Hồ Xuân Hương',
            'Đồi chè Cầu Đất (săn mây sớm)',
            'Ga Đà Lạt / Nhà thờ Con Gà',
            'Thung lũng tình yêu / Datanla (tùy vibe)',
            'Chợ đêm Đà Lạt',
        ],
        'food' => [
            'Lẩu gà lá é',
            'Bánh căn / bánh mì xíu mại',
            'Atiso, sữa dâu, cafe view rừng thông',
            'Dâu tây / mứt (quà)',
        ],
        'photo' => '5h–7h săn mây; 17h–18h30 hồ và đồi.',
    ],
    'ha noi' => [
        'name' => 'Hà Nội',
        'lat' => 21.0285,
        'lon' => 105.8542,
        'vibe' => 'phố cổ, văn hóa, ẩm thực đường phố',
        'intro' => 'Hà Nội nên đi bộ khu Phố Cổ, ăn vặt, tham quan di tích; giao thông đông, ưu tiên Grab hoặc xe điện.',
        'spots' => [
            'Phố Cổ – Hồ Hoàn Kiếm',
            'Văn Miếu – Quốc Tử Giám',
            'Hồ Tây (hoàng hôn, cafe)',
            'Lăng Ba Đình / Quảng trường Ba Đình',
            'Phố đi bộ cuối tuần',
        ],
        'food' => [
            'Phở bò, phở gà, bún chả',
            'Cà phê trứng, cốm Hà Nội',
            'Bún đậu mắm tôm',
            'Chè cổ truyền phố cổ',
        ],
        'photo' => 'Sáng sớm phố cổ; tối ánh đèn vàng.',
    ],
    'ho chi minh' => [
        'name' => 'TP. Hồ Chí Minh',
        'lat' => 10.8231,
        'lon' => 106.6297,
        'aliases' => ['sai gon', 'sài gòn', 'tp hcm', 'hcm'],
        'vibe' => 'đô thị, ẩm thực, cafe, nightlife',
        'intro' => 'Sài Gòn hợp ăn uống, cafe, tham quan nhanh; nên chia theo quận để đỡ mất thời gian di chuyển.',
        'spots' => [
            'Nhà thờ Đức Bà – Bưu điện',
            'Chợ Bến Thành / Khu phố đi bộ Nguyễn Huệ',
            'Landmark 81 / view sông (tùy ngân sách)',
            'Thảo Cầm Viên',
            'Khu cafe Quận 3 / Quận 1',
        ],
        'food' => [
            'Hủ tiếu, bánh mì Sài Gòn',
            'Cơm tấm',
            'Lẩu, quán nướng đêm',
            'Cafe rooftop',
        ],
        'photo' => 'Blue hour trung tâm; trưa nắng mạnh.',
    ],
    'cat ba' => [
        'name' => 'Cát Bà',
        'lat' => 20.7278,
        'lon' => 107.0481,
        'vibe' => 'biển đảo, trekking, hải sản',
        'intro' => 'Cát Bà kết hợp biển + rừng; nên ở 1–2 đêm nếu muốn thư giãn thật.',
        'spots' => [
            'Bãi biển Cát Cò',
            'Vịnh Lan Hạ (tour kayak / tàu)',
            'Quan Yểm (trek nhẹ)',
            'Phố Cát Bà buổi tối',
        ],
        'food' => ['Hải sản tươi', 'Nem cua bể', 'Ốc, sò địa phương'],
        'photo' => 'Hoàng hôn bãi biển; tour sáng sớm ít nắng.',
    ],
    'nha trang' => [
        'name' => 'Nha Trang',
        'lat' => 12.2388,
        'lon' => 109.1967,
        'vibe' => 'biển, đảo, vinwonder',
        'intro' => 'Nha Trang mạnh về biển và resort; có thể kết hợp đảo trong ngày.',
        'spots' => ['Bãi Trần Phú', 'Vinpearl / đảo Hòn Tre', 'Tháp Bà Ponagar', 'Hòn Mun (lặn)'],
        'food' => ['Nem nướng Nha Trang', 'Bún cá', 'Hải sản', 'Bánh căn'],
        'photo' => 'Sáng biển; tránh mưa bão mùa cuối năm.',
    ],
    'phu quoc' => [
        'name' => 'Phú Quốc',
        'lat' => 10.2899,
        'lon' => 103.984,
        'vibe' => 'biển, hoàng hôn, hải sản, resort',
        'intro' => 'Phú Quốc nên thuê xe máy; chia Bắc–Nam đảo; 3–4 ngày là hợp lý.',
        'spots' => ['Bãi Sao', 'Hoàng hôn Dinh Cậu', 'Grand World / Sunset Sanato', 'Chợ đêm Dương Đông', 'Ngọc Trai / làng chài'],
        'food' => ['Hải sản', 'Ken noodles', 'Nước mắm', 'Sim wine'],
        'photo' => '17h–18h30 hoàng hôn là vàng.',
    ],
    'ha giang' => [
        'name' => 'Hà Giang',
        'lat' => 22.8233,
        'lon' => 104.9833,
        'vibe' => 'chinh phục đèo dốc, núi non hùng vĩ, ruộng bậc thang, bản làng dân tộc',
        'intro' => 'Hà Giang nổi tiếng với cung đường phượt Quản Bạ - Yên Minh - Đồng Văn - Mèo Vạc hùng vĩ. Lịch trình 3 ngày 2 đêm hoặc 4 ngày 3 đêm là lý tưởng nhất.',
        'spots' => [
            'Cột mốc số 0 (Trung tâm TP. Hà Giang)',
            'Dốc Bắc Sum / Cổng trời Quản Bạ',
            'Dốc Thẩm Mã (con đèo uốn lượn chín khoanh dốc)',
            'Phố cổ Đồng Văn (chợ phiên cuối tuần náo nhiệt)',
            'Cột cờ Lũng Cú (Cực Bắc địa đầu Tổ quốc)',
            'Dinh thự họ Vương (Dinh Vua Mèo cổ kính)',
            'Đèo Mã Pí Lèng (một trong tứ đại đỉnh đèo Việt Nam)',
            'Sông Nho Quế / Hẻm Tu Sản (đi thuyền dưới vực sâu)'
        ],
        'food' => [
            'Bánh cuốn Đồng Văn (ăn kèm nước dùng xương ấm nóng)',
            'Cháo ấu tẩu (đặc sản giải cảm ăn đêm)',
            'Thắng cố & mèn mén tại chợ phiên vùng cao',
            'Thịt trâu gác bếp, lạp sườn hun khói',
            'Phở tráng tay Đồng Văn'
        ],
        'photo' => 'Săn mây đỉnh Mã Pí Lèng 6:00-7:00 sáng; hoàng hôn Lũng Cú hoặc cổng trời Quản Bạ 17:00-18:00.',
    ],
    'sapa' => [
        'name' => 'Sapa',
        'lat' => 22.3364,
        'lon' => 103.8438,
        'vibe' => 'sương mù, đỉnh Fansipan, ruộng bậc thang, bản làng mộc mạc',
        'intro' => 'Sapa ngập trong sương mờ với đỉnh Fansipan hùng vĩ và các bản làng Cát Cát, Tả Van mộc mạc. Thích hợp đi 2-3 ngày nghỉ dưỡng hoặc trekking.',
        'spots' => [
            'Đỉnh Fansipan (đi cáp treo ngắm biển mây và đỉnh thiêng)',
            'Bản Cát Cát (bản làng người Hmong gỗ mộc bên thác nước)',
            'Nhà thờ Đá Sapa (trung tâm thị trấn)',
            'Bản Tả Van / Lao Chải (trekking ngắm ruộng bậc thang)',
            'Cổng trời Ô Quy Hồ (đèo Ô Quy Hồ săn hoàng hôn)'
        ],
        'food' => [
            'Lẩu cá hồi / cá tầm Sapa tươi ngon',
            'Đồ nướng sưởi ấm đêm lạnh thị trấn',
            'Thịt lợn cắp nách nướng ống tre',
            'Thắng cố ngựa bản địa'
        ],
        'photo' => '17:00 hoàng hôn đỉnh Ô Quy Hồ; 7:30 sáng đi cáp treo Fansipan săn mây.',
    ],
    'ninh binh' => [
        'name' => 'Ninh Bình',
        'lat' => 20.2506,
        'lon' => 105.9744,
        'vibe' => 'non nước Tràng An, hang động, chùa cổ kính',
        'intro' => 'Ninh Bình sở hữu di sản kép Tràng An sơn thủy hữu tình, hang Múa view toàn cảnh và các ngôi chùa cổ kính. Rất hợp đi 1-2 ngày.',
        'spots' => [
            'Khu du lịch Tràng An / Tam Cốc (đi thuyền nan xuyên hang)',
            'Hang Múa (leo 486 bậc đá ngắm toàn cảnh sông Ngô Đồng)',
            'Chùa Bái Đính (ngôi chùa quy mô lớn kỷ lục)',
            'Cố đô Hoa Lư (đền thờ vua Đinh, vua Lê)',
            'Đầm Vân Long (ngắm voọc quần đùi trắng và cò bay)'
        ],
        'food' => [
            'Cơm cháy ruốc Ninh Bình siêu giòn',
            'Thịt dê núi Ninh Bình (tái dê, dê nướng sả)',
            'Ốc núi luộc gừng sả',
            'Rượu Kim Sơn'
        ],
        'photo' => 'Chiều muộn trên đỉnh Hang Múa; đi thuyền Tràng An lúc 15:00 để nắng xiên dịu đẹp.',
    ],
    'ha long' => [
        'name' => 'Hạ Long',
        'lat' => 20.9509,
        'lon' => 107.0733,
        'vibe' => 'kỳ quan vịnh biển, du thuyền, hang động tự nhiên',
        'intro' => 'Hạ Long nổi tiếng với hàng ngàn đảo đá vôi nhô lên từ vịnh xanh ngọc. Hãy trải nghiệm ngủ đêm trên du thuyền hoặc vui chơi tại Sun World.',
        'spots' => [
            'Vịnh Hạ Long (ngắm Hòn Trống Mái, Động Thiên Cung, Hang Sửng Sốt)',
            'Đảo Ti Tốp (leo đỉnh ngắm vịnh hoặc tắm biển cát trắng)',
            'Công viên Sun World Hạ Long',
            'Bảo tàng Quảng Ninh (thiết kế kính đen độc đáo bên vịnh)',
            'Núi Bài Thơ (ngắm trọn vẹn vịnh từ trên cao)'
        ],
        'food' => [
            'Chả mực giã tay ăn kèm xôi nóng / bánh cuốn',
            'Bánh gật gù Quảng Ninh',
            'Sá sùng Quảng Ninh xào hoặc nấu cháo',
            'Hải sản tươi sống (bề bề, ghẹ, ốc móng tay)'
        ],
        'photo' => 'Hoàng hôn trên vịnh từ boong tàu du lịch; check-in Bảo tàng Quảng Ninh lúc 15:00-16:00.',
    ],
    'hoi an' => [
        'name' => 'Hội An',
        'lat' => 15.8801,
        'lon' => 108.338,
        'vibe' => 'phố cổ đèn lồng, sông Hoài hoài cổ, bình yên',
        'intro' => 'Hội An giữ nguyên nét rêu phong của thương cảng cổ thế kỷ 17. Hãy đi bộ dạo phố cổ và đi thuyền thả hoa đăng trên sông Hoài.',
        'spots' => [
            'Chùa Cầu Nhật Bản (biểu tượng phố cổ)',
            'Hội quán Phúc Kiến / Quảng Đông cổ kính',
            'Sông Hoài (đi thuyền thả hoa đăng tối lung linh)',
            'Rừng dừa Bảy Mẫu (trải nghiệm đi thuyền thúng)',
            'Bãi biển An Bàng (bãi cát hoang sơ, yên bình)'
        ],
        'food' => [
            'Cao lầu Hội An chính gốc',
            'Bánh mì Phượng / Madam Khánh nức tiếng',
            'Cơm gà Hội An (gà xé phay thơm ngọt)',
            'Nước Mót (thảo mộc sả chanh thanh lọc)'
        ],
        'photo' => '6:00-7:00 sáng phố cổ vắng người hoài cổ; 18:30 lúc phố cổ lên đèn lồng.',
    ],
    'hue' => [
        'name' => 'Huế',
        'lat' => 16.4637,
        'lon' => 107.5909,
        'vibe' => 'lăng tẩm cổ kính, sông Hương thơ mộng, ẩm thực phong phú',
        'intro' => 'Huế mang vẻ trầm mặc, thơ mộng của cố đô. Lịch trình 2N1Đ hoặc 3N2Đ là đủ để khám phá Đại Nội, lăng tẩm và ăn sập các món Huế.',
        'spots' => [
            'Đại Nội Huế (Kinh Thành hoàng cung cổ kính)',
            'Chùa Thiên Mụ (ngôi chùa cổ bên bờ sông Hương)',
            'Lăng Khải Định / Lăng Minh Mạng / Lăng Tự Đức cổ kính',
            'Chợ Đông Ba (khám phá ẩm thực và đời sống Huế)',
            'Cầu Trường Tiền (tối lên đèn màu lấp lánh)'
        ],
        'food' => [
            'Bún bò Huế nguyên bản ngon đậm đà',
            'Cơm hến / Bún hến sông Hương',
            'Các loại bánh Huế: bánh bèo, nậm, lọc, ram ít',
            'Chè hẻm Huế (thưởng thức hơn 20 loại chè ngọt ngào)'
        ],
        'photo' => 'Chụp ảnh cổ phục tại Đại Nội hoặc Lăng Khải Định; hoàng hôn sông Hương từ phía chùa Thiên Mụ.',
    ],
    'tam dao' => [
        'name' => 'Tam Đảo',
        'lat' => 21.4589,
        'lon' => 105.6456,
        'vibe' => 'sương mù, núi non, lâu đài cổ kính, không khí mát lạnh',
        'intro' => 'Tam Đảo được ví như Đà Lạt của miền Bắc, khí hậu mát mẻ quanh năm. Cực kỳ lý tưởng cho chuyến đi trốn ngắn ngày 2 ngày 1 đêm hoặc cuối tuần.',
        'spots' => [
            'Nhà thờ đá cổ Tam Đảo (biểu tượng kiến trúc Pháp cổ kính)',
            'Cổng Trời Tam Đảo (nơi ôm trọn toàn cảnh núi rừng mây phủ)',
            'Cầu Mây Tam Đảo (góc check-in sống ảo ngắm thung lũng thông)',
            'Quảng trường trung tâm & Chợ đêm Tam Đảo',
            'Thác Bạc (thác nước giấu mình trong rừng mát lạnh)',
            'Quán Cafe Gió (quán cafe view núi rừng thung lũng cực chill)'
        ],
        'food' => [
            'Đặc sản rau su su Tam Đảo (ngọn su su xào tỏi giòn ngọt)',
            'Thịt xiên nướng than hoa nóng hổi ở chợ đêm',
            'Gà đồi đắp đất nướng hoặc hấp lá chanh',
            'Lợn mán nướng ống tre bản địa'
        ],
        'photo' => 'Săn mây lúc 6:00 - 7:30 sáng tại Cổng Trời hoặc Cầu Mây; đón hoàng hôn núi rừng lúc 17:30 tại góc ngoài trời của Cafe Gió.'
    ],
];
