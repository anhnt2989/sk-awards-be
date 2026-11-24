<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramCriterion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Giải thưởng Việt Nam Rồng Xanh 2026 (Viet Nam Rong Xanh Awards).
 * Source: "Quy chế Giai Thuong Rong Xanh (16.10.2026-final)_nosig.docx" (Quy chế số 16/QC-VIDTI và Phụ lục Tiêu chí chấm điểm).
 *
 * php artisan db:seed --class=RongXanhAwards2026Seeder
 */
class RongXanhAwards2026Seeder extends Seeder
{
    private const ABBR = 'RXA';

    private const YEAR = 2026;

    // Every giải thành phần is scored on these 5 shared criteria (40 điểm) plus its own 5 specific ones (60 điểm).
    private const GENERAL_CRITERIA = [
        ['name' => '1.1 Tính đổi mới sáng tạo', 'max_score' => 10],
        ['name' => '1.2 Tác động và khả năng đo lường', 'max_score' => 10],
        ['name' => '1.3 Khả năng nhân rộng và phát triển bền vững', 'max_score' => 10],
        ['name' => '1.4 Mức độ ứng dụng khoa học công nghệ và chuyển đổi số', 'max_score' => 5],
        ['name' => '1.5 Tuân thủ pháp luật, đạo đức và trách nhiệm xã hội', 'max_score' => 5],
    ];

    private const GENERAL_LABEL = 'I. Tiêu chí chung (40 điểm)';

    private const SPECIFIC_LABEL = 'II. Tiêu chí đặc thù (60 điểm)';

    // Hạng mục -> giải thành phần (the leaf a submission registers on).
    // The "Tên giải thưởng" listed under each giải thành phần are kept as comments: they share its criteria.
    private const CATEGORIES = [
        [
            'name'  => '1. Giải thưởng Rồng Xanh Di sản',
            'color' => '#b45309',
            'sub_categories' => [
                [
                    // Sáng kiến chuyển đổi số di sản; Địa phương chuyển đổi số di sản ấn tượng
                    'name'     => '1.1 Di sản số và Công nghệ vì Di sản (Digital Heritage and Heritage Technology)',
                    'criteria' => [
                        ['name' => '2.1 Hiệu quả ứng dụng công nghệ số, AI, dữ liệu số, thực tế ảo (VR/AR), Digital Twin hoặc các công nghệ mới trong bảo tồn và phát huy giá trị di sản', 'max_score' => 15],
                        ['name' => '2.2 Mức độ đóng góp cho công tác số hóa, lưu trữ, phục dựng, bảo tồn và quản lý di sản', 'max_score' => 15],
                        ['name' => '2.3 Hiệu quả quảng bá, giáo dục, truyền thông và nâng cao khả năng tiếp cận di sản đối với cộng đồng', 'max_score' => 10],
                        ['name' => '2.4 Tác động đối với phát triển du lịch, công nghiệp văn hóa và kinh tế sáng tạo dựa trên giá trị di sản', 'max_score' => 10],
                        ['name' => '2.5 Khả năng nhân rộng, chuyển giao và áp dụng tại các di sản, địa phương hoặc cộng đồng khác', 'max_score' => 10],
                    ],
                ],
                [
                    // Di sản vật thể/phi vật thể được công chúng yêu thích nhất; Lễ hội truyền thống được công chúng yêu thích nhất;
                    // Làng nghề được công chúng yêu thích nhất; Di sản truyền cảm hứng nhất cho các content creators
                    'name'     => '1.2 Di sản được yêu thích nhất (Blue Dragon Award for Most Loved Heritage)',
                    'criteria' => [
                        ['name' => '2.1 Giá trị nổi bật về văn hóa, lịch sử, thiên nhiên hoặc bản sắc cộng đồng', 'max_score' => 15],
                        ['name' => '2.2 Mức độ yêu thích, quan tâm và đánh giá tích cực của công chúng', 'max_score' => 15],
                        ['name' => '2.3 Sức hấp dẫn đối với du khách, cộng đồng và khả năng lan tỏa trên các nền tảng truyền thông', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả bảo tồn, phát huy giá trị và gắn kết cộng đồng với di sản', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho quảng bá hình ảnh địa phương, văn hóa Việt Nam và phát triển du lịch bền vững', 'max_score' => 10],
                    ],
                ],
                [
                    // Cộng đồng bảo tồn di sản xuất sắc; Dự án gìn giữ di sản tiêu biểu
                    'name'     => '1.3 Cộng đồng gìn giữ Di sản (Heritage Preservation Community)',
                    'criteria' => [
                        ['name' => '2.1 Đóng góp thực chất cho bảo tồn và phát huy giá trị di sản', 'max_score' => 20],
                        ['name' => '2.2 Mức độ tham gia và huy động cộng đồng', 'max_score' => 15],
                        ['name' => '2.3 Tác động xã hội, giáo dục và truyền cảm hứng', 'max_score' => 10],
                        ['name' => '2.4 Tính bền vững và khả năng duy trì hoạt động', 'max_score' => 10],
                        ['name' => '2.5 Khả năng lan tỏa, nhân rộng mô hình', 'max_score' => 5],
                    ],
                ],
                [
                    // Địa phương có nhiều sáng kiến bảo tồn và phát huy di sản; Content creator có content sáng tạo về di sản
                    'name'     => '1.4 Sáng tạo từ Di sản (Heritage Creativeness)',
                    'criteria' => [
                        ['name' => '2.1 Giá trị sáng tạo và tính nguyên bản của sản phẩm, dịch vụ', 'max_score' => 15],
                        ['name' => '2.2 Mức độ khai thác và phát huy giá trị di sản', 'max_score' => 15],
                        ['name' => '2.3 Giá trị văn hóa, giáo dục và xã hội', 'max_score' => 10],
                        ['name' => '2.4 Khả năng thương mại hóa và tạo giá trị kinh tế', 'max_score' => 10],
                        ['name' => '2.5 Khả năng mở rộng thị trường và lan tỏa quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    // Thương hiệu di sản có giá trị nhất; Doanh nghiệp đồng hành cùng di sản
                    'name'     => '1.5 Thương hiệu Di sản (Heritage Brand)',
                    'criteria' => [
                        ['name' => '2.1 Hiệu quả bảo tồn, gìn giữ và phát huy giá trị di sản văn hóa, lịch sử hoặc thiên nhiên', 'max_score' => 15],
                        ['name' => '2.2 Thành công trong xây dựng, phát triển và định vị thương hiệu di sản', 'max_score' => 15],
                        ['name' => '2.3 Tác động đối với phát triển kinh tế, du lịch, văn hóa và cộng đồng địa phương', 'max_score' => 10],
                        ['name' => '2.4 Mức độ lan tỏa, quảng bá và nâng cao nhận diện di sản trong nước và quốc tế', 'max_score' => 10],
                        ['name' => '2.5 Mức độ ứng dụng đổi mới sáng tạo, công nghệ số và các giải pháp hiện đại trong bảo tồn, quản lý và phát huy giá trị di sản', 'max_score' => 10],
                    ],
                ],
            ],
        ],
        [
            'name'  => '2. Giải thưởng Rồng Xanh Thương hiệu Điểm đến',
            'color' => '#0284c7',
            'sub_categories' => [
                [
                    // Thành phố thông minh trong trải nghiệm người dùng; Điểm đến áp dụng chuyển đổi số xuất sắc
                    'name'     => '2.1 Điểm đến Thông minh (Smart Destination)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ ứng dụng công nghệ số, dữ liệu số, AI, IoT hoặc các giải pháp thông minh trong quản lý và vận hành điểm đến', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả nâng cao trải nghiệm, khả năng tiếp cận và mức độ hài lòng của du khách hoặc người sử dụng dịch vụ', 'max_score' => 15],
                        ['name' => '2.3 Hiệu quả quản lý tài nguyên, bảo tồn môi trường, văn hóa và phát triển bền vững', 'max_score' => 10],
                        ['name' => '2.4 Mức độ kết nối với hệ sinh thái số, hệ sinh thái du lịch, văn hóa, thương mại hoặc dịch vụ của địa phương', 'max_score' => 10],
                        ['name' => '2.5 Khả năng lan tỏa, nhân rộng mô hình điểm đến thông minh và đóng góp nâng cao năng lực cạnh tranh của địa phương', 'max_score' => 10],
                    ],
                ],
                [
                    // Thành phố điểm đến của năm; Thành phố điểm đến được công chúng Việt Nam yêu thích nhất;
                    // Thành phố điểm đến được công chúng quốc tế yêu thích nhất; Không gian (khu phố/khu vực) văn hóa sáng tạo của năm;
                    // Đại sứ văn hóa ẩm thực Việt Nam tại Việt Nam; Đại sứ văn hóa ẩm thực Việt Nam trên thế giới;
                    // Đại sứ văn hóa cafe Việt Nam tại Việt Nam; Đại sứ văn hóa cafe Việt Nam trên thế giới;
                    // Điểm đến mua sắm (thời trang local brands) được yêu thích nhất;
                    // Điểm đến khám phá (làng nghề, trải nghiệm thủ công) được yêu thích nhất;
                    // Điểm đến trải nghiệm nghỉ dưỡng (wellness, resort) được yêu thích nhất
                    'name'     => '2.2 Điểm đến được yêu thích nhất (Most Loved Destination)',
                    'criteria' => [
                        ['name' => '2.1 Giá trị nổi bật và sức hấp dẫn của điểm đến đối với du khách và cộng đồng', 'max_score' => 15],
                        ['name' => '2.2 Mức độ yêu thích, đánh giá tích cực và sự hài lòng của du khách', 'max_score' => 15],
                        ['name' => '2.3 Sức lan tỏa của điểm đến trên các nền tảng truyền thông, mạng xã hội và truyền thông đại chúng', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả bảo tồn, phát huy các giá trị văn hóa, thiên nhiên và bản sắc địa phương', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp đối với phát triển du lịch, kinh tế địa phương và quảng bá hình ảnh Việt Nam', 'max_score' => 10],
                    ],
                ],
                [
                    // Điểm đến du lịch xanh tiêu biểu; Mô hình du lịch bền vững
                    // Điều 5 names it "Điểm đến Xanh và Bền vững"; the name and English title below follow the Phụ lục.
                    'name'     => '2.3 Du lịch Xanh và Bền vững (Green and Sustainable Tourism)',
                    'criteria' => [
                        ['name' => '2.1 Hiệu quả bảo vệ môi trường và giảm phát thải', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả sử dụng tài nguyên và năng lượng', 'max_score' => 15],
                        ['name' => '2.3 Mức độ tham gia và hưởng lợi của cộng đồng địa phương', 'max_score' => 10],
                        ['name' => '2.4 Áp dụng mô hình kinh tế tuần hoàn và du lịch có trách nhiệm', 'max_score' => 10],
                        ['name' => '2.5 Tính bền vững và khả năng duy trì dài hạn', 'max_score' => 10],
                    ],
                ],
                [
                    // Doanh nghiệp du lịch tiên phong đổi mới; Dịch vụ du lịch sáng tạo của năm
                    'name'     => '2.4 Doanh nghiệp Du lịch Đổi mới sáng tạo (Innovative Tourism Enterprise)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ đổi mới mô hình kinh doanh và quản trị doanh nghiệp', 'max_score' => 15],
                        ['name' => '2.2 Ứng dụng công nghệ và chuyển đổi số trong hoạt động doanh nghiệp', 'max_score' => 15],
                        ['name' => '2.3 Hiệu quả kinh doanh và năng lực cạnh tranh', 'max_score' => 10],
                        ['name' => '2.4 Đóng góp cho phát triển ngành du lịch và cộng đồng', 'max_score' => 10],
                        ['name' => '2.5 Tiềm năng tăng trưởng và phát triển bền vững', 'max_score' => 10],
                    ],
                ],
            ],
        ],
        [
            'name'  => '3. Giải thưởng Rồng Xanh Công nghiệp Văn hóa',
            'color' => '#db2777',
            'sub_categories' => [
                [
                    // Thương hiệu văn hóa sáng tạo đối với: lễ hội festival; nhân vật - film/truyện; nhân vật - game;
                    // nhân vật - quà tặng văn hóa sáng tạo; nền tảng ẩm thực; nền tảng thể thao; show văn hoá;
                    // bài hát/album/MV; phim; sách/xuất bản
                    'name'     => '3.1 Sản phẩm Văn hóa Sáng tạo (Creative Cultural Product)',
                    'criteria' => [
                        ['name' => '2.1 Giá trị sáng tạo, tính độc đáo và khác biệt của sản phẩm', 'max_score' => 15],
                        ['name' => '2.2 Giá trị văn hóa, nghệ thuật và bản sắc Việt Nam', 'max_score' => 15],
                        ['name' => '2.3 Khả năng thương mại hóa và phát triển thị trường', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả kinh tế, xã hội và truyền thông', 'max_score' => 10],
                        ['name' => '2.5 Khả năng lan tỏa và hội nhập quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    // Chiến dịch truyền thông văn hóa ấn tượng; Nền tảng số quảng bá văn hóa xuất sắc
                    'name'     => '3.2 Nội dung số và Truyền thông Văn hóa (Digital Content and Cultural Communication)',
                    'criteria' => [
                        ['name' => '2.1 Chất lượng nội dung và giá trị văn hóa được truyền tải', 'max_score' => 15],
                        ['name' => '2.2 Mức độ sáng tạo trong hình thức thể hiện và truyền thông', 'max_score' => 15],
                        ['name' => '2.3 Hiệu quả tiếp cận, tương tác và lan tỏa tới công chúng', 'max_score' => 10],
                        ['name' => '2.4 Ứng dụng công nghệ số, AI và các công nghệ mới', 'max_score' => 10],
                        ['name' => '2.5 Khả năng quảng bá hình ảnh Việt Nam ra quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    // Thương hiệu văn hóa vươn tầm quốc tế; Thương hiệu văn hóa truyền cảm hứng
                    'name'     => '3.3 Thương hiệu Văn hóa Việt Nam (Vietnam Cultural Brand)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ gắn kết với các giá trị văn hóa Việt Nam', 'max_score' => 15],
                        ['name' => '2.2 Giá trị thương hiệu và uy tín xã hội', 'max_score' => 15],
                        ['name' => '2.3 Khả năng quảng bá hình ảnh, bản sắc Việt Nam', 'max_score' => 10],
                        ['name' => '2.4 Tác động kinh tế, văn hóa và xã hội', 'max_score' => 10],
                        ['name' => '2.5 Khả năng phát triển và hội nhập quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    // Chiến dịch quảng cáo của nhãn hàng đóng góp vào quảng bá văn hoá Việt Nam
                    'name'     => '3.4 Dự án Quảng bá hình ảnh Việt Nam (Vietnam Image Promotion Project)',
                    'criteria' => [
                        ['name' => '2.1 Hiệu quả quảng bá hình ảnh Việt Nam trong nước và quốc tế', 'max_score' => 20],
                        ['name' => '2.2 Quy mô ảnh hưởng và độ phủ truyền thông', 'max_score' => 15],
                        ['name' => '2.3 Tính sáng tạo trong nội dung và phương thức triển khai', 'max_score' => 10],
                        ['name' => '2.4 Tác động đối với nhận thức và hình ảnh quốc gia', 'max_score' => 10],
                        ['name' => '2.5 Khả năng lan tỏa và duy trì hiệu quả lâu dài', 'max_score' => 5],
                    ],
                ],
            ],
        ],
        [
            'name'  => '4. Giải thưởng Rồng Xanh Đổi mới Sáng tạo',
            'color' => '#7c3aed',
            'sub_categories' => [
                [
                    // Sản phẩm công nghệ O2O của năm; Sản phẩm quản lý & phân tích data của năm; Sản phẩm UI/UX của năm;
                    // Sản phẩm công nghệ trải nghiệm của năm; Sản phẩm công nghệ thanh toán của năm
                    'name'     => '4.1 AI vì Văn hóa và Du lịch (AI for Culture and Tourism)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ ứng dụng AI trong lĩnh vực văn hóa, di sản hoặc du lịch', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả giải quyết vấn đề và tạo giá trị thực tiễn', 'max_score' => 15],
                        ['name' => '2.3 Tính tiên phong và khả năng đổi mới công nghệ', 'max_score' => 10],
                        ['name' => '2.4 Khả năng triển khai, thương mại hóa và mở rộng ứng dụng', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho hệ sinh thái đổi mới sáng tạo quốc gia', 'max_score' => 10],
                    ],
                ],
                [
                    // Startup công nghệ vì cộng đồng; Startup triển vọng nhất trong lĩnh vực văn hóa - du lịch
                    'name'     => '4.2 Startup Đổi mới Sáng tạo (Innovation Startup)',
                    'criteria' => [
                        ['name' => '2.1 Tính đột phá của ý tưởng, sản phẩm hoặc mô hình kinh doanh', 'max_score' => 15],
                        ['name' => '2.2 Tiềm năng tăng trưởng và khả năng mở rộng thị trường', 'max_score' => 15],
                        ['name' => '2.3 Khả năng thương mại hóa và thu hút đầu tư', 'max_score' => 10],
                        ['name' => '2.4 Năng lực đội ngũ sáng lập và quản trị doanh nghiệp', 'max_score' => 10],
                        ['name' => '2.5 Tác động kinh tế, xã hội và đổi mới sáng tạo', 'max_score' => 10],
                    ],
                ],
                [
                    // Nền tảng thương mại điện tử văn hóa; Nền tảng kết nối du lịch thông minh
                    'name'     => '4.3 Nền tảng số Xuất sắc (Outstanding Digital Platform)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ hoàn thiện và năng lực công nghệ của nền tảng', 'max_score' => 15],
                        ['name' => '2.2 Quy mô người dùng và hiệu quả khai thác nền tảng', 'max_score' => 15],
                        ['name' => '2.3 Khả năng tích hợp, kết nối và chia sẻ dữ liệu', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả tạo giá trị cho người dùng và cộng đồng', 'max_score' => 10],
                        ['name' => '2.5 Khả năng phát triển bền vững và mở rộng hệ sinh thái', 'max_score' => 10],
                    ],
                ],
                [
                    // Nhà đổi mới sáng tạo trẻ trong lĩnh vực công nghệ; Tài năng trẻ khởi nghiệp trong ngành văn hóa
                    'name'     => '4.4 Nhà Đổi mới Sáng tạo trẻ (Young Innovation Leader)',
                    'criteria' => [
                        ['name' => '2.1 Thành tích nổi bật trong đổi mới sáng tạo, nghiên cứu hoặc khởi nghiệp', 'max_score' => 20],
                        ['name' => '2.2 Khả năng tạo ảnh hưởng và truyền cảm hứng cho cộng đồng', 'max_score' => 15],
                        ['name' => '2.3 Giá trị ứng dụng thực tiễn của các sáng kiến, công trình hoặc dự án', 'max_score' => 10],
                        ['name' => '2.4 Tiềm năng phát triển và đóng góp trong tương lai', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho cộng đồng và xã hội', 'max_score' => 5],
                    ],
                ],
            ],
        ],
        [
            'name'  => '5. Giải thưởng Rồng Xanh STEAM',
            'color' => '#059669',
            'sub_categories' => [
                [
                    // Mô hình giáo dục STEAM xuất sắc nhất; Chương trình đào tạo kỹ năng số cho văn hóa
                    'name'     => '5.1 Mô hình sáng tạo trong giáo dục và đào tạo (Educational Innovative Model)',
                    'criteria' => [
                        ['name' => '2.1 Mức độ đổi mới trong mô hình giáo dục, phương pháp đào tạo hoặc công nghệ giáo dục', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả nâng cao chất lượng học tập và giảng dạy, đào tạo hoặc phát triển năng lực người học', 'max_score' => 15],
                        ['name' => '2.3 Mức độ ứng dụng AI, dữ liệu lớn, nền tảng số hoặc công nghệ mới trong giáo dục', 'max_score' => 10],
                        ['name' => '2.4 Quy mô triển khai, số lượng người học hoặc đối tượng thụ hưởng', 'max_score' => 10],
                        ['name' => '2.5 Khả năng nhân rộng, thương mại hóa hoặc áp dụng trong nhiều bậc học khác nhau', 'max_score' => 10],
                    ],
                ],
                [
                    // Trung tâm đào tạo STEAM tiêu biểu; Dự án đào tạo sáng tạo kết nối cộng đồng
                    'name'     => '5.2 Mô hình tổ chức đào tạo sáng tạo trong các lĩnh vực STEAM (STEAM Skills Development Model)',
                    'criteria' => [
                        ['name' => '2.1 Nội dung chương trình đào tạo kỹ năng STEAM (Science, Tech, Engineering, Arts, Math)', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả phát triển năng lực giao tiếp, tư duy, đổi mới sáng tạo, làm việc nhóm và học tập thích ứng của người học', 'max_score' => 15],
                        ['name' => '2.3 Tác động của các nhóm kỹ năng STEAM với việc phát triển nhân lực các ngành công nghệ mũi nhọn (tỷ lệ học viên đỗ vào các trường khối STEAM)', 'max_score' => 10],
                        ['name' => '2.4 Mức độ đổi mới phương pháp đào tạo, huấn luyện và trải nghiệm học tập (project-based, entrepreneur-based, …)', 'max_score' => 10],
                        ['name' => '2.5 Uy tín tổ chức, đội ngũ giảng viên và khả năng lan tỏa giá trị tới cộng đồng', 'max_score' => 10],
                    ],
                ],
                [
                    // Viện/Học viện đào tạo công nghiệp văn hóa; Chương trình bồi dưỡng lãnh đạo trẻ ngành văn hóa
                    'name'     => '5.3 Mô hình tổ chức đào tạo về công nghiệp văn hoá và sáng tạo (Cultural and Creative Industrial Development Model)',
                    'criteria' => [
                        ['name' => '2.1 Nội dung chương trình đào tạo, bồi dưỡng năng lực của ngành công nghiệp văn hoá, sáng tạo, sở hữu trí tuệ', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả phát triển các năng lực sáng tạo, trải nghiệm người dùng, sáng tạo công nghiệp', 'max_score' => 15],
                        ['name' => '2.3 Mức độ tích hợp tư duy đổi mới sáng tạo, chuyển đổi số với tư duy văn hoá sáng tạo, IP.', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả phát triển năng lực sáng tạo văn hoá, học tập và phát triển tích cực và thích ứng thông qua các phương pháp giảng dạy và đào tạo để phát huy năng lực cá nhân', 'max_score' => 10],
                        ['name' => '2.5 Uy tín tổ chức, đội ngũ giảng viên và khả năng lan tỏa giá trị tới cộng đồng sáng tạo, kết nối cộng đồng sáng tạo quốc tế.', 'max_score' => 10],
                    ],
                ],
                [
                    // Chương trình phát triển lãnh đạo thế hệ mới; Dự án rèn luyện sức mạnh mềm tiêu biểu
                    'name'     => '5.4 Mô hình tổ chức đào tạo Lãnh đạo tương lai và Sức mạnh mềm (Future Leaders and Soft-Power Skills Development Organization)',
                    'criteria' => [
                        ['name' => '2.1 Chất lượng chương trình đào tạo, bồi dưỡng năng lực lãnh đạo, quản trị và kiến tạo tương lai, kỹ năng nghề nghiệp và năng lực công dân số', 'max_score' => 15],
                        ['name' => '2.2 Hiệu quả phát triển đội ngũ lãnh đạo trẻ, doanh nhân trẻ, nhà quản lý và nhân tài tương lai', 'max_score' => 15],
                        ['name' => '2.3 Mức độ tích hợp tư duy đổi mới sáng tạo, chuyển đổi số, quản trị hiện đại và phát triển bền vững', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả phát triển năng lực giao tiếp, tư duy, sáng tạo, làm việc nhóm và thích ứng của học viên', 'max_score' => 10],
                        ['name' => '2.5 Tầm ảnh hưởng, khả năng kết nối hệ sinh thái lãnh đạo và phát triển nguồn nhân lực chất lượng cao', 'max_score' => 10],
                    ],
                ],
            ],
        ],
        [
            // Highest category: nominees are picked from the best entries of categories 1-5, TOP 3 per giải thành phần.
            'name'  => '6. Giải thưởng đặc biệt Rồng Xanh Impact',
            'color' => '#dc2626',
            'sub_categories' => [
                [
                    'name'     => '6.1 Điểm đến Rồng Xanh (Blue Dragon Destination)',
                    'criteria' => [
                        ['name' => '2.1 Thành tựu nổi bật trong phát triển và nâng cao giá trị điểm đến', 'max_score' => 15],
                        ['name' => '2.2 Tác động đối với phát triển du lịch, kinh tế, văn hóa và cộng đồng địa phương', 'max_score' => 15],
                        ['name' => '2.3 Mức độ hấp dẫn, sức lan tỏa và khả năng thu hút du khách trong nước, quốc tế', 'max_score' => 10],
                        ['name' => '2.4 Hiệu quả bảo tồn và phát huy giá trị văn hóa, thiên nhiên, di sản và bản sắc địa phương', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho việc quảng bá hình ảnh và nâng cao vị thế Việt Nam trên trường quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    'name'     => '6.2 Lãnh đạo Tác động Xuất sắc (Blue Dragon Outstanding Impact Leadership)',
                    'criteria' => [
                        ['name' => '2.1 Tầm nhìn chiến lược và năng lực kiến tạo thay đổi', 'max_score' => 15],
                        ['name' => '2.2 Kết quả và tác động thực tiễn đối với tổ chức, cộng đồng hoặc xã hội', 'max_score' => 15],
                        ['name' => '2.3 Khả năng dẫn dắt đổi mới sáng tạo và phát triển bền vững', 'max_score' => 10],
                        ['name' => '2.4 Uy tín, ảnh hưởng và khả năng truyền cảm hứng', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho sự phát triển của Việt Nam và hội nhập quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    'name'     => '6.3 Doanh nghiệp Tác động vì Việt Nam (Blue Dragon Vietnam Impact Enterprise)',
                    'criteria' => [
                        ['name' => '2.1 Tác động kinh tế, xã hội và tạo việc làm', 'max_score' => 15],
                        ['name' => '2.2 Đổi mới sáng tạo, chuyển đổi số và năng lực cạnh tranh', 'max_score' => 15],
                        ['name' => '2.3 Trách nhiệm xã hội, môi trường và phát triển bền vững', 'max_score' => 10],
                        ['name' => '2.4 Khả năng lan tỏa giá trị Việt Nam thông qua sản phẩm, dịch vụ hoặc thương hiệu', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho cộng đồng và sự phát triển quốc gia', 'max_score' => 10],
                    ],
                ],
                [
                    'name'     => '6.4 Lãnh đạo/Cá nhân truyền cảm hứng (Blue Dragon Inspirational Individual)',
                    'criteria' => [
                        ['name' => '2.1 Thành tựu nổi bật và những đóng góp có giá trị đối với tổ chức, cộng đồng, địa phương hoặc quốc gia', 'max_score' => 15],
                        ['name' => '2.2 Khả năng truyền cảm hứng, dẫn dắt và tạo động lực cho cộng đồng, đặc biệt đối với thế hệ trẻ', 'max_score' => 15],
                        ['name' => '2.3 Tinh thần tiên phong, đổi mới sáng tạo, bản lĩnh vượt khó và kiến tạo giá trị tích cực cho xã hội', 'max_score' => 10],
                        ['name' => '2.4 Mức độ lan tỏa của hình ảnh, câu chuyện, sáng kiến hoặc hoạt động thông qua cộng đồng và truyền thông', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp trong việc quảng bá hình ảnh, con người, văn hóa và các giá trị tốt đẹp của Việt Nam trong nước và quốc tế', 'max_score' => 10],
                    ],
                ],
                [
                    'name'     => '6.5 Thương hiệu truyền cảm hứng (Blue Dragon Inspiring Brand)',
                    'criteria' => [
                        ['name' => '2.1 Giá trị cốt lõi, hình ảnh và uy tín thương hiệu được xã hội ghi nhận', 'max_score' => 15],
                        ['name' => '2.2 Mức độ lan tỏa, truyền cảm hứng và tạo ảnh hưởng tích cực tới cộng đồng', 'max_score' => 15],
                        ['name' => '2.3 Thành tựu nổi bật trong đổi mới sáng tạo, phát triển bền vững hoặc cống hiến xã hội', 'max_score' => 10],
                        ['name' => '2.4 Khả năng dẫn dắt xu hướng, tạo động lực và khơi dậy các giá trị tốt đẹp trong xã hội', 'max_score' => 10],
                        ['name' => '2.5 Đóng góp cho việc quảng bá hình ảnh, giá trị văn hóa và thương hiệu Việt Nam trong nước và quốc tế', 'max_score' => 10],
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $id = self::ABBR . '-' . self::YEAR;

        if (Program::where('id', $id)->exists()) {
            $this->command?->warn("Rồng Xanh Awards 2026 đã tồn tại ({$id}), bỏ qua.");

            return;
        }

        // Possibly created by hand through the admin UI under the ABBR-N scheme
        $existing = Program::where('abbr', self::ABBR)->where('year', self::YEAR)->value('id');
        if ($existing !== null) {
            $this->command?->warn("Đã có chương trình Rồng Xanh 2026 với mã {$existing}, bỏ qua.");

            return;
        }

        DB::transaction(fn () => $this->create($id));
    }

    private function create(string $id): void
    {
        $program = Program::create([
            'id'          => $id,
            'name'        => 'Việt Nam Rồng Xanh - Rong Xanh Awards',
            'year'        => self::YEAR,
            'abbr'        => self::ABBR,
            'color'       => '#1d4ed8',
            'status'      => 'active',
            'deadline'    => '2026-11-08',
            'description' => 'Giải thưởng Việt Nam Rồng Xanh (Viet Nam Rong Xanh Awards) năm 2026: 6 hạng mục, 26 giải thành phần. Kỳ xét thành tích từ 01/11/2024 đến 31/10/2026.',
            'next_sub_id' => 1,
        ]);

        $general = array_map(
            fn (array $crit) => $crit + ['description' => self::GENERAL_LABEL],
            self::GENERAL_CRITERIA,
        );

        // Same ID scheme for categories and criteria as ProgramController::store
        $crIdx = 1;
        foreach (self::CATEGORIES as $ci => $cat) {
            $catId = $program->id . '-C' . ($ci + 1);
            ProgramCategory::create([
                'id'         => $catId,
                'program_id' => $program->id,
                'parent_id'  => null,
                'name'       => $cat['name'],
                'color'      => $cat['color'],
                'sort_order' => $ci,
            ]);

            foreach ($cat['sub_categories'] as $si => $sc) {
                $scId = $catId . '-SC' . ($si + 1);
                ProgramCategory::create([
                    'id'         => $scId,
                    'program_id' => $program->id,
                    'parent_id'  => $catId,
                    'name'       => $sc['name'],
                    'color'      => $cat['color'],
                    'sort_order' => $si,
                ]);

                $specific = array_map(
                    fn (array $crit) => $crit + ['description' => self::SPECIFIC_LABEL],
                    $sc['criteria'],
                );

                foreach ([...$general, ...$specific] as $ki => $crit) {
                    ProgramCriterion::create([
                        'id'          => $program->id . '-cr' . $crIdx++,
                        'program_id'  => $program->id,
                        'category_id' => $scId,
                        'name'        => $crit['name'],
                        'description' => $crit['description'],
                        'max_score'   => $crit['max_score'],
                        'sort_order'  => $ki,
                    ]);
                }
            }
        }

        $this->command?->info("Đã tạo chương trình {$program->id} với " . ($crIdx - 1) . ' tiêu chí.');
    }
}
