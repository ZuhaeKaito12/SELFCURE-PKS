<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'code' => 'identify',
                'name' => 'Identify',
                'description' => 'Understanding what business data and accounts you have, who owns cybersecurity decisions, and what you need to protect.',
                'name_ms' => 'Kenal Pasti',
                'description_ms' => 'Mengetahui data dan akaun perniagaan yang anda ada, siapa yang bertanggungjawab terhadap keselamatan siber, dan apa yang perlu dilindungi.',
                'order' => 1,
            ],
            [
                'code' => 'protect',
                'name' => 'Protect',
                'description' => 'Everyday safeguards such as passwords, updates, written guidelines, and access control that keep your business assets safe.',
                'name_ms' => 'Lindungi',
                'description_ms' => 'Langkah keselamatan harian seperti kata laluan, kemas kini perisian, panduan bertulis dan kawalan akses untuk melindungi aset perniagaan anda.',
                'order' => 2,
            ],
            [
                'code' => 'detect',
                'name' => 'Detect',
                'description' => 'Noticing suspicious activity or scams early, before they cause harm.',
                'name_ms' => 'Kesan',
                'description_ms' => 'Mengesan aktiviti mencurigakan atau penipuan lebih awal, sebelum ia menyebabkan kerugian.',
                'order' => 3,
            ],
            [
                'code' => 'respond',
                'name' => 'Respond',
                'description' => 'Knowing what to do and who to contact when something goes wrong.',
                'name_ms' => 'Tindak Balas',
                'description_ms' => 'Tahu apa yang perlu dibuat dan siapa yang perlu dihubungi apabila berlaku masalah.',
                'order' => 4,
            ],
            [
                'code' => 'recover',
                'name' => 'Recover',
                'description' => 'Being able to restore your data, keep the business running, and improve after an incident.',
                'name_ms' => 'Pulih',
                'description_ms' => 'Mampu memulihkan data, memastikan perniagaan terus berjalan dan memperbaiki amalan selepas sesuatu insiden.',
                'order' => 5,
            ],
        ];

        foreach ($domains as $domain) {
            Domain::updateOrCreate(['code' => $domain['code']], $domain);
        }
    }
}
