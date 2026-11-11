<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Judge accounts added after AwardSystemSeeder (which stops at J16).
 * Safe to re-run: an email that already has an account is left untouched.
 *
 * php artisan db:seed --class=AdditionalJudgesSeeder
 */
class AdditionalJudgesSeeder extends Seeder
{
    // Same convention as the judges in AwardSystemSeeder: judge{tên}{họ}@sk.vn / {tên}{họ}{số thứ tự}
    private const JUDGES = [
        ['name' => 'PGS.TS. Phạm Ngọc Linh', 'email' => 'judgelinhpham@sk.vn', 'password' => 'linhpham17', 'title' => 'Phó chủ tịch liên hiệp các hội Khoa học và Kỹ thuật Việt Nam'],
        ['name' => 'PGS.TS. Trần Thành Nam', 'email' => 'judgenamtran@sk.vn',  'password' => 'namtran18',  'title' => 'Hiệu trưởng Trường Đại học Giáo dục, Đại học Quốc gia Hà Nội'],
        ['name' => 'TS. Đặng Vũ Cảnh Linh',  'email' => 'judgelinhdang@sk.vn', 'password' => 'linhdang19', 'title' => 'Phó viện trưởng Viện Đào tạo bồi dưỡng cán bộ và Nghiên cứu khoa học, Ủy ban Trung ương Mặt trận Tổ quốc Việt Nam'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            foreach (self::JUDGES as $data) {
                // Never reset an existing account - the judge may have changed the password
                if (User::where('email', $data['email'])->exists() || Judge::where('email', $data['email'])->exists()) {
                    $this->command?->warn("{$data['email']} đã tồn tại, bỏ qua.");

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

                $this->command?->info("Đã tạo giám khảo {$judge->id}: {$data['name']} ({$data['email']}).");
            }
        });
    }
}
