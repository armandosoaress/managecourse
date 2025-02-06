<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Store;
use App\Models\UserStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Utility;
use App\Models\BlogSocial;
use App\Models\StoreThemeSettings;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $arrPermissions =
        [
            [
                'name' => 'manage dashboard',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage store analytics',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage order',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete order',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'show order',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'export order',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage user',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create user',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit user',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete user',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'reset password user',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage role',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create role',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit role',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete role',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage course',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create course',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit course',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete course',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'export course',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create content',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit content',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete content',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create chapter',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit chapter',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete chapter',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'upload practice file',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit practice file',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete practice file',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create faq',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit faq',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete faq',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage category',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create category',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit category',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete category',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage subcategory',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create subcategory',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit subcategory',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete subcategory',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage custom page',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create custom page',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit custom page',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete custom page',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage blog',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create blog',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit blog',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete blog',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'social media blog',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage subscriber',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create subscriber',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete subscriber',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'show course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'import course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'export course coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage student',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'show student',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'export student',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage zoom meeting',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create zoom meeting',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete zoom meeting',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'show coupon',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage plan',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create plan',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit plan',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage plan request',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage store settings',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage settings',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage language',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create language',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete language',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage store',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'create store',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'edit store',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete store',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'change store',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'upgrade plan',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'reset password',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'manage student logs',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'show student logs',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'delete student logs',
                'guard_name' => 'web',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        Permission::insert($arrPermissions);


         // Super admin

         $superAdminRole        = Role::create(
            [
                'name' => 'super admin',
                'created_by' => 0,
            ]
        );
        $superAdminPermissions = [
            ['name' => 'manage dashboard'],
            ['name' => 'manage language'],
            ['name' => 'create language'],
            ['name' => 'delete language'],
            ['name' => 'manage store'],
            ['name' => 'create store'],
            ['name' => 'edit store'],
            ['name' => 'delete store'],
            ['name' => 'upgrade plan'],
            ['name' => 'reset password'],
            ['name' => 'manage coupon'],
            ['name' => 'create coupon'],
            ['name' => 'edit coupon'],
            ['name' => 'delete coupon'],
            ['name' => 'show coupon'],
            ['name' => 'manage plan'],
            ['name' => 'create plan'],
            ['name' => 'edit plan'],
            ['name' => 'manage plan request'],
            ['name' => 'export order'],
            ['name' => 'manage settings'],
        ];
        $superAdminRole->givePermissionTo($superAdminPermissions);

        $superAdmin = User::create(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('1234'),
                'type' => 'super admin',
                'lang' => 'en',
                'avatar' => '',
                'created_by' => 0,
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $companyRole        = Role::create(
            [
                'name' => 'Owner',
                'created_by' => $superAdmin->id,
            ]
        );
        $companyPermissions = [
            ['name' => 'manage dashboard'],
            ['name' => 'manage store analytics'],
            ['name' => 'create store'],
            ['name' => 'delete store'],
            ['name' => 'manage order'],
            ['name' => 'show order'],
            ['name' => 'delete order'],
            ['name' => 'export order'],
            ['name' => 'manage user'],
            ['name' => 'create user'],
            ['name' => 'edit user'],
            ['name' => 'delete user'],
            ['name' => 'reset password user'],
            ['name' => 'manage role'],
            ['name' => 'create role'],
            ['name' => 'edit role'],
            ['name' => 'delete role'],
            ['name' => 'manage course'],
            ['name' => 'create course'],
            ['name' => 'edit course'],
            ['name' => 'delete course'],
            ['name' => 'export course'],
            ['name' => 'create content'],
            ['name' => 'edit content'],
            ['name' => 'delete content'],
            ['name' => 'create chapter'],
            ['name' => 'edit chapter'],
            ['name' => 'delete chapter'],
            ['name' => 'upload practice file'],
            ['name' => 'edit practice file'],
            ['name' => 'delete practice file'],
            ['name' => 'create faq'],
            ['name' => 'edit faq'],
            ['name' => 'delete faq'],
            ['name' => 'manage category'],
            ['name' => 'create category'],
            ['name' => 'edit category'],
            ['name' => 'delete category'],
            ['name' => 'manage subcategory'],
            ['name' => 'create subcategory'],
            ['name' => 'edit subcategory'],
            ['name' => 'delete subcategory'],
            ['name' => 'manage custom page'],
            ['name' => 'create custom page'],
            ['name' => 'edit custom page'],
            ['name' => 'delete custom page'],
            ['name' => 'manage blog'],
            ['name' => 'create blog'],
            ['name' => 'edit blog'],
            ['name' => 'delete blog'],
            ['name' => 'social media blog'],
            ['name' => 'manage subscriber'],
            ['name' => 'create subscriber'],
            ['name' => 'delete subscriber'],
            ['name' => 'manage course coupon'],
            ['name' => 'create course coupon'],
            ['name' => 'edit course coupon'],
            ['name' => 'delete course coupon'],
            ['name' => 'show course coupon'],
            ['name' => 'import course coupon'],
            ['name' => 'export course coupon'],
            ['name' => 'manage student'],
            ['name' => 'show student'],
            ['name' => 'export student'],
            ['name' => 'manage plan'],
            ['name' => 'manage zoom meeting'],
            ['name' => 'create zoom meeting'],
            ['name' => 'delete zoom meeting'],
            ['name' => 'change store'],
            ['name' => 'manage store settings'],
            ['name' => 'manage student logs'],
            ['name' => 'show student logs'],
            ['name' => 'delete student logs'],
        ];

        $companyRole->givePermissionTo($companyPermissions);

        $admin = User::create(
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'password' => Hash::make('1234'),
                'type' => 'Owner',
                'lang' => 'en',
                'created_by' => $superAdmin->id,
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]
        );
        $admin->assignRole($companyRole);

        $objStore = Store::create(
            [
                'name' => 'My Store',
                'email' => 'owner@example.com',
                'enable_storelink' => 'on',
                'store_theme' => 'yellow-style.css',
                'theme_dir' => 'theme1',
                'whatsapp' => '#',
                'facebook' => '#',
                'instagram' => '#',
                'twitter' => '#',
                'youtube' => '#',
                'footer_note' => '© 2020 LMSGo. All rights reserved',
                'enable_header_img' => 'on',
                'header_img' => 'img-15.jpg',
                'header_title' => 'Home Accessories',
                'header_desc' => 'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.',
                'button_text' => 'Start shopping',
                'enable_rating' => 'on',
                'enable_subscriber' => 'on',
                'subscriber_title' => 'Always on time',
                'sub_title' => 'Subscription here',
                'logo' => 'logo.png',
                'created_by' => $admin->id,
                'header_name' => 'Course Certificate',
                'certificate_template' => 'template1',
                'certificate_color' => 'b10d0d',
                'certificate_gradiant' => 'color-one',
            ]
        );


        // author
        $authorRole = Role::create(
            [
                'name' => 'author',
                'store_id' => $objStore->id,
                'created_by' => $admin->id,
            ]
        );

        $authorPermission = [
            ['name' => 'manage dashboard'],
            ['name' => 'manage store analytics'],
            ['name' => 'create store'],
            ['name' => 'manage order'],
            ['name' => 'show order'],
            ['name' => 'delete order'],
            ['name' => 'export order'],
            ['name' => 'manage user'],
            ['name' => 'manage course'],
            ['name' => 'create course'],
            ['name' => 'edit course'],
            ['name' => 'delete course'],
            ['name' => 'export course'],
            ['name' => 'create content'],
            ['name' => 'edit content'],
            ['name' => 'delete content'],
            ['name' => 'create chapter'],
            ['name' => 'edit chapter'],
            ['name' => 'delete chapter'],
            ['name' => 'upload practice file'],
            ['name' => 'edit practice file'],
            ['name' => 'delete practice file'],
            ['name' => 'create faq'],
            ['name' => 'edit faq'],
            ['name' => 'delete faq'],
            ['name' => 'manage category'],
            ['name' => 'create category'],
            ['name' => 'edit category'],
            ['name' => 'delete category'],
            ['name' => 'manage subcategory'],
            ['name' => 'create subcategory'],
            ['name' => 'edit subcategory'],
            ['name' => 'delete subcategory'],
            ['name' => 'manage custom page'],
            ['name' => 'create custom page'],
            ['name' => 'edit custom page'],
            ['name' => 'delete custom page'],
            ['name' => 'manage blog'],
            ['name' => 'create blog'],
            ['name' => 'edit blog'],
            ['name' => 'delete blog'],
            ['name' => 'social media blog'],
            ['name' => 'manage subscriber'],
            ['name' => 'create subscriber'],
            ['name' => 'delete subscriber'],
            ['name' => 'manage course coupon'],
            ['name' => 'create course coupon'],
            ['name' => 'edit course coupon'],
            ['name' => 'delete course coupon'],
            ['name' => 'show course coupon'],
            ['name' => 'import course coupon'],
            ['name' => 'export course coupon'],
            ['name' => 'manage student'],
            ['name' => 'show student'],
            ['name' => 'export student'],
            ['name' => 'change store'],
            ['name' => 'manage student logs'],
            ['name' => 'show student logs'],
            ['name' => 'delete student logs'],
        ];

        $authorRole->givePermissionTo($authorPermission);

        $author = User::create(
            [
                'name' => 'Author',
                'email' => 'author@example.com',
                'password' => Hash::make('1234'),
                'type' => 'author',
                'lang' => 'en',
                'avatar' => '',
                'current_store'=> $objStore->id,
                'created_by' => $admin->id,
                'email_verified_at' => date('Y-m-d H:i:s'),
            ]
        );
        $author->assignRole($authorRole);

        $student = \App\Models\Student::create(
            [
                'name' => 'Student',
                'email' => 'student@example.com',
                'password' => Hash::make('1234'),
                'phone_number' => '9876543210',
                'store_id' => $objStore->id,
                'avatar' => 'avatar.png',
                'lang' => 'en',
            ]
        );

        $admin->current_store = $objStore->id;
        $admin->save();

        UserStore::create(
            [
                'user_id' => $admin->id,
                'store_id' => $objStore->id,
                'permission' => 'Owner',
            ]
        );
        \App\Models\BlogSocial::create(
            [
                'enable_social_button' => 'on',
                'enable_email' => 'on',
                'enable_twitter' => 'on',
                'enable_facebook' => 'on',
                'enable_googleplus' => 'on',
                'enable_linkedIn' => 'on',
                'enable_pinterest' => 'on',
                'enable_stumbleupon' => 'on',
                'enable_whatsapp' => 'on',
                'store_id' => $objStore->id,
                'created_by' => $admin->id,
            ]
        );
        Utility::languagecreate();
        // Utility::add_landing_page_data();
    }
}
