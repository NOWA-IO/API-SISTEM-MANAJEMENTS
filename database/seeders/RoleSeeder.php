<?php
namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Helpers\EnumHelper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\String\b;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = EnumHelper::toArray(UserRoleEnum::class);

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
            ]);

            $userEmail = strtolower(str_replace(' ', '_', $role)) . '@gmail.com';
            $user      = User::firstOrCreate(
                ['email' => $userEmail],
                [
                    'name'     => $role,
                    'password' => bcrypt('password')]
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
