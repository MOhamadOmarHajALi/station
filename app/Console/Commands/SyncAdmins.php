<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncAdmins extends Command
{
    protected $signature = 'station:admins {--reset : إعادة تعيين كلمات مرور الحسابات من ADMIN_PASSWORD}';
    protected $description = 'إنشاء حسابات المدراء (حتى 3) من المتغير ADMIN_EMAILS';

    public function handle(): int
    {
        $emails = config('station.admin_emails');
        $password = config('station.admin_password');

        if (! $emails) {
            $this->error('ضع الإيميلات في المتغير ADMIN_EMAILS مفصولة بفاصلة.');
            return self::FAILURE;
        }
        if (count($emails) > 3) {
            $this->error('المسموح 3 إيميلات فقط، والموجود '.count($emails).'.');
            return self::FAILURE;
        }

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                if (! $password) {
                    $this->error("لا توجد كلمة مرور لإنشاء {$email}: ضع ADMIN_PASSWORD.");
                    continue;
                }
                User::forceCreate([
                    'name' => Str::before($email, '@'),
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);
                $this->info("تم إنشاء {$email}");
            } elseif ($this->option('reset') && $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $this->info("تمت إعادة تعيين كلمة مرور {$email}");
            } else {
                $this->line("موجود: {$email}");
            }
        }

        return self::SUCCESS;
    }
}
