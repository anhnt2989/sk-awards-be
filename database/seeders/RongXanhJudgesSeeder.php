<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Hội đồng Giám khảo Giải thưởng Việt Nam Rồng Xanh 2026.
 * Source: "DS HỘI ĐỒNG GIÁM KHẢO GIẢI THƯỞNG VIET NAM RỒNG XANH-H3.docx", minus the six names marked red and underlined
 * (Hoàng Hữu Hạnh, Hồ Đức Thắng, Lê Nam Trung, Trần Quang Diệu, Trần Nam Tú, Trần Tuệ Tri: they already have
 * accounts J8, J9, J11, J12, J13, J16 from AwardSystemSeeder), plus TS. Lê Thu Hà.
 *
 * Creates the accounts and, when RXA-2026 exists (RongXanhAwards2026Seeder), adds them to that program.
 * Safe to re-run: an email that already has an account is left untouched.
 *
 * php artisan db:seed --class=RongXanhJudgesSeeder
 */
class RongXanhJudgesSeeder extends Seeder
{
    private const PROGRAM_ID = 'RXA-2026';

    // Same convention as AdditionalJudgesSeeder: judge{tên}{họ}@sk.vn / {tên}{họ}{số thứ tự}, continuing after 19
    private const JUDGES = [
        ['name' => 'TS. Nguyễn Hoàng Giang',          'email' => 'judgegiangnguyen@sk.vn',  'password' => 'giangnguyen20',  'title' => 'Phó Cục trưởng Cục Sở hữu trí tuệ, Bộ Khoa học và Công nghệ'],
        ['name' => 'TS. Nguyễn Thị Hoàng Lan',        'email' => 'judgelannguyen@sk.vn',    'password' => 'lannguyen21',    'title' => 'Cục trưởng Cục Thông tin điện tử và Chuyển đổi số, Bộ Văn hóa, Thể thao và Du lịch'],
        ['name' => 'ThS. Kiều Công Thược',            'email' => 'judgethuockieu@sk.vn',    'password' => 'thuockieu22',    'title' => 'Phó Chủ tịch Hiệp hội Phát triển Công nghiệp Văn hóa Việt Nam'],
        ['name' => 'PGS.TS. Nguyễn Thị Thu Phương',   'email' => 'judgephuongnguyen@sk.vn', 'password' => 'phuongnguyen23', 'title' => 'Viện trưởng Viện Văn hoá, Nghệ thuật, Thể thao và Du lịch Việt Nam'],
        ['name' => 'TS. NSND. Bạch Tuyết',            'email' => 'judgetuyetbach@sk.vn',    'password' => 'tuyetbach24',    'title' => 'Nghệ sĩ Nhân dân'],
        ['name' => 'Bà Lê Cẩm Tế',                    'email' => 'judgetele@sk.vn',         'password' => 'tele25',         'title' => 'Nhà sáng lập Không gian Lưu niệm Lê Bá Đảng (LeBaDang Memory Space)'],
        ['name' => 'Ông Bùi Công Khánh',              'email' => 'judgekhanhbui@sk.vn',     'password' => 'khanhbui26',     'title' => 'Nghệ sĩ Thị giác và Đa phương tiện'],
        ['name' => 'Đạo diễn Ngô Thanh Vân',          'email' => 'judgevanngo@sk.vn',       'password' => 'vanngo27',       'title' => 'Đạo diễn'],
        ['name' => 'Nghệ sĩ thị giác Tùng Monkey',    'email' => 'judgetungmonkey@sk.vn',   'password' => 'tungmonkey28',   'title' => 'Nghệ sĩ thị giác'],
        ['name' => 'Ông Nguyễn Quang Trí',            'email' => 'judgetringuyen@sk.vn',    'password' => 'tringuyen29',    'title' => 'Giám đốc điều hành Khối Marketing, Công ty Cổ phần Sữa Việt Nam (Vinamilk)'],
        ['name' => 'Ông Nguyễn Hòa Chung',            'email' => 'judgechungnguyen@sk.vn',  'password' => 'chungnguyen30',  'title' => 'Giám đốc điều hành Khối Đầu tư Tư nhân, Công ty Cổ phần Chứng khoán Thiên Việt (TVS)'],
        ['name' => 'Ông Huy Nguyễn',                  'email' => 'judgehuynguyen@sk.vn',    'password' => 'huynguyen31',    'title' => 'Trưởng Ban Công nghệ, Hiệp hội Dữ liệu quốc gia (NDA)'],
        ['name' => 'Ông Đỗ Đăng Thường',              'email' => 'judgethuongdo@sk.vn',     'password' => 'thuongdo32',     'title' => 'Giám đốc Điều hành Sáng tạo, The Purpose Group'],
        ['name' => 'TS. Lê Thu Hà',                   'email' => 'judgehale@sk.vn',         'password' => 'hale33',         'title' => 'Phó Chủ nhiệm Văn phòng Quốc hội'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $judgeIds = [];

            foreach (self::JUDGES as $data) {
                // Never reset an existing account - the judge may have changed the password
                if (User::where('email', $data['email'])->exists() || Judge::where('email', $data['email'])->exists()) {
                    $this->command?->warn("{$data['email']} đã tồn tại, bỏ qua.");

                    if ($id = Judge::where('email', $data['email'])->value('id')) {
                        $judgeIds[] = $id;
                    }

                    continue;
                }

                $user = User::create([
                    'name'              => $data['name'],
                    'email'             => $data['email'],
                    'password'          => Hash::make($data['password']),
                    'role'              => 'judge',
                    'title'             => $data['title'],
                    'email_verified_at' => now(),
                ]);

                // Next free J number rather than a fixed one, in case the real DB has more judges than the seeders
                $next = Judge::pluck('id')->map(fn ($id) => (int) substr($id, 1))->max() + 1;

                // Empty strings, not null: the admin UI prints these two fields as text
                $judge = Judge::create([
                    'id'          => 'J' . $next,
                    'name'        => $data['name'],
                    'email'       => $data['email'],
                    'specialty'   => '',
                    'judge_group' => '',
                    'user_id'     => $user->id,
                ]);
                $judgeIds[] = $judge->id;

                $this->command?->info("Đã tạo giám khảo {$judge->id}: {$data['name']} ({$data['email']}).");
            }

            $program = Program::find(self::PROGRAM_ID);
            if ($program === null) {
                $this->command?->warn('Chưa có chương trình ' . self::PROGRAM_ID . ', chưa gán giám khảo. Chạy RongXanhAwards2026Seeder rồi chạy lại seeder này.');

                return;
            }

            $program->judges()->syncWithoutDetaching($judgeIds);
            $this->command?->info('Đã gán ' . count($judgeIds) . ' giám khảo vào ' . self::PROGRAM_ID . '.');
        });
    }
}
