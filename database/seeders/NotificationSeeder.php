<?php

namespace Database\Seeders;

use App\Models\NotificationTemplateLangs;
use App\Models\NotificationTemplates;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications = [
            'new_course'=>'New Course',
            'new_order'=>'New Order',
            'new_zoom_meeting'=>'New Zoom Meeting'
        ];

        $defaultTemplate = [
            'new_course' => [
                'variables' => '{
                    "Course Title": "course_title",
                    "Store Name": "store_name",
                    "Company Name": "company_name"
                }',
                'lang' => [

                    'ar' => 'تم إنشاء دورة تدريبية جديدة {course_title} بواسطة {store_name}.',
                    'da' => 'Nyt kursus {course_title} er oprettet af {store_name}.',
                    'de' => 'Neuer Kurs {course_title} wird erstellt von {store_name}.',
                    'en' => 'New Course {course_title} is created by {store_name}.',
                    'es' => 'El nuevo curso {course_title} fue creado por {store_name}.',
                    'fr' => 'Le nouveau cours {course_title} est créé par {store_name}.',
                    'it' => 'Il nuovo corso {course_title} è stato creato da {store_name}.',
                    'ja' => '新しいコース {course_title} が {store_name} によって作成されました。',
                    'nl' => 'Nieuwe cursus {course_title} is gemaakt door {store_name}.',
                    'pl' => 'Nowy kurs {course_title} jest tworzony przez {store_name}.',
                    'ru' => 'Новый курс {course_title} создан {store_name}.',
                    'pt' => 'O novo curso {course_title} foi criado por {store_name}.',
                    'tr' => 'Yeni Kurs { course_title }, { store_name } tarafından oluşturuldu.',
                    'pt-br' => 'Novo curso {course_title} é criado por {store_name}.',
                    'zh' => '新课程 {Course_title } 由 {store_name} 创建。',
                    'he' => 'קורס חדש {rese_title} נוצר על-ידי {store_name}.',
                ]
            ],

            'new_order' => [
                'variables' => '{
                    "Order Id": "order_id",
                    "Store Name": "store_name",
                    "Company Name": "company_name"
                }',
                'lang' => [
                    'ar' => 'تم إنشاء الطلب الجديد {order_id} بواسطة {store_name}.',
                    'da' => 'Ny ordre {order_id} er oprettet af {store_name}.',
                    'de' => 'Neue Bestellung {order_id} wird von {store_name} erstellt.',
                    'en' => 'New Order {order_id} is created by {store_name}.',
                    'es' => 'El nuevo pedido {order_id} es creado por {store_name}.',
                    'fr' => 'La nouvelle commande {order_id} est créée par {store_name}.',
                    'it' => 'Il nuovo ordine {order_id} è stato creato da {store_name}.',
                    'ja' => '新しい注文 {order_id} が {store_name} によって作成されました。',
                    'nl' => 'Nieuwe bestelling {order_id} is gemaakt door {store_name}.',
                    'pl' => 'Nowe zamówienie {order_id} jest tworzone przez {store_name}.',
                    'ru' => 'Новый заказ {order_id} создан магазином {store_name}.',
                    'pt' => 'O novo pedido {order_id} foi criado por {store_name}.',
                    'tr' => 'Yeni Sipariş { order_id }, { store_name } tarafından oluşturuldu.',
                    'pt-br' => 'New Order {order_id} é criado por {store_name}.',
                    'zh' => '新建订单 {order_id} 由 {store_name} 创建。',
                    'he' => 'סדר חדש {order_id} נוצר על-ידי {store_name}.',
                ]
            ],

            'new_zoom_meeting' => [
                'variables' => '{
                    "Title": "title",
                    "Store Name": "store_name",
                    "Company Name": "company_name"
                }',
                'lang' => [

                    'ar' => 'تم إنشاء اجتماع Zoom الجديد {title} بواسطة {store_name}.',
                    'da' => 'Nyt Zoom-møde {title} er oprettet af {store_name}.',
                    'de' => 'Das neue Zoom-Meeting {title} wird von {store_name} erstellt.',
                    'en' => 'New Zoom Meeting {title} is created by {store_name}.',
                    'es' => 'La nueva reunión de Zoom {title} es creada por {store_name}.',
                    'fr' => 'La nouvelle réunion Zoom {title} est créée par {store_name}.',
                    'it' => 'Il nuovo Zoom Meeting {title} è stato creato da {store_name}.',
                    'ja' => '新しい Zoom ミーティング {title} が {store_name} によって作成されました。',
                    'nl' => 'Nieuwe Zoom Meeting {title} is gemaakt door {store_name}.',
                    'pl' => 'Nowe spotkanie Zoom {title} jest tworzone przez {store_name}.',
                    'ru' => 'Новое собрание Zoom {название} создано {store_name}.',
                    'pt' => 'A nova Zoom Meeting {title} foi criada por {store_name}.',
                    'tr' => '{ store_name } tarafından yeni Zoom Toplantısı { title } oluşturuldu.',
                    'pt-br' => 'Nova reunião do Zoom {title} é criada por {store_name}.',
                    'zh' => '新缩放会议 {title } 由 {store_name} 创建。',
                    'he' => 'פגישת זום חדשה {title} נוצרת על-ידי {store_name}.',
                ]
            ],

        ];


        $user = User::where('type','super admin')->first();

        foreach($notifications as $k => $n)
        {
            $ntfy = NotificationTemplates::where('slug',$k)->count();
            if($ntfy == 0)
            {
                $new = new NotificationTemplates();
                $new->name = $n;
                $new->slug = $k;
                $new->save();

                foreach($defaultTemplate[$k]['lang'] as $lang => $content)
                {
                    NotificationTemplateLangs::create(
                        [
                            'parent_id' => $new->id,
                            'lang' => $lang,
                            'variables' => $defaultTemplate[$k]['variables'],
                            'content' => $content,
                            'created_by' => !empty($user) ? $user->id : 1,
                        ]
                    );
                }
            }
        }
    }
}
