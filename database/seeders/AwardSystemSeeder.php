<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\ProgramCriterion;
use App\Models\Score;
use App\Models\ScoreDetail;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class AwardSystemSeeder extends Seeder
{
    // Fixed UUIDs so submitter references stay stable across re-seeds
    private const USERS = [
        'admin'    => 'a0000000-0000-0000-0000-000000000001',
        'judge1'   => 'a0000000-0000-0000-0000-000000000002',
        'judge2'   => 'a0000000-0000-0000-0000-000000000003',
        'judge3'   => 'a0000000-0000-0000-0000-000000000004',
        'judge4'   => 'a0000000-0000-0000-0000-000000000005',
        'judge5'   => 'a0000000-0000-0000-0000-000000000006',
        'judge6'   => 'a0000000-0000-0000-0000-000000000010',
        'judge7'   => 'a0000000-0000-0000-0000-000000000011',
        'judge8'   => 'a0000000-0000-0000-0000-000000000012',
        'judge9'   => 'a0000000-0000-0000-0000-000000000013',
        'judge10'  => 'a0000000-0000-0000-0000-000000000014',
        'judge11'  => 'a0000000-0000-0000-0000-000000000015',
        'judge12'  => 'a0000000-0000-0000-0000-000000000016',
        'judge13'  => 'a0000000-0000-0000-0000-000000000017',
        'judge14'  => 'a0000000-0000-0000-0000-000000000018',
        'judge15'  => 'a0000000-0000-0000-0000-000000000019',
        'judge16'  => 'a0000000-0000-0000-0000-000000000020',
        'submit1'  => 'a0000000-0000-0000-0000-000000000007',
        'submit2'  => 'a0000000-0000-0000-0000-000000000008',
        'submit3'  => 'a0000000-0000-0000-0000-000000000009',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::transaction(function () {
            $this->seedUsers();
            $this->seedJudges();
            $this->seedPrograms();
        });
        Schema::enableForeignKeyConstraints();
    }

    /* ────────────────────────── USERS ─────────────────────────── */
    private function seedUsers(): void
    {
        $u = self::USERS;
        $users = [
            ['id' => $u['admin'],   'name' => 'Nguyễn Thị Lan',         'email' => 'admin@sk.vn',   'password' => Hash::make('admin'),  'role' => 'admin',     'title' => 'Quản trị viên',   'email_verified_at' => now()],
            ['id' => $u['judge1'],  'name' => 'PGS.TS. Nguyễn Văn An',  'email' => 'judge1@sk.vn',  'password' => Hash::make('judge'),  'role' => 'judge',     'title' => 'Giám khảo',       'email_verified_at' => now()],
            ['id' => $u['judge2'],  'name' => 'TS. Trần Thị Bình',       'email' => 'judge2@sk.vn',  'password' => Hash::make('judge'),  'role' => 'judge',     'title' => 'Giám khảo',       'email_verified_at' => now()],
            ['id' => $u['judge3'],  'name' => 'PGS.TS. Lê Hoàng Cường', 'email' => 'judge3@sk.vn',  'password' => Hash::make('judge'),  'role' => 'judge',     'title' => 'Giám khảo',       'email_verified_at' => now()],
            ['id' => $u['judge4'],  'name' => 'TS. Phạm Minh Đức',       'email' => 'judge4@sk.vn',  'password' => Hash::make('judge'),  'role' => 'judge',     'title' => 'Giám khảo',       'email_verified_at' => now()],
            ['id' => $u['judge5'],  'name' => 'PGS.TS. Vũ Thị Hà',      'email' => 'judge5@sk.vn',  'password' => Hash::make('judge'),  'role' => 'judge',     'title' => 'Giám khảo',       'email_verified_at' => now()],
            ['id' => $u['judge6'],  'name' => 'TS. Nguyễn Quân',        'email' => 'judgequannguyen@sk.vn',     'password' => Hash::make('quannguyen6'), 'role' => 'judge', 'title' => 'Chủ tịch Hội Tự động hóa Việt Nam, Chủ tịch Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge7'],  'name' => 'TS. Nguyễn Minh Hồng',   'email' => 'judgehongnguyen@sk.vn',      'password' => Hash::make('hongnguyen7'), 'role' => 'judge', 'title' => 'Chủ tịch Hội Truyền thông số, Phó Chủ tịch Thường trực Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge8'],  'name' => 'PGS.TS. Hoàng Hữu Hạnh', 'email' => 'judgehanhhoang@sk.vn',       'password' => Hash::make('hanhhoang8'), 'role' => 'judge', 'title' => 'Phó Cục trưởng Cục Chuyển đổi số quốc gia, Bộ Khoa học và Công nghệ, Phó Chủ tịch Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge9'],  'name' => 'TS. Hồ Đức Thắng',       'email' => 'judgethangho@sk.vn',         'password' => Hash::make('thangho9'), 'role' => 'judge', 'title' => 'Đại biểu Quốc hội hoạt động chuyên trách tại Ủy ban Văn hóa và Xã hội của Quốc hội, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge10'], 'name' => 'ThS. Đỗ Công Anh',       'email' => 'judgeanhdo@sk.vn',           'password' => Hash::make('anhdo10'), 'role' => 'judge', 'title' => 'Phó Cục trưởng Cục Chuyển đổi số - Cơ yếu, Văn phòng Trung ương Đảng, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge11'], 'name' => 'TS. Lê Nam Trung',       'email' => 'judgetrungle@sk.vn',         'password' => Hash::make('trungle11'), 'role' => 'judge', 'title' => 'Chánh Văn phòng Viện Hàn lâm Khoa học Xã hội Việt Nam, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge12'], 'name' => 'PGS.TS. Trần Quang Diệu','email' => 'judgedieutran@sk.vn',        'password' => Hash::make('dieutran12'), 'role' => 'judge', 'title' => 'Giám đốc Trung tâm Công nghệ và Chuyển đổi số, Học viện Chính trị Quốc gia Hồ Chí Minh, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge13'], 'name' => 'TS. Trần Nam Tú',        'email' => 'judgetutran@sk.vn',          'password' => Hash::make('tutran13'), 'role' => 'judge', 'title' => 'Phó Cục trưởng Cục Khoa học, Công nghệ và Thông tin, Bộ Giáo dục và Đào tạo, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge14'], 'name' => 'Ông Nguyễn Trường Nam',  'email' => 'judgenamnguyen@sk.vn',       'password' => Hash::make('namnguyen14'), 'role' => 'judge', 'title' => 'Phó Giám đốc Trung tâm Thông tin, Bộ Y tế, Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge15'], 'name' => 'Ông Phạm Đình Vũ',       'email' => 'judgevupham@sk.vn',          'password' => Hash::make('vupham15'), 'role' => 'judge', 'title' => 'Chánh Văn phòng Liên đoàn Thương mại và Công nghiệp Việt Nam (VCCI), Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['judge16'], 'name' => 'Bà Trần Tuệ Tri',        'email' => 'judgetritran@sk.vn',         'password' => Hash::make('tritran16'), 'role' => 'judge', 'title' => 'Chủ tịch Hội đồng Cố vấn Mạng lưới Rồng Xanh Toàn cầu - Global Blue Dragon Fellowship (GBDF), Thành viên Hội đồng', 'email_verified_at' => now()],
            ['id' => $u['submit1'], 'name' => 'Trần Văn Hùng',           'email' => 'submit@sk.vn',  'password' => Hash::make('submit'), 'role' => 'submitter', 'title' => 'Đại diện đơn vị', 'email_verified_at' => now()],
            ['id' => $u['submit2'], 'name' => 'Lê Thị Mai',              'email' => 'submit2@sk.vn', 'password' => Hash::make('submit'), 'role' => 'submitter', 'title' => 'Đại diện đơn vị', 'email_verified_at' => now()],
            ['id' => $u['submit3'], 'name' => 'Phạm Quốc Bảo',           'email' => 'submit3@sk.vn', 'password' => Hash::make('submit'), 'role' => 'submitter', 'title' => 'Đại diện đơn vị', 'email_verified_at' => now()],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['id' => $data['id']], $data);
        }
    }

    /* ────────────────────────── JUDGES ────────────────────────── */
    private function seedJudges(): void
    {
        $u = self::USERS;
        $judges = [
            ['id' => 'J1', 'name' => 'PGS.TS. Nguyễn Văn An',  'email' => 'judge1@sk.vn', 'specialty' => 'Giải pháp CNTT',   'judge_group' => 'Nhóm 1', 'user_id' => $u['judge1']],
            ['id' => 'J2', 'name' => 'TS. Trần Thị Bình',       'email' => 'judge2@sk.vn', 'specialty' => 'AI & ML',          'judge_group' => 'Nhóm 1', 'user_id' => $u['judge2']],
            ['id' => 'J3', 'name' => 'PGS.TS. Lê Hoàng Cường', 'email' => 'judge3@sk.vn', 'specialty' => 'Nền tảng số',      'judge_group' => 'Nhóm 2', 'user_id' => $u['judge3']],
            ['id' => 'J4', 'name' => 'TS. Phạm Minh Đức',       'email' => 'judge4@sk.vn', 'specialty' => 'An ninh mạng',     'judge_group' => 'Nhóm 2', 'user_id' => $u['judge4']],
            ['id' => 'J5', 'name' => 'PGS.TS. Vũ Thị Hà',      'email' => 'judge5@sk.vn', 'specialty' => 'IoT & Smart City', 'judge_group' => 'Nhóm 3', 'user_id' => $u['judge5']],
            ['id' => 'J6', 'name' => 'TS. Nguyễn Quân',        'email' => 'judgequannguyen@sk.vn', 'specialty' => 'Giải pháp CNTT',   'judge_group' => 'Nhóm 1', 'user_id' => $u['judge6']],
            ['id' => 'J7', 'name' => 'TS. Nguyễn Minh Hồng',   'email' => 'judgehongnguyen@sk.vn', 'specialty' => 'AI & ML',          'judge_group' => 'Nhóm 1', 'user_id' => $u['judge7']],
            ['id' => 'J8', 'name' => 'PGS.TS. Hoàng Hữu Hạnh', 'email' => 'judgehanhhoang@sk.vn', 'specialty' => 'Nền tảng số',      'judge_group' => 'Nhóm 2', 'user_id' => $u['judge8']],
            ['id' => 'J9', 'name' => 'TS. Hồ Đức Thắng',       'email' => 'judgethangho@sk.vn', 'specialty' => 'An ninh mạng',     'judge_group' => 'Nhóm 2', 'user_id' => $u['judge9']],
            ['id' => 'J10', 'name' => 'ThS. Đỗ Công Anh',       'email' => 'judgeanhdo@sk.vn', 'specialty' => 'IoT & Smart City', 'judge_group' => 'Nhóm 3', 'user_id' => $u['judge10']],
            ['id' => 'J11', 'name' => 'TS. Lê Nam Trung',       'email' => 'judgetrungle@sk.vn', 'specialty' => 'Giải pháp CNTT',   'judge_group' => 'Nhóm 1', 'user_id' => $u['judge11']],
            ['id' => 'J12', 'name' => 'PGS.TS. Trần Quang Diệu','email' => 'judgedieutran@sk.vn', 'specialty' => 'AI & ML',          'judge_group' => 'Nhóm 1', 'user_id' => $u['judge12']],
            ['id' => 'J13', 'name' => 'TS. Trần Nam Tú',        'email' => 'judgetutran@sk.vn', 'specialty' => 'Nền tảng số',      'judge_group' => 'Nhóm 2', 'user_id' => $u['judge13']],
            ['id' => 'J14', 'name' => 'Ông Nguyễn Trường Nam',  'email' => 'judgenamnguyen@sk.vn', 'specialty' => 'An ninh mạng',     'judge_group' => 'Nhóm 2', 'user_id' => $u['judge14']],
            ['id' => 'J15', 'name' => 'Ông Phạm Đình Vũ',       'email' => 'judgevupham@sk.vn', 'specialty' => 'IoT & Smart City', 'judge_group' => 'Nhóm 3', 'user_id' => $u['judge15']],
            ['id' => 'J16', 'name' => 'Bà Trần Tuệ Tri',        'email' => 'judgetritran@sk.vn', 'specialty' => 'Giải pháp CNTT',   'judge_group' => 'Nhóm 1', 'user_id' => $u['judge16']],
        ];

        foreach ($judges as $j) {
            Judge::updateOrCreate(['id' => $j['id']], $j);
        }
    }

    /* ────────────────────────── PROGRAMS ──────────────────────── */
    private function seedPrograms(): void
    {
        $this->seedP1SaoKhue2026();
        $this->seedP2SaoKhue2025();
        $this->seedP3Sea2026();
    }

    /* ─── P1: Sao Khuê 2026 (active) ─── */
    private function seedP1SaoKhue2026(): void
    {
        $p = Program::updateOrCreate(['id' => 'SK-1'], [
            'name'        => 'Giải thưởng Sao Khuê',
            'year'        => 2026,
            'abbr'        => 'SK',
            'color'       => '#1e3a5f',
            'status'      => 'active',
            'deadline'    => '2026-06-30',
            'description' => 'Giải thưởng thường niên dành cho các sản phẩm, dịch vụ CNTT xuất sắc của Việt Nam.',
            'next_sub_id' => 8,
        ]);

        $categories = [
            ['id' => 'SK-1-C1', 'name' => 'Giải pháp phần mềm',    'color' => '#3b82f6', 'sort_order' => 0],
            ['id' => 'SK-1-C2', 'name' => 'Nền tảng số',            'color' => '#8b5cf6', 'sort_order' => 1],
            ['id' => 'SK-1-C3', 'name' => 'Dịch vụ CNTT',           'color' => '#10b981', 'sort_order' => 2],
            ['id' => 'SK-1-C4', 'name' => 'Sản phẩm công nghệ',     'color' => '#f59e0b', 'sort_order' => 3],
            ['id' => 'SK-1-C5', 'name' => 'Giải pháp AI/ML',        'color' => '#ef4444', 'sort_order' => 4],
        ];
        $criteria = [
            ['id' => 'SK-1-cr1', 'name' => 'Tính sáng tạo & Đổi mới',  'description' => 'Mức độ sáng tạo, tính mới',        'max_score' => 20, 'sort_order' => 0],
            ['id' => 'SK-1-cr2', 'name' => 'Chất lượng kỹ thuật',       'description' => 'Kiến trúc, công nghệ, hiệu năng',  'max_score' => 20, 'sort_order' => 1],
            ['id' => 'SK-1-cr3', 'name' => 'Hiệu quả kinh tế - xã hội', 'description' => 'Tác động tích cực',                'max_score' => 20, 'sort_order' => 2],
            ['id' => 'SK-1-cr4', 'name' => 'Khả năng ứng dụng thực tế', 'description' => 'Mức độ triển khai',                'max_score' => 20, 'sort_order' => 3],
            ['id' => 'SK-1-cr5', 'name' => 'Năng lực cạnh tranh',       'description' => 'Cạnh tranh trong nước & quốc tế',  'max_score' => 20, 'sort_order' => 4],
        ];

        $this->upsertCats($p->id, $categories);
        $this->upsertCriteria($p->id, $criteria);

        // Program judges: J1..J5
        $p->judges()->sync(['J1', 'J2', 'J3', 'J4', 'J5']);

        $u = self::USERS;
        $subs = [
            ['id' => 'SK26-001', 'name' => 'VinCloud - Nền tảng đám mây',      'company' => 'Công ty CP VinTech',        'submitter_id' => $u['submit1'], 'category_id' => 'SK-1-C2', 'description' => 'Nền tảng điện toán đám mây toàn diện.', 'submitted_date' => '2026-01-15', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Tài liệu kỹ thuật', 'Video demo'], 'judges' => ['J1','J2','J3']],
            ['id' => 'SK26-002', 'name' => 'SmartEdu Pro - Quản lý đào tạo',   'company' => 'Công ty TNHH Giáo dục TM', 'submitter_id' => $u['submit1'], 'category_id' => 'SK-1-C1', 'description' => 'Hệ thống đào tạo trực tuyến tích hợp AI.', 'submitted_date' => '2026-01-18', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Tài liệu kỹ thuật'], 'judges' => ['J1','J2']],
            ['id' => 'SK26-003', 'name' => 'MediCare AI - Y tế thông minh',    'company' => 'Công ty CP Y tế Số VN',    'submitter_id' => $u['submit2'], 'category_id' => 'SK-1-C5', 'description' => 'AI hỗ trợ chẩn đoán bệnh lý qua hình ảnh.', 'submitted_date' => '2026-01-20', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Tài liệu kỹ thuật', 'Video demo'], 'judges' => ['J2','J5']],
            ['id' => 'SK26-004', 'name' => 'FinSecure - Bảo mật tài chính',    'company' => 'Công ty CP CyberVN',       'submitter_id' => $u['submit2'], 'category_id' => 'SK-1-C1', 'description' => 'Bảo mật toàn diện cho ngân hàng.', 'submitted_date' => '2026-02-01', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Tài liệu kỹ thuật'], 'judges' => ['J1','J4']],
            ['id' => 'SK26-005', 'name' => 'AgriSmart - Nông nghiệp 4.0',      'company' => 'Công ty AgriTech VN',      'submitter_id' => $u['submit3'], 'category_id' => 'SK-1-C4', 'description' => 'IoT cho nông nghiệp thông minh.', 'submitted_date' => '2026-02-05', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Video demo'], 'judges' => ['J3','J5']],
            ['id' => 'SK26-006', 'name' => 'GovPortal - Dịch vụ công',         'company' => 'Tập đoàn FPT',             'submitter_id' => $u['submit1'], 'category_id' => 'SK-1-C3', 'description' => 'Cổng dịch vụ công trực tuyến.', 'submitted_date' => '2026-02-10', 'status' => 'pending',  'docs' => ['Hồ sơ đăng ký', 'Tài liệu kỹ thuật'], 'judges' => []],
            ['id' => 'SK26-007', 'name' => 'ChatBot VN - Trợ lý ảo',           'company' => 'Công ty CP AI VN',         'submitter_id' => $u['submit1'], 'category_id' => 'SK-1-C5', 'description' => 'Chatbot AI xử lý tiếng Việt.', 'submitted_date' => '2026-02-18', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Demo'], 'judges' => ['J2','J4','J5']],
        ];

        foreach ($subs as $s) {
            $this->upsertSubmission($p->id, $s);
        }

        // Scores
        $this->upsertScore('SK26-001', 'J1', ['SK-1-cr1'=>17,'SK-1-cr2'=>16,'SK-1-cr3'=>18,'SK-1-cr4'=>19,'SK-1-cr5'=>15], 'Rất tiềm năng.', true);
        $this->upsertScore('SK26-001', 'J2', ['SK-1-cr1'=>18,'SK-1-cr2'=>17,'SK-1-cr3'=>16,'SK-1-cr4'=>18,'SK-1-cr5'=>16], 'Kỹ thuật tốt.', true);
        $this->upsertScore('SK26-002', 'J1', ['SK-1-cr1'=>15,'SK-1-cr2'=>14,'SK-1-cr3'=>16,'SK-1-cr4'=>17,'SK-1-cr5'=>13], 'Ứng dụng cao.', true);
        $this->upsertScore('SK26-007', 'J2', ['SK-1-cr1'=>19,'SK-1-cr2'=>18,'SK-1-cr3'=>17,'SK-1-cr4'=>16,'SK-1-cr5'=>17], 'NLP xuất sắc.', true);
    }

    /* ─── P2: Sao Khuê 2025 (completed) ─── */
    private function seedP2SaoKhue2025(): void
    {
        $p = Program::updateOrCreate(['id' => 'SK-2'], [
            'name'        => 'Giải thưởng Sao Khuê',
            'year'        => 2025,
            'abbr'        => 'SK',
            'color'       => '#7c3aed',
            'status'      => 'completed',
            'deadline'    => '2025-06-30',
            'description' => 'Giải thưởng Sao Khuê năm 2025 - Đã hoàn thành.',
            'next_sub_id' => 3,
        ]);

        $categories = [
            ['id' => 'SK-2-C1', 'name' => 'Giải pháp phần mềm', 'color' => '#3b82f6', 'sort_order' => 0],
            ['id' => 'SK-2-C2', 'name' => 'Nền tảng số',         'color' => '#8b5cf6', 'sort_order' => 1],
            ['id' => 'SK-2-C3', 'name' => 'Dịch vụ CNTT',        'color' => '#10b981', 'sort_order' => 2],
        ];
        $criteria = [
            ['id' => 'SK-2-cr1', 'name' => 'Sáng tạo',   'description' => 'Tính sáng tạo',       'max_score' => 25, 'sort_order' => 0],
            ['id' => 'SK-2-cr2', 'name' => 'Kỹ thuật',   'description' => 'Chất lượng kỹ thuật', 'max_score' => 25, 'sort_order' => 1],
            ['id' => 'SK-2-cr3', 'name' => 'Ứng dụng',   'description' => 'Khả năng ứng dụng',   'max_score' => 25, 'sort_order' => 2],
            ['id' => 'SK-2-cr4', 'name' => 'Cạnh tranh', 'description' => 'Năng lực cạnh tranh', 'max_score' => 25, 'sort_order' => 3],
        ];

        $this->upsertCats($p->id, $categories);
        $this->upsertCriteria($p->id, $criteria);
        $p->judges()->sync(['J1', 'J3']);

        $u = self::USERS;
        $subs = [
            ['id' => 'SK25-001', 'name' => 'DataVN - Phân tích dữ liệu lớn', 'company' => 'Công ty DataVN',   'submitter_id' => $u['submit1'], 'category_id' => 'SK-2-C1', 'description' => 'Nền tảng phân tích big data.', 'submitted_date' => '2025-01-10', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký'], 'judges' => ['J1','J3']],
            ['id' => 'SK25-002', 'name' => 'CloudPay - Thanh toán số',       'company' => 'Công ty CloudPay', 'submitter_id' => $u['submit2'], 'category_id' => 'SK-2-C2', 'description' => 'Ví điện tử thế hệ mới.',   'submitted_date' => '2025-01-15', 'status' => 'approved', 'docs' => ['Hồ sơ đăng ký', 'Demo'], 'judges' => ['J1','J3']],
        ];

        foreach ($subs as $s) {
            $this->upsertSubmission($p->id, $s);
        }

        $this->upsertScore('SK25-001', 'J1', ['SK-2-cr1'=>22,'SK-2-cr2'=>21,'SK-2-cr3'=>23,'SK-2-cr4'=>20], 'Xuất sắc.', true);
        $this->upsertScore('SK25-001', 'J3', ['SK-2-cr1'=>20,'SK-2-cr2'=>22,'SK-2-cr3'=>21,'SK-2-cr4'=>19], 'Rất tốt.', true);
        $this->upsertScore('SK25-002', 'J1', ['SK-2-cr1'=>18,'SK-2-cr2'=>19,'SK-2-cr3'=>20,'SK-2-cr4'=>17], 'Tốt.', true);
        $this->upsertScore('SK25-002', 'J3', ['SK-2-cr1'=>19,'SK-2-cr2'=>20,'SK-2-cr3'=>18,'SK-2-cr4'=>18], 'Khá tốt.', true);
    }

    /* ─── P3: SEA IT Awards 2026 (active) ─── */
    private function seedP3Sea2026(): void
    {
        $p = Program::updateOrCreate(['id' => 'SEA-1'], [
            'name'        => 'Giải thưởng CNTT Đông Nam Á',
            'year'        => 2026,
            'abbr'        => 'SEA',
            'color'       => '#dc2626',
            'status'      => 'active',
            'deadline'    => '2026-09-30',
            'description' => 'Giải thưởng quốc tế dành cho sản phẩm CNTT khu vực Đông Nam Á.',
            'next_sub_id' => 3,
        ]);

        $categories = [
            ['id' => 'SEA-1-C1', 'name' => 'Best Innovation',  'color' => '#ef4444', 'sort_order' => 0],
            ['id' => 'SEA-1-C2', 'name' => 'Best Platform',    'color' => '#f59e0b', 'sort_order' => 1],
            ['id' => 'SEA-1-C3', 'name' => 'Best AI Solution', 'color' => '#8b5cf6', 'sort_order' => 2],
        ];
        $criteria = [
            ['id' => 'SEA-1-cr1', 'name' => 'Innovation',  'description' => 'Level of innovation',           'max_score' => 30, 'sort_order' => 0],
            ['id' => 'SEA-1-cr2', 'name' => 'Impact',      'description' => 'Social & economic impact',      'max_score' => 30, 'sort_order' => 1],
            ['id' => 'SEA-1-cr3', 'name' => 'Scalability', 'description' => 'Scalability & sustainability',  'max_score' => 40, 'sort_order' => 2],
        ];

        $this->upsertCats($p->id, $categories);
        $this->upsertCriteria($p->id, $criteria);
        $p->judges()->sync(['J1', 'J2', 'J5']);

        $u = self::USERS;
        $subs = [
            ['id' => 'SEA26-001', 'name' => 'GrabTech Analytics', 'company' => 'Grab Holdings', 'submitter_id' => $u['submit3'], 'category_id' => 'SEA-1-C1', 'description' => 'AI-powered logistics optimization for Southeast Asia.', 'submitted_date' => '2026-03-01', 'status' => 'approved', 'docs' => ['Application', 'Technical docs'], 'judges' => ['J1','J2','J5']],
            ['id' => 'SEA26-002', 'name' => 'TokopediaCloud',     'company' => 'Tokopedia',     'submitter_id' => $u['submit3'], 'category_id' => 'SEA-1-C2', 'description' => 'E-commerce cloud platform for SMEs.', 'submitted_date' => '2026-03-05', 'status' => 'pending',  'docs' => ['Application', 'Demo'], 'judges' => []],
        ];

        foreach ($subs as $s) {
            $this->upsertSubmission($p->id, $s);
        }

        $this->upsertScore('SEA26-001', 'J1', ['SEA-1-cr1'=>25,'SEA-1-cr2'=>27,'SEA-1-cr3'=>35], 'Outstanding.', true);
    }

    /* ────────────────── HELPERS ────────────────── */
    private function upsertCats(string $programId, array $cats): void
    {
        ProgramCategory::where('program_id', $programId)->delete();
        foreach ($cats as $c) {
            ProgramCategory::create(array_merge($c, ['program_id' => $programId]));
        }
    }

    private function upsertCriteria(string $programId, array $crits): void
    {
        ProgramCriterion::where('program_id', $programId)->delete();
        foreach ($crits as $c) {
            ProgramCriterion::create(array_merge($c, ['program_id' => $programId]));
        }
    }

    private function upsertSubmission(string $programId, array $data): void
    {
        $sub = Submission::updateOrCreate(['id' => $data['id']], [
            'program_id'     => $programId,
            'name'           => $data['name'],
            'company'        => $data['company'],
            'submitter_id'   => $data['submitter_id'],
            'category_id'    => $data['category_id'],
            'description'    => $data['description'] ?? null,
            'submitted_date' => $data['submitted_date'],
            'status'         => $data['status'],
        ]);

        if ($data['judges']) {
            $sub->assignedJudges()->sync($data['judges']);
        }
    }

    private function upsertScore(string $submissionId, string $judgeId, array $scores, string $comment, bool $submitted): void
    {
        $score = Score::updateOrCreate(
            ['submission_id' => $submissionId, 'judge_id' => $judgeId],
            ['comment' => $comment, 'is_submitted' => $submitted]
        );

        foreach ($scores as $critId => $value) {
            ScoreDetail::updateOrCreate(
                ['score_id' => $score->id, 'criterion_id' => $critId],
                ['value'    => $value]
            );
        }
    }
}
