<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::questions() as $domainCode => $questions) {
            $domain = Domain::where('code', $domainCode)->firstOrFail();

            $domain->questions()->delete();

            foreach ($questions as $index => $question) {
                Question::create([
                    'domain_id' => $domain->id,
                    'order' => $index + 1,
                    ...$question,
                ]);
            }
        }

        // Deleting questions above cascades to their actions; rebuild them.
        $this->call(SecurityActionSeeder::class);
    }

    public static function questions(): array
    {
        return [
            'identify' => [
                [
                    'text' => 'Do you know what customer data you collect (e.g. names, phone numbers, addresses, payment details) and where it is stored?',
                    'gap_description' => 'Without a clear picture of what customer data you hold and where it lives, it is very difficult to protect it, and you cannot respond quickly if something goes wrong.',
                    'recommendation' => 'Write down a simple list of every place customer data is captured (order forms, WhatsApp, e-commerce platform) and where each is stored. Review and update this list every few months.',
                    'regulation_reference' => 'PDPA – General Principle (accountability for personal data collected)',
                    'text_ms' => 'Adakah anda tahu data pelanggan apa yang dikumpul oleh perniagaan anda (contohnya nama, nombor telefon, alamat dan maklumat pembayaran) dan di mana data itu disimpan?',
                    'gap_description_ms' => 'Jika anda tidak tahu data pelanggan apa yang ada dan di mana ia disimpan, sukar untuk melindunginya. Anda juga tidak dapat bertindak dengan cepat jika berlaku masalah.',
                    'recommendation_ms' => 'Senaraikan setiap tempat data pelanggan diterima (contohnya borang tempahan, WhatsApp atau platform e-dagang) dan di mana ia disimpan. Kemas kini senarai ini setiap beberapa bulan.',
                ],
                [
                    'text' => 'Have you heard of the Cybersecurity Act 2024 and the Personal Data Protection Act (PDPA), and do you understand how they may affect your business?',
                    'gap_description' => 'Businesses unaware of these laws risk unintentional non-compliance, which can lead to fines, reputational damage, or data breaches going unmanaged.',
                    'recommendation' => 'Read a plain-language summary of the Cybersecurity Act 2024 and the seven PDPA data protection principles, and note down which parts apply to how your business operates.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854); Personal Data Protection Act (PDPA) 2010',
                    'text_ms' => 'Adakah anda pernah mendengar tentang Akta Keselamatan Siber 2024 dan Akta Perlindungan Data Peribadi (PDPA), dan faham bagaimana kedua-dua akta ini boleh memberi kesan kepada perniagaan anda?',
                    'gap_description_ms' => 'Perniagaan yang tidak tahu tentang undang-undang ini mungkin melanggarnya tanpa sedar. Akibatnya boleh jadi denda, reputasi terjejas atau kebocoran data yang tidak diuruskan dengan betul.',
                    'recommendation_ms' => 'Baca ringkasan mudah tentang Akta Keselamatan Siber 2024 dan tujuh prinsip PDPA, kemudian catat bahagian yang berkaitan dengan operasi perniagaan anda.',
                ],
                [
                    'text' => 'Does someone in your business (you or a specific staff member) take clear ownership of cybersecurity decisions, such as approving new tools or handling incidents?',
                    'gap_description' => 'Without a clear owner, cybersecurity decisions get delayed or missed entirely because no one feels responsible for acting on them.',
                    'recommendation' => 'Nominate one person, even if it is just yourself, as the point of responsibility for cybersecurity decisions in the business.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – governance and accountability expectations',
                    'text_ms' => 'Adakah sesiapa dalam perniagaan anda (anda sendiri atau pekerja tertentu) bertanggungjawab membuat keputusan berkaitan keselamatan siber, seperti meluluskan aplikasi baharu atau menguruskan insiden?',
                    'gap_description_ms' => 'Jika tiada orang yang jelas bertanggungjawab, keputusan keselamatan siber mudah tertangguh atau terlepas kerana tiada siapa yang rasa itu tugasnya.',
                    'recommendation_ms' => 'Tetapkan seorang, walaupun anda sendiri, sebagai orang yang bertanggungjawab membuat keputusan keselamatan siber dalam perniagaan.',
                ],
            ],
            'protect' => [
                [
                    'text' => 'Do you use a different, strong password for each business account (e.g. Instagram, WhatsApp Business, online banking)?',
                    'gap_description' => 'Reusing the same password across accounts means that if one account is compromised, attackers can often access all your other accounts too.',
                    'recommendation' => 'Set a unique password for each important account. Consider using a password manager app to make this easier to keep track of.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – reasonable security measures',
                    'text_ms' => 'Adakah anda menggunakan kata laluan yang kukuh dan berbeza untuk setiap akaun perniagaan (contohnya Instagram, WhatsApp Business dan perbankan dalam talian)?',
                    'gap_description_ms' => 'Jika kata laluan yang sama digunakan untuk banyak akaun, satu akaun yang digodam boleh membuka jalan kepada akaun-akaun lain.',
                    'recommendation_ms' => 'Gunakan kata laluan yang berbeza untuk setiap akaun penting. Aplikasi pengurus kata laluan boleh memudahkan anda menyimpannya.',
                ],
                [
                    'text' => 'Do you keep the devices and apps used for your business updated with the latest security updates?',
                    'gap_description' => 'Outdated apps and operating systems often contain known security flaws that attackers can exploit.',
                    'recommendation' => 'Turn on automatic updates where possible, and check for and install updates on your phone, laptop, and business apps regularly.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – reasonable security measures',
                    'text_ms' => 'Adakah peranti dan aplikasi yang digunakan untuk perniagaan anda sentiasa dikemas kini dengan kemas kini keselamatan terkini?',
                    'gap_description_ms' => 'Aplikasi dan sistem operasi yang lama selalunya ada kelemahan keselamatan yang sudah diketahui umum dan mudah diambil kesempatan oleh penggodam.',
                    'recommendation_ms' => 'Hidupkan kemas kini automatik jika boleh, dan semak kemas kini untuk telefon, komputer riba dan aplikasi perniagaan secara berkala.',
                ],
                [
                    'text' => 'Do you limit access to sensitive business and customer information to only the people who genuinely need it?',
                    'gap_description' => 'Giving everyone full access to customer data and accounts, even when not needed, increases the chance of accidental leaks or misuse.',
                    'recommendation' => 'Only share access to customer information and business accounts with people who need it for their role, and remove access once it is no longer required.',
                    'regulation_reference' => 'PDPA – Security Principle',
                    'text_ms' => 'Adakah akses kepada maklumat sensitif perniagaan dan pelanggan hanya diberikan kepada orang yang benar-benar memerlukannya?',
                    'gap_description_ms' => 'Jika semua orang boleh mengakses data pelanggan dan akaun perniagaan walaupun tidak perlu, risiko data bocor atau disalah guna menjadi lebih tinggi.',
                    'recommendation_ms' => 'Berikan akses kepada maklumat pelanggan dan akaun perniagaan hanya kepada mereka yang memerlukannya untuk kerja, dan tarik balik akses apabila tidak lagi diperlukan.',
                ],
                [
                    'text' => 'Do you have any basic written rules or guidelines, even a simple one-page SOP, for how staff or family helpers should handle devices, passwords, and customer data?',
                    'gap_description' => 'Without written guidance, day-to-day practices depend entirely on memory and can vary from person to person, increasing risk.',
                    'recommendation' => 'Write a short one-page guide covering the basics: password rules, who can access customer data, and what to do if something looks suspicious.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – reasonable security measures; PDPA – Security Principle',
                    'text_ms' => 'Adakah anda ada peraturan atau garis panduan bertulis, walaupun SOP ringkas satu muka surat, tentang cara pekerja atau ahli keluarga yang membantu menggunakan peranti, kata laluan dan data pelanggan?',
                    'gap_description_ms' => 'Tanpa panduan bertulis, amalan harian bergantung pada ingatan dan boleh berbeza antara seorang dengan yang lain. Ini meningkatkan risiko.',
                    'recommendation_ms' => 'Sediakan panduan ringkas satu muka surat tentang perkara asas: peraturan kata laluan, siapa yang boleh mengakses data pelanggan dan apa yang perlu dibuat jika ada perkara yang mencurigakan.',
                ],
            ],
            'detect' => [
                [
                    'text' => 'Would you and anyone helping with the business be able to recognise a phishing message or fake website pretending to be a bank, courier, or supplier?',
                    'gap_description' => 'Phishing is one of the most common threats reported by Malaysian SMEs. Not being able to recognise it significantly increases the risk of falling victim.',
                    'recommendation' => 'Learn the common signs of phishing: urgent language, unexpected links, requests to confirm personal or payment details, and slightly misspelled sender names or links.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – awareness and reasonable security measures',
                    'text_ms' => 'Bolehkah anda dan sesiapa yang membantu perniagaan anda mengenal pasti mesej pancingan data (phishing) atau laman web palsu yang menyamar sebagai bank, syarikat kurier atau pembekal?',
                    'gap_description_ms' => 'Pancingan data antara ancaman yang paling kerap dilaporkan oleh PKS di Malaysia. Jika tidak dapat mengenalinya, risiko menjadi mangsa jauh lebih tinggi.',
                    'recommendation_ms' => 'Kenali tanda-tanda biasa pancingan data: bahasa yang mendesak, pautan yang tidak dijangka, permintaan untuk mengesahkan maklumat peribadi atau pembayaran, dan nama pengirim atau pautan yang ejaannya sedikit berbeza.',
                ],
                [
                    'text' => 'Do you regularly check your business accounts and bank transactions for unusual or unauthorised activity?',
                    'gap_description' => 'Without regular checks, unauthorised transactions or account changes can go unnoticed for a long time, increasing the potential loss.',
                    'recommendation' => 'Set a regular time each week to review your bank and business account activity for anything unexpected.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – monitoring and reasonable security measures',
                    'text_ms' => 'Adakah anda kerap menyemak akaun perniagaan dan transaksi bank untuk mengesan aktiviti yang luar biasa atau tanpa kebenaran?',
                    'gap_description_ms' => 'Tanpa semakan berkala, transaksi atau perubahan akaun tanpa kebenaran boleh terlepas pandang untuk tempoh yang lama, dan kerugian semakin besar.',
                    'recommendation_ms' => 'Tetapkan masa yang sama setiap minggu untuk menyemak aktiviti akaun bank dan akaun perniagaan anda.',
                ],
                [
                    'text' => 'Do you get notified (e.g. by SMS, email, or app alert) when someone logs into your business accounts from a new device?',
                    'gap_description' => 'Without login alerts, unauthorised access to your accounts may go undetected until real damage has already occurred.',
                    'recommendation' => 'Turn on login or new-device notifications in the security settings of your important business accounts (email, banking, social media).',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – monitoring and reasonable security measures',
                    'text_ms' => 'Adakah anda menerima makluman (contohnya melalui SMS, e-mel atau notifikasi aplikasi) apabila seseorang log masuk ke akaun perniagaan anda dari peranti baharu?',
                    'gap_description_ms' => 'Tanpa makluman log masuk, akses tanpa kebenaran ke akaun anda mungkin tidak disedari sehinggalah kerosakan sudah berlaku.',
                    'recommendation_ms' => 'Hidupkan notifikasi log masuk atau peranti baharu dalam tetapan keselamatan akaun perniagaan yang penting (e-mel, perbankan dan media sosial).',
                ],
                [
                    'text' => 'Do you carefully check delivery, courier, or bank messages before clicking on any links inside them?',
                    'gap_description' => 'Scam messages disguised as delivery or bank notifications are a very common way that business owners are tricked into revealing sensitive information.',
                    'recommendation' => 'Avoid clicking links in unexpected messages. Instead, go directly to the courier or bank\'s official app or website to check any claimed delivery or account issue.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – awareness and reasonable security measures',
                    'text_ms' => 'Adakah anda menyemak mesej penghantaran, kurier atau bank dengan teliti sebelum menekan sebarang pautan di dalamnya?',
                    'gap_description_ms' => 'Mesej palsu yang menyamar sebagai notis penghantaran atau bank ialah taktik biasa untuk menipu pemilik perniagaan supaya mendedahkan maklumat sensitif.',
                    'recommendation_ms' => 'Elakkan menekan pautan dalam mesej yang tidak dijangka. Sebaliknya, semak terus melalui aplikasi atau laman web rasmi syarikat kurier atau bank.',
                ],
            ],
            'respond' => [
                [
                    'text' => 'Do you have a plan for what to do if a business account or customer data is compromised (e.g. who to contact, what to change)?',
                    'gap_description' => 'Without a plan, a security incident is more likely to cause confusion, delay, and greater damage while you figure out what to do.',
                    'recommendation' => 'Write a short, simple plan: which passwords to change first, who to inform (bank, platform, customers), and who in your business should be told immediately.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – incident response obligations',
                    'text_ms' => 'Adakah anda ada pelan tentang apa yang perlu dibuat jika akaun perniagaan atau data pelanggan digodam (contohnya siapa yang perlu dihubungi dan kata laluan mana yang perlu ditukar)?',
                    'gap_description_ms' => 'Tanpa pelan, insiden keselamatan lebih mudah menyebabkan kekeliruan dan kelewatan, dan kerosakan menjadi lebih besar ketika anda masih memikirkan langkah seterusnya.',
                    'recommendation_ms' => 'Tulis pelan ringkas: kata laluan mana yang perlu ditukar dahulu, siapa yang perlu dimaklumkan (bank, platform, pelanggan) dan siapa dalam perniagaan yang perlu diberitahu dengan segera.',
                ],
                [
                    'text' => 'Do you know how to report a cybersecurity incident (e.g. to your bank, the platform provider, or the authorities)?',
                    'gap_description' => 'Not knowing how or where to report an incident can delay important actions, such as freezing a compromised account or limiting financial loss.',
                    'recommendation' => 'Save the fraud/security reporting contact details for your bank and main business platforms somewhere easy to find, before you need them.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – incident reporting obligations',
                    'text_ms' => 'Adakah anda tahu cara melaporkan insiden keselamatan siber (contohnya kepada bank, penyedia platform atau pihak berkuasa)?',
                    'gap_description_ms' => 'Jika anda tidak tahu cara atau ke mana perlu membuat laporan, tindakan penting seperti membekukan akaun yang digodam atau mengehadkan kerugian boleh tertangguh.',
                    'recommendation_ms' => 'Simpan nombor talian penipuan atau keselamatan bank dan platform perniagaan utama anda di tempat yang mudah dicari, sebelum anda memerlukannya.',
                ],
                [
                    'text' => 'If customer data were exposed, would you know how to inform the affected customers and manage the situation?',
                    'gap_description' => 'Failing to inform affected customers appropriately can lead to loss of trust and potential liability under the PDPA.',
                    'recommendation' => 'Prepare a short, honest message template in advance explaining what happened, what data was affected, and what customers should do, ready to use if ever needed.',
                    'regulation_reference' => 'PDPA – notification and accountability expectations',
                    'text_ms' => 'Jika data pelanggan terdedah, adakah anda tahu cara memaklumkan pelanggan yang terjejas dan menguruskan keadaan tersebut?',
                    'gap_description_ms' => 'Jika pelanggan yang terjejas tidak dimaklumkan dengan betul, mereka boleh hilang kepercayaan, dan perniagaan anda mungkin berdepan liabiliti di bawah PDPA.',
                    'recommendation_ms' => 'Sediakan templat mesej yang ringkas dan jujur lebih awal, yang menerangkan apa yang berlaku, data apa yang terjejas dan apa yang perlu dibuat oleh pelanggan.',
                ],
            ],
            'recover' => [
                [
                    'text' => 'Do you keep backup copies of important business and customer data (e.g. order records, contact lists)?',
                    'gap_description' => 'Without backups, losing a device or having files corrupted or held for ransom could mean permanently losing critical business records.',
                    'recommendation' => 'Set up a simple backup routine, such as regularly saving order and customer records to cloud storage (e.g. Google Drive) in addition to your device.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – resilience and recovery expectations',
                    'text_ms' => 'Adakah anda menyimpan salinan sandaran (backup) bagi data perniagaan dan pelanggan yang penting (contohnya rekod tempahan dan senarai kenalan)?',
                    'gap_description_ms' => 'Tanpa sandaran, peranti yang hilang, fail yang rosak atau serangan perisian tebusan (ransomware) boleh menyebabkan rekod penting perniagaan hilang terus.',
                    'recommendation_ms' => 'Mulakan rutin sandaran yang mudah, contohnya simpan rekod tempahan dan pelanggan ke storan awan (seperti Google Drive) secara berkala, selain di dalam peranti anda.',
                ],
                [
                    'text' => 'Are your backups stored somewhere separate from your main device, and have you actually tested that you can restore from them?',
                    'gap_description' => 'Backups stored only on the same device offer no protection if that device is lost, and a backup that has never been tested may turn out to be unusable exactly when you need it most.',
                    'recommendation' => 'Store at least one backup copy on cloud storage or an external drive kept elsewhere, and try restoring a small sample of it at least once to confirm it actually works.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – resilience and recovery expectations',
                    'text_ms' => 'Adakah sandaran anda disimpan di tempat yang berasingan daripada peranti utama, dan pernahkah anda cuba memulihkan data daripadanya?',
                    'gap_description_ms' => 'Sandaran yang disimpan dalam peranti yang sama tidak membantu jika peranti itu hilang. Sandaran yang tidak pernah diuji pula mungkin tidak berfungsi tepat pada masa anda memerlukannya.',
                    'recommendation_ms' => 'Simpan sekurang-kurangnya satu salinan sandaran di storan awan atau pemacu luaran yang disimpan di tempat lain, dan cuba pulihkan beberapa fail sekurang-kurangnya sekali untuk memastikan ia berfungsi.',
                ],
                [
                    'text' => 'After a suspicious event or near-miss, do you review what happened and adjust your practices to prevent it from happening again?',
                    'gap_description' => 'Without reviewing incidents, near-misses, or your practices periodically, the same mistakes and weaknesses tend to repeat as the business and threats evolve.',
                    'recommendation' => 'After any suspicious event, take a few minutes to note what happened and what you will change. Also set a recurring reminder, such as every six months, to redo this self-assessment.',
                    'regulation_reference' => 'Cybersecurity Act 2024 (Act 854) – continuous improvement expectations',
                    'text_ms' => 'Selepas berlaku kejadian yang mencurigakan atau hampir terkena, adakah anda menyemak semula apa yang berlaku dan mengubah amalan supaya ia tidak berulang?',
                    'gap_description_ms' => 'Jika kejadian dan amalan tidak disemak semula, kesilapan dan kelemahan yang sama cenderung berulang apabila perniagaan dan ancaman siber berubah.',
                    'recommendation_ms' => 'Selepas sebarang kejadian yang mencurigakan, luangkan beberapa minit untuk mencatat apa yang berlaku dan apa yang akan anda ubah. Tetapkan juga peringatan, contohnya setiap enam bulan, untuk membuat penilaian ini semula.',
                ],
            ],
        ];
    }
}
