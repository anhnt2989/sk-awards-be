<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramCriterion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SEI Awards 2026 - Biểu dương Sáng kiến Giáo dục thông minh.
 * Source: "Hồ sơ đăng ký SEI AWARDS 2026.docx".
 *
 * php artisan db:seed --class=SeiAwards2026Seeder
 */
class SeiAwards2026Seeder extends Seeder
{
    private const ABBR = 'SEI';

    private const YEAR = 2026;

    // Same shape as the POST /api/programs payload.
    // Each scored row of the form (1.1, 1.2, ...) is one criterion; every category totals 100.
    private const CATEGORIES = [
        [
            'name'  => 'Môi trường giáo dục của năm',
            'color' => '#3b82f6',
            'sub_categories' => [
                [
                    'name'     => 'Môi trường giáo dục của năm (Educational Environment of the Year)',
                    'criteria' => [
                        // Tiêu chí 1: Thư viện, trang thiết bị học tập và cơ sở vật chất (20 điểm)
                        ['name' => '1.1 Môi trường giáo dục có các khu vực phục vụ hoạt động của trường ứng dụng thích hợp các công nghệ số:', 'description' => 'Phòng học thông minh; Phòng thí nghiệm/thực hành; Phòng tra cứu thông tin số / điện tử', 'max_score' => 5],
                        ['name' => '1.2 Các trang thiết bị học tập tại phòng học, phòng làm việc:', 'description' => 'Máy chiếu; Bảng thông minh; Máy tính hỗ trợ học tập; Thiết bị điều khiển tự động', 'max_score' => 5],
                        ['name' => '1.3 Mức độ cung cấp học liệu số và dịch vụ thông tin tư liệu cho người học, người dạy tại cơ sở giáo dục:', 'description' => 'Giáo trình điện tử; Bài giảng điện tử; Tài liệu tham khảo số hoá; Học liệu số hoá; Dịch vụ tra cứu thông tin số', 'max_score' => 5],
                        ['name' => '1.4 Tình trạng mạng và kết nối internet tại trường học/ cơ sở giáo dục, đảm bảo độ ổn định để hỗ trợ sử dụng công nghệ trong giảng dạy và học tập.', 'description' => 'Mạng có dây; Mạng không dây; Internet tốc độ cao', 'max_score' => 5],
                        // Tiêu chí 2: Mức độ tích hợp công nghệ trong hoạt động dạy và học ở cơ sở giáo dục (Ưu tiên ứng dụng AI) (20 điểm)
                        ['name' => '2.1 Mức độ ứng dụng hình thức giáo dục e-learning trong môi trường giáo dục:', 'description' => 'Sử dụng công cụ soạn bài điện tử; Công cụ mô phỏng; Công cụ tạo bài kiểm tra; Công cụ tạo bài trình bày có multimedia; Công cụ hội thảo điện tử; Hệ thống tài liệu số trên LCMS', 'max_score' => 5],
                        ['name' => '2.2 Mức độ sử dụng AI trong quản lý, hoạt động dạy và học.', 'description' => 'Có sử dụng AI/GenAI trong quản lý và quản trị nhà trường; Có sử dụng AI/GenAI trong hoạt động dạy học; Có sử dụng AI/GenAI trong hỗ trợ hoạt động học tập của người học', 'max_score' => 5],
                        ['name' => '2.3 Sử dụng hệ thống quản lý giáo dục và học viên LMS có tích hợp AI để quản lý, vận hành', 'description' => 'Có hệ thống quản lý học tập LMS; Mức độ sử dụng thường xuyên LMS; Mức độ hoạt động giảng dạy trên LMS', 'max_score' => 5],
                        ['name' => '2.4 Sử dụng giáo án điện tử, công cụ số có hỗ trợ bởi AI trong giảng dạy', 'description' => 'Máy tính mô phỏng; Tài liệu số multimedia; Hệ thống hỗ trợ trực quan, tương tác', 'max_score' => 5],
                        // Tiêu chí 3: Kỹ năng số của đội ngũ giáo viên, cán bộ của cơ sở giáo dục sử dụng trong dạy và học (20 điểm)
                        ['name' => '3.1 Đội ngũ giáo viên, cán bộ trong tổ chức giáo dục sử dụng hiệu quả các phần mềm, ứng dụng công nghệ và có kỹ năng về AI trong hoạt động giảng dạy và các công việc khác.', 'description' => null, 'max_score' => 5],
                        ['name' => '3.2 Liên tục cập nhật, tìm hiểu và triển khai áp dụng những công nghệ mới đang thay đổi hàng ngày để đáp ứng được nhu cầu của người học. Tích hợp AI/GenAI trong thiết kế bài giảng, hướng dẫn', 'description' => null, 'max_score' => 5],
                        ['name' => '3.3 Mức độ làm chủ được công nghệ số, công nghệ AI để sẵn sàng hỗ trợ người học cách tiếp cận', 'description' => null, 'max_score' => 5],
                        ['name' => '3.4 Khả năng quản lý và sử dụng dữ liệu, cải thiện việc dạy và học. Hiểu rõ các biện pháp bảo mật và quyền riêng tư được áp dụng để bảo vệ thông tin cá nhân và dữ liệu học tập.', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 4: Quản lý cơ sở học tập và cơ sở vật chất nhà trường/tổ chức giáo dục đào tạo ứng dụng công nghệ số (20 điểm)
                        ['name' => '4.1 Mức độ sử dụng các công nghệ số trong tự động hóa trong sử dụng hiệu quả cơ sở vật chất:', 'description' => 'Mức độ tự động hoá (đèn LED, thiết bị điện tử); Hệ thống AI camera hỗ trợ quản lý hiệu quả; Số hóa các cơ sở vật chất', 'max_score' => 5],
                        ['name' => '4.2 Các phòng học, khu vực học tập được trang bị các hệ thống số hỗ trợ giảng dạy và học tập được tự động hoá', 'description' => 'Có hệ thống điều khiển cho giảng viên; Có hệ thống tự động hóa điều khiển ánh sáng, điều hoà; Có sử dụng hệ thống IoT trong giảng đường', 'max_score' => 5],
                        ['name' => '4.3 Có hệ thống quản lý phòng học, giảng đường được số hoá cho công tác quản lý và phân bố thời khóa biểu; có tích hợp vào hệ thống quản lý chung của nhà trường trong việc phân bố thời khoá biểu và kế hoạch giảng dạy', 'description' => null, 'max_score' => 5],
                        ['name' => '4.4 Có kế hoạch nâng cấp, bảo trì nhằm đảm bảo tính hiện đại, năng lực phục vụ cho giảng dạy, học tập của sinh viên/học sinh', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 5: Ứng dụng các công nghệ vào hoạt động hành chính (20 điểm)
                        ['name' => '5.1 Sử dụng phần mềm và hệ thống quản lý trường học để quản lý thông tin người học, người dạy, và nhân viên hành chính.', 'description' => null, 'max_score' => 5],
                        ['name' => '5.2 Mức độ chuyển đổi từ tài liệu và văn bản truyền thống sang phiên bản số hóa, bao gồm cả hồ sơ học sinh và quản lý hồ sơ nhân viên.', 'description' => null, 'max_score' => 5],
                        ['name' => '5.3 Có sử dụng hệ thống quản lý nguồn lực chung để phục công tác điều hành, quản lý và ra quyết định', 'description' => null, 'max_score' => 5],
                        ['name' => '5.4 Có sử dụng các hệ thống quản lý tác nghiệp phục vụ các mảng công tác liên quan: kế hoạch đầu tư, mua sắm trang thiết bị, đào tạo bồi dưỡng', 'description' => null, 'max_score' => 5],
                    ],
                ],
            ],
        ],
        [
            'name'  => 'Sáng tạo của năm/ Nhà lãnh đạo Đổi mới sáng tạo',
            'color' => '#8b5cf6',
            'sub_categories' => [
                [
                    'name'     => 'Sáng tạo của năm/ Nhà lãnh đạo Đổi mới sáng tạo (Innovation/Vator of the year)',
                    'criteria' => [
                        // Tiêu chí 1: Tính đột phá và ứng dụng công nghệ số (20 điểm)
                        ['name' => '1.1 Sáng kiến/giải pháp sử dụng các công nghệ số tiên tiến và đột phá trong dạy học và quản lý như:', 'description' => 'Trí tuệ nhân tạo (AI); Tự động hóa quy trình (RPA); Internet of Things (IoT); Điện toán đám mây', 'max_score' => 5],
                        ['name' => '1.2 Có kế hoạch thúc đẩy Sáng tạo trong dạy – học sử dụng AI/GenAI và khả năng phát triển và cải thiện liên tục nhằm đáp ứng yêu cầu ngày càng cao trong tương lai, thông qua các sáng kiến số trong dạy học và quản lý', 'description' => null, 'max_score' => 5],
                        ['name' => '1.3 Sáng tạo được phát triển dựa trên:', 'description' => 'Kế thừa, phát triển dựa trên các nền tảng sẵn có; Tự nghiên cứu và phát triển từ đầu; Tích hợp với việc sử dụng các công cụ GenAI', 'max_score' => 5],
                        ['name' => '1.4 Tính mới và đột phá trong sáng tạo:', 'description' => 'Sáng tạo độc đáo; Có tính mới, độc đáo; Có tính đột phá về công nghệ / Mô hình', 'max_score' => 5],
                        // Tiêu chí 2: Tính khả thi và ứng dụng trong thực tế (20 điểm)
                        ['name' => '2.1 Triển khai dễ dàng và hiệu quả trong môi trường giáo dục thực tế. Nêu các ứng dụng cụ thể', 'description' => null, 'max_score' => 10],
                        ['name' => '2.2 Tích hợp với các hệ thống và ứng dụng giáo dục hiện có tại các trường học hoặc tổ chức giáo dục, không gây xung đột với hệ thống khác. Nêu các ứng dụng cụ thể', 'description' => null, 'max_score' => 5],
                        ['name' => '2.3 Khả năng mở rộng hoặc thay đổi linh hoạt để phù hợp với quy mô và cơ cấu của các trường học hoặc tổ chức giáo dục có tính chất khác nhau. Nêu các khả năng ứng dụng thực tiễn.', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 3: Hiệu quả của sáng tạo trong triển khai dạy học tại cơ sở giáo dục (20 điểm)
                        ['name' => '3.1 Nâng cao hiệu quả quản trị và quản lý cơ sở giáo dục, hiệu quả giảng dạy:', 'description' => 'Tăng hiệu suất làm việc; Cải thiện quản lý tại cơ sở giáo dục; Phát huy tính tích cực, chủ động, sáng tạo của người học, người dạy trong giảng dạy.', 'max_score' => 5],
                        ['name' => '3.2 Hỗ trợ giảng dạy và học tập trực tuyến hoặc trên môi trường số một cách hiệu quả.', 'description' => 'Tỷ lệ giảng dạy học tập online; Số lượng truy cập; Số lượng tài khoản online…', 'max_score' => 5],
                        ['name' => '3.3 Sáng tạo đã được thử nghiệm và có kết quả khả quan trong việc áp dụng thực tế tại cơ sở giáo dục, đem lại lợi ích rõ ràng và giải quyết các vấn đề thực tế trong quá trình giảng dạy và học tập.', 'description' => null, 'max_score' => 5],
                        ['name' => '3.4 Có kế hoạch rõ ràng để cải thiện sáng tạo trong triển khai dạy học tại cơ sở giáo dục phù hợp với sự thay đổi của công nghệ và đặc biệt là công nghệ AI – thay đổi căn bản và toàn diện dạy và học.', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 4: Mức độ đóng góp của sáng tạo với cộng đồng giáo dục (20 điểm)
                        ['name' => '4.1 Sáng tạo mang đến sự tương tác tích cực và xây dựng mối quan hệ trong cộng đồng giáo dục.', 'description' => 'Tạo điều kiện để người dùng (giáo viên, học sinh, phụ huynh) có cơ hội chia sẻ ý kiến, góp ý và tham gia vào quá trình phát triển và cải thiện giải pháp. Có các tài liệu, giới thiệu, hướng dẫn trực tuyến.', 'max_score' => 10],
                        ['name' => '4.2 Khảo sát mức độ đóng góp của sáng tạo đối với cộng đồng giáo dục và nhận được phản hồi tích cực, tạo ra môi trường học tập giảng dạy tích cực, thân thiện và giảm tải cho người học và người giáo viên', 'description' => null, 'max_score' => 5],
                        ['name' => '4.3 Sáng tạo đã tham gia hoặc hỗ trợ vào việc giải quyết các vấn đề quan trọng trong lĩnh vực giáo dục như cải thiện chất lượng giảng dạy, tăng cường quản lý giáo dục.', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 5: Tiềm năng tăng trưởng của sáng tạo trong tương lai (20 điểm)
                        ['name' => '5.1 Xác định được tiềm năng sáng tạo trong dạy học sử dụng các công nghệ mới nổi trong chuyển đổi số, đặc biệt là AI/GenAI', 'description' => null, 'max_score' => 5],
                        ['name' => '5.2 Xác định được tiềm năng sáng tạo trong học tập sử dụng các công nghệ mới nổi trong chuyển đổi số, đặc biệt là AI/GenAI', 'description' => null, 'max_score' => 5],
                        ['name' => '5.3 Xác định được tiềm năng của sáng tạo trong cải thiện chất lượng, hiệu quả công tác, hoạt động dạy và học với công nghệ số và đặc biệt là công nghệ AI/GenAI', 'description' => null, 'max_score' => 5],
                        ['name' => '5.4 Lãnh đạo tiềm năng: sáng tạo trong công tác quản lý điều hành và ra quyết định trong phát triển nhà trường, tổ chức giáo dục, cải thiện môi trường giáo dục và thúc đẩy chất lượng giáo dục đào tạo nói chung,', 'description' => 'tạo ra mô hình quản trị mới dựa trên sự tiến bộ của KHCN, chuyển đổi số và công nghệ AI.', 'max_score' => 5],
                    ],
                ],
            ],
        ],
        [
            'name'  => 'Ảnh hưởng của năm',
            'color' => '#10b981',
            'sub_categories' => [
                [
                    'name'     => 'Ảnh hưởng của năm (Impact Award)',
                    'criteria' => [
                        // Tiêu chí 1: Công nghệ thông minh của sản phẩm, giải pháp (20 điểm)
                        ['name' => '1.1 Sản phẩm, giải pháp công nghệ số sử dụng các công nghệ CMCN 4.0:', 'description' => 'AI (bao gồm Machine learning, Deep learning), GenAI; VR/AR/MR/XR; 3D printing; IoT, Edge computing (điện toán biên); Mobile – Cloud computing (điện toán di động - đám mây); RPA', 'max_score' => 5],
                        ['name' => '1.2 Sản phẩm, giải pháp công nghệ số phục vụ:', 'description' => 'Trường học có hạ tầng thông minh (Smart school); Quản trị thông minh (Smart administration); Dạy và học thông minh (Smart teaching/learning); Công cụ hỗ trợ dạy học dựa trên GenAI', 'max_score' => 5],
                        ['name' => '1.3 Sản phẩm, giải pháp công nghệ số được phát triển dựa trên:', 'description' => 'Kế thừa, phát triển dựa trên các nền tảng sẵn có; Tự nghiên cứu và phát triển từ đầu', 'max_score' => 5],
                        ['name' => '1.4 Sản phẩm, giải pháp công nghệ số có sử dụng các kiến trúc dữ liệu:', 'description' => 'CSDL quan hệ; CSDL phi quan hệ; Kho dữ liệu (Data Warehouse); Hồ Dữ liệu (Data Lake)', 'max_score' => 5],
                        // Tiêu chí 2: Tính năng, quản trị và an toàn dữ liệu (20 điểm)
                        ['name' => '2.1 Các tính năng của sản phẩm, giải pháp công nghệ số đảm bảo:', 'description' => 'Tính dễ sử dụng và tương tác cao; Tích hợp nội dung đa phương tiện; Khả năng cá thể/cá nhân hoá (UX); An toàn và bảo mật dữ liệu', 'max_score' => 5],
                        ['name' => '2.2 Sản phẩm, giải pháp công nghệ đảm bảo các yêu cầu về an toàn, anh ninh dữ liệu theo quy định của Bộ TT&TT:', 'description' => 'Đảm bảo theo yêu cầu về an toàn bảo mật thông tin; Chữ ký số; Đảm bảo theo NĐ 13/NĐ-CP', 'max_score' => 5],
                        ['name' => '2.3 Sản phẩm, giải pháp công nghệ cung cấp nền tảng quản lý dữ liệu để đảm bảo chất lượng dữ liệu và trao đổi liên thông dữ liệu giữa các hệ thống', 'description' => null, 'max_score' => 5],
                        ['name' => '2.4 Sản phẩm, giải pháp công nghệ có quy định kèm về quản trị dữ liệu trong hệ thống và tổ chức nới triển khai', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 3: Hiệu quả trong triển khai sản phẩm, giải pháp công nghệ số trong nhà trường (20 điểm)
                        ['name' => '3.1 Nâng cao hiệu quả quản trị và quản lý nhà trường/ cơ sở giáo dục', 'description' => 'Tiết kiệm chi phí; Hiệu suất công việc; Quản lý chặt chẽ', 'max_score' => 4],
                        ['name' => '3.2 Quy trình ra quyết định dựa trên dữ liệu', 'description' => 'Hệ thống báo cáo, tổng hợp; Có ứng dụng phân tích dữ liệu trong các hoạt động nhà trường', 'max_score' => 4],
                        ['name' => '3.3 Giảng dạy và học tập trên môi trường số chiếm tỷ trọng đáng kế', 'description' => 'Ứng dụng LMS/LCMS có tính năng AI trong giảng dạy và học tập; Các nền tảng, giải pháp học trực tuyến; Tài liệu, học liệu số; Có sử dụng AI trong các nội dung và hoạt động dạy và học', 'max_score' => 4],
                        ['name' => '3.4 Hoạt động KHCN và ĐMST gia tăng trên nền tảng số', 'description' => 'Số lượng bài báo; Số lượng đề tài; Số lượng sáng kiến số', 'max_score' => 4],
                        ['name' => '3.5 Dịch vụ giáo dục và đào tạo được đổi mới và mang lại giá trị hơn', 'description' => 'Gia tăng học sinh, sinh viên, người học; Gia tăng và đa dạng hóa dịch vụ đào tạo, giáo dục; Cung cấp đa dạng dịch vụ giáo dục và đào tạo trên nền tảng số', 'max_score' => 4],
                        // Tiêu chí 4: Kế hoạch, chiến lược quảng bá và phát triển thị trường cho sản phẩm hay giải pháp (20 điểm)
                        ['name' => '4.1 Đã có khảo sát nhu cầu thị trường cho sản phẩm hay giải pháp của mình', 'description' => null, 'max_score' => 10],
                        ['name' => '4.2 Chiến lược quảng bá, phát triển thị trường cho sản phẩm/giải pháp của mình, trong đó:', 'description' => 'Truyền thông trong nước; Truyền thông quốc tế; Đại lý/đại diện trong nước; Đại lý/đại diện quốc tế', 'max_score' => 5],
                        ['name' => '4.3 Khả năng liên kết, hợp tác phát triển hệ sinh thái của sản phẩm/giải pháp gắn với các mô hình hợp tác PPP, APPP.', 'description' => null, 'max_score' => 5],
                        // Tiêu chí 5: Đánh giá kết quả, chiến lược phát triển của nhà trường khi triển khai ứng dụng sản phẩm, giải pháp số (20 điểm)
                        ['name' => '5.1 Mức độ tăng trưởng tài chính nhờ ứng dụng sản phẩm, giải pháp trong năm 2024 so với 2023, trong đó:', 'description' => 'Tăng trưởng dưới 5%; Tăng trưởng từ 5-10%; Tăng trưởng trên 10%', 'max_score' => 5],
                        ['name' => '5.2 Mức độ tăng sự hài lòng của người làm việc khi đưa giải pháp, sản phẩm vào ứng dụng năm 2025 so với 2024, trong đó:', 'description' => 'Tăng trưởng dưới 5%; Tăng trưởng trên 5%', 'max_score' => 5],
                        ['name' => '5.3 Mức độ tăng trưởng thu nhập của người lao động năm 2025 so với 2024, trong đó:', 'description' => 'Tăng trưởng dưới 5; Tăng trưởng trên 5%', 'max_score' => 5],
                        ['name' => '5.4 Mục tiêu tăng trưởng 5 năm sắp tới của doanh nghiệp cung cấp giải pháp, sản phẩm có khả năng hiện thực trong đó:', 'description' => 'Có kế hoạch tăng trưởng doanh thu dưới 5%/năm; Có kế hoạch tăng trưởng doanh thu 5-10%/năm; Có kế hoạch tăng trưởng doanh thu trên 10%/năm', 'max_score' => 5],
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $id = self::ABBR . '-' . self::YEAR;

        if (Program::where('id', $id)->exists()) {
            $this->command?->warn("SEI Awards 2026 đã tồn tại ({$id}), bỏ qua.");

            return;
        }

        // Seeded earlier under the ABBR-N scheme (e.g. SEI-1): keep its data, only move it to the new ID
        $old = Program::where('abbr', self::ABBR)->where('year', self::YEAR)->pluck('id');
        if ($old->count() > 1) {
            $this->command?->error('Có nhiều chương trình SEI 2026 (' . $old->implode(', ') . '), không tự đổi mã.');

            return;
        }

        DB::transaction(fn () => $old->isEmpty() ? $this->create($id) : $this->rename($old->first(), $id));
    }

    private function create(string $id): void
    {
        $program = Program::create([
            'id'          => $id,
            'name'        => 'Biểu dương Sáng kiến Giáo dục thông minh - SEI Awards',
            'year'        => self::YEAR,
            'abbr'        => self::ABBR,
            'color'       => '#0891b2',
            'status'      => 'active',
            'deadline'    => '2026-11-05',
            'description' => 'Chương trình Biểu dương Sáng kiến Giáo dục thông minh (Smart Education Initiative Awards - SEI Awards) năm 2026.',
            'next_sub_id' => 1,
        ]);

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

                foreach ($sc['criteria'] as $ki => $crit) {
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

    /**
     * Move a program and everything that points at it to a new ID.
     * Rows are copied or repointed in an order the foreign keys accept, then the old rows are dropped.
     */
    private function rename(string $oldId, string $newId): void
    {
        // SEI-1-C1-SC1 -> SEI-2026-C1-SC1, SEI-1-cr7 -> SEI-2026-cr7
        $swap = fn (?string $id) => $id !== null && str_starts_with($id, $oldId . '-')
            ? $newId . substr($id, strlen($oldId))
            : $id;

        $program = (array) DB::table('programs')->where('id', $oldId)->first();
        DB::table('programs')->insert(['id' => $newId] + $program);

        // Top-level categories first, their sub-categories reference them
        $categories = DB::table('program_categories')->where('program_id', $oldId)->get()
            ->sortBy(fn ($cat) => $cat->parent_id !== null);
        foreach ($categories as $cat) {
            DB::table('program_categories')->insert([
                'id'         => $swap($cat->id),
                'program_id' => $newId,
                'parent_id'  => $swap($cat->parent_id),
            ] + (array) $cat);
            DB::table('submissions')->where('category_id', $cat->id)->update(['category_id' => $swap($cat->id)]);
        }

        foreach (DB::table('program_criteria')->where('program_id', $oldId)->get() as $crit) {
            DB::table('program_criteria')->where('id', $crit->id)->update([
                'id'          => $swap($crit->id),
                'program_id'  => $newId,
                'category_id' => $swap($crit->category_id),
            ]);
            DB::table('score_details')->where('criterion_id', $crit->id)->update(['criterion_id' => $swap($crit->id)]);
        }

        DB::table('submissions')->where('program_id', $oldId)->update(['program_id' => $newId]);
        DB::table('program_judges')->where('program_id', $oldId)->update(['program_id' => $newId]);

        // Only the old category rows are left under the old program
        DB::table('program_categories')->where('program_id', $oldId)->whereNotNull('parent_id')->delete();
        DB::table('program_categories')->where('program_id', $oldId)->delete();
        DB::table('programs')->where('id', $oldId)->delete();

        $this->command?->info("Đã đổi mã chương trình {$oldId} thành {$newId}, giữ nguyên dữ liệu.");
    }
}
