<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\SecurityAction;
use Illuminate\Database\Seeder;

class SecurityActionSeeder extends Seeder
{
    /**
     * One concrete, doable action per assessment question, keyed by domain
     * code and the question's position within that domain. The question
     * asks about current practice; the action says what to do about a gap.
     */
    public function run(): void
    {
        foreach (self::actions() as $domainCode => $actions) {
            $domain = Domain::where('code', $domainCode)->with('questions')->first();

            if (! $domain) {
                continue;
            }

            foreach ($actions as $index => $action) {
                $question = $domain->questions->firstWhere('order', $index + 1);

                if (! $question) {
                    continue;
                }

                SecurityAction::updateOrCreate(['question_id' => $question->id], $action);
            }
        }
    }

    public static function actions(): array
    {
        return [
            'identify' => [
                [
                    'title' => 'Map Where Your Customer Data Is Kept',
                    'title_ms' => 'Kenal Pasti Data Pelanggan dan Lokasi Simpanannya',
                    'topic' => 'Knowing your customer data',
                    'topic_ms' => 'Pengurusan data pelanggan',
                    'finding' => 'You may not have a clear picture of what customer data your business collects or where it is stored.',
                    'finding_ms' => 'Anda mungkin belum ada gambaran yang jelas tentang data pelanggan yang dikumpul atau di mana ia disimpan.',
                    'description' => 'Create a simple record of the customer data you collect and every place it is stored, so you know exactly what needs protecting.',
                    'description_ms' => 'Senaraikan data pelanggan yang anda kumpul dan setiap tempat ia disimpan, supaya anda tahu apa yang perlu dilindungi.',
                    'steps' => [
                        'List the types of customer data you collect (names, phone numbers, addresses, payment details).',
                        'Note every place that data is captured, such as order forms, WhatsApp or your online store.',
                        'Write down where each type of data is stored (phone, laptop, cloud, paper).',
                        'Delete data you no longer need.',
                        'Review and update the list every few months.',
                    ],
                    'steps_ms' => [
                        'Senaraikan jenis data pelanggan yang dikumpul (nama, nombor telefon, alamat dan maklumat pembayaran).',
                        'Catat semua tempat data itu diterima, contohnya borang tempahan, WhatsApp atau kedai dalam talian.',
                        'Tulis di mana setiap jenis data disimpan (telefon, komputer riba, storan awan atau kertas).',
                        'Padam data yang tidak lagi diperlukan.',
                        'Semak dan kemas kini senarai ini setiap beberapa bulan.',
                    ],
                ],
                [
                    'title' => 'Learn Your Obligations Under PDPA and the Cybersecurity Act',
                    'title_ms' => 'Fahami Keperluan PDPA dan Akta Keselamatan Siber',
                    'topic' => 'Awareness of cybersecurity laws',
                    'topic_ms' => 'Kefahaman undang-undang siber',
                    'finding' => 'Your business may not be fully aware of how the PDPA and the Cybersecurity Act 2024 apply to it.',
                    'finding_ms' => 'Perniagaan anda mungkin belum faham sepenuhnya bagaimana PDPA dan Akta Keselamatan Siber 2024 terpakai kepadanya.',
                    'description' => 'Get a basic understanding of the two main laws and note down which requirements apply to how your business works.',
                    'description_ms' => 'Dapatkan kefahaman asas tentang kedua-dua akta ini dan catat keperluan yang berkaitan dengan perniagaan anda.',
                    'steps' => [
                        'Read a plain-language summary of the seven PDPA data protection principles.',
                        'Read a short overview of the Cybersecurity Act 2024.',
                        'Note which requirements apply to the customer data you handle.',
                        'Write down one or two changes your business should make to comply.',
                        'Keep these notes somewhere you and your staff can find them.',
                    ],
                    'steps_ms' => [
                        'Baca ringkasan mudah tentang tujuh prinsip perlindungan data PDPA.',
                        'Baca gambaran ringkas tentang Akta Keselamatan Siber 2024.',
                        'Kenal pasti keperluan yang berkaitan dengan data pelanggan yang anda uruskan.',
                        'Catat satu atau dua perubahan yang perlu dibuat supaya perniagaan anda patuh.',
                        'Simpan nota ini di tempat yang mudah dicari oleh anda dan pekerja.',
                    ],
                ],
                [
                    'title' => 'Appoint a Person Responsible for Cybersecurity',
                    'title_ms' => 'Tetapkan Orang yang Bertanggungjawab Terhadap Keselamatan Siber',
                    'topic' => 'Cybersecurity ownership',
                    'topic_ms' => 'Tanggungjawab keselamatan siber',
                    'finding' => 'No one in the business has clear responsibility for cybersecurity decisions.',
                    'finding_ms' => 'Belum ada sesiapa yang jelas bertanggungjawab membuat keputusan keselamatan siber dalam perniagaan.',
                    'description' => 'Name one person, even yourself, who makes cybersecurity decisions and handles incidents.',
                    'description_ms' => 'Tetapkan seorang, walaupun anda sendiri, untuk membuat keputusan keselamatan siber dan menguruskan insiden.',
                    'steps' => [
                        'Choose one person to own cybersecurity decisions (it can be you).',
                        'Write down what they are responsible for, such as approving new apps and handling incidents.',
                        'Tell staff or family helpers who this person is.',
                        'Agree how often this person reviews the business\'s security, for example monthly.',
                    ],
                    'steps_ms' => [
                        'Pilih seorang untuk bertanggungjawab terhadap keselamatan siber (boleh jadi anda sendiri).',
                        'Tulis tugasnya, contohnya meluluskan aplikasi baharu dan menguruskan insiden.',
                        'Beritahu pekerja atau ahli keluarga yang membantu siapa orang tersebut.',
                        'Tetapkan kekerapan semakan keselamatan, contohnya sebulan sekali.',
                    ],
                ],
            ],
            'protect' => [
                [
                    'title' => 'Review and Strengthen Business Account Passwords',
                    'title_ms' => 'Semak dan Kukuhkan Kata Laluan Akaun Perniagaan',
                    'topic' => 'Password management',
                    'topic_ms' => 'Pengurusan kata laluan',
                    'finding' => 'Some business accounts may share passwords or use weak ones, so one breach could expose several accounts.',
                    'finding_ms' => 'Ada akaun perniagaan yang mungkin menggunakan kata laluan yang sama atau lemah. Jika satu akaun digodam, akaun lain juga boleh terjejas.',
                    'description' => 'Give every important business account its own strong, unique password.',
                    'description_ms' => 'Pastikan setiap akaun perniagaan yang penting ada kata laluan sendiri yang kukuh dan berbeza.',
                    'steps' => [
                        'Identify your important business accounts.',
                        'Check whether any passwords are reused or easy to guess.',
                        'Replace weak or reused passwords.',
                        'Use a strong, unique password for each account.',
                        'Consider using a password manager to keep track of them.',
                    ],
                    'steps_ms' => [
                        'Kenal pasti akaun perniagaan yang penting.',
                        'Semak sama ada ada kata laluan yang digunakan semula atau mudah diteka.',
                        'Tukar kata laluan yang lemah atau digunakan semula.',
                        'Gunakan kata laluan yang kukuh dan berbeza untuk setiap akaun.',
                        'Pertimbangkan aplikasi pengurus kata laluan untuk menyimpannya.',
                    ],
                ],
                [
                    'title' => 'Update Business Devices and Applications',
                    'title_ms' => 'Kemas Kini Peranti dan Aplikasi Perniagaan',
                    'topic' => 'Software updates',
                    'topic_ms' => 'Kemas kini perisian',
                    'finding' => 'Devices or apps used for the business may be missing security updates, leaving known weaknesses open.',
                    'finding_ms' => 'Peranti atau aplikasi perniagaan mungkin belum menerima kemas kini keselamatan terkini, jadi kelemahan yang sudah diketahui masih terbuka.',
                    'description' => 'Bring every business device and app up to date and keep them updated automatically.',
                    'description_ms' => 'Kemas kini semua peranti dan aplikasi perniagaan, dan hidupkan kemas kini automatik.',
                    'steps' => [
                        'Identify the devices and applications used for business.',
                        'Check for pending security updates.',
                        'Install the available updates.',
                        'Turn on automatic updates where possible.',
                        'Set a routine to check for updates, for example monthly.',
                    ],
                    'steps_ms' => [
                        'Senaraikan peranti dan aplikasi yang digunakan untuk perniagaan.',
                        'Semak sama ada ada kemas kini keselamatan yang belum dipasang.',
                        'Pasang kemas kini yang tersedia.',
                        'Hidupkan kemas kini automatik jika boleh.',
                        'Tetapkan rutin untuk menyemak kemas kini, contohnya sebulan sekali.',
                    ],
                ],
                [
                    'title' => 'Review and Restrict Access to Sensitive Information',
                    'title_ms' => 'Semak dan Hadkan Akses kepada Maklumat Sensitif',
                    'topic' => 'Access control',
                    'topic_ms' => 'Kawalan akses',
                    'finding' => 'Sensitive information may be accessible to people who do not need it for their work.',
                    'finding_ms' => 'Maklumat sensitif mungkin boleh diakses oleh orang yang tidak memerlukannya untuk kerja mereka.',
                    'description' => 'Review who can access sensitive business and customer information and remove access that is not required.',
                    'description_ms' => 'Semak siapa yang ada akses kepada maklumat sensitif perniagaan dan pelanggan, dan tarik balik akses yang tidak diperlukan.',
                    'steps' => [
                        'Identify your sensitive business and customer information.',
                        'List everyone who currently has access to it.',
                        'Decide who genuinely needs access for their work.',
                        'Remove access that is not needed.',
                        'Review access regularly, and whenever someone leaves.',
                    ],
                    'steps_ms' => [
                        'Kenal pasti maklumat sensitif perniagaan dan pelanggan.',
                        'Senaraikan semua orang yang ada akses kepadanya sekarang.',
                        'Tentukan siapa yang benar-benar perlukan akses untuk kerja mereka.',
                        'Tarik balik akses yang tidak diperlukan.',
                        'Semak akses secara berkala, terutamanya apabila ada pekerja yang berhenti.',
                    ],
                ],
                [
                    'title' => 'Create a Basic Cybersecurity SOP for Staff',
                    'title_ms' => 'Sediakan SOP Keselamatan Siber Asas untuk Pekerja',
                    'topic' => 'Written security rules',
                    'topic_ms' => 'Peraturan keselamatan bertulis',
                    'finding' => 'There are no written rules for how staff should handle devices, passwords and customer data.',
                    'finding_ms' => 'Belum ada peraturan bertulis tentang cara pekerja menggunakan peranti, kata laluan dan data pelanggan.',
                    'description' => 'Write a simple one-page guide so everyone follows the same safe practices.',
                    'description_ms' => 'Sediakan panduan ringkas satu muka surat supaya semua orang mengikut amalan selamat yang sama.',
                    'steps' => [
                        'Define basic password rules.',
                        'Define how customer data should be handled.',
                        'Define rules for using business devices.',
                        'Explain what staff should do if they suspect a security incident.',
                        'Share the SOP with staff or family helpers.',
                    ],
                    'steps_ms' => [
                        'Tetapkan peraturan asas kata laluan.',
                        'Tetapkan cara mengendalikan data pelanggan.',
                        'Tetapkan peraturan penggunaan peranti perniagaan.',
                        'Terangkan apa yang perlu dibuat jika pekerja mengesyaki insiden keselamatan.',
                        'Kongsi SOP ini dengan pekerja atau ahli keluarga yang membantu.',
                    ],
                ],
            ],
            'detect' => [
                [
                    'title' => 'Learn to Spot Phishing Messages',
                    'title_ms' => 'Belajar Mengenal Mesej Pancingan Data (Phishing)',
                    'topic' => 'Phishing awareness',
                    'topic_ms' => 'Kesedaran pancingan data',
                    'finding' => 'You or your helpers may not reliably recognise phishing messages or fake websites.',
                    'finding_ms' => 'Anda atau pembantu anda mungkin belum dapat mengenal pasti mesej pancingan data atau laman web palsu dengan yakin.',
                    'description' => 'Learn the common warning signs of phishing and make sure everyone helping the business knows them too.',
                    'description_ms' => 'Kenali tanda-tanda biasa pancingan data dan pastikan semua yang membantu perniagaan juga tahu.',
                    'steps' => [
                        'Learn the common signs: urgent language, unexpected links and requests for passwords or payment details.',
                        'Check sender names and website addresses for small misspellings.',
                        'Share these warning signs with staff or family helpers.',
                        'Agree to verify suspicious requests by calling the sender on a known number.',
                        'Refresh this knowledge every few months with recent scam examples.',
                    ],
                    'steps_ms' => [
                        'Kenali tanda-tanda biasa: bahasa yang mendesak, pautan yang tidak dijangka dan permintaan kata laluan atau maklumat pembayaran.',
                        'Semak nama pengirim dan alamat laman web untuk ejaan yang sedikit berbeza.',
                        'Kongsi tanda-tanda ini dengan pekerja atau ahli keluarga yang membantu.',
                        'Sahkan permintaan yang mencurigakan dengan menelefon pengirim melalui nombor yang anda kenali.',
                        'Ulang kaji tanda-tanda ini setiap beberapa bulan dengan contoh penipuan terkini.',
                    ],
                ],
                [
                    'title' => 'Set Up a Weekly Account Activity Check',
                    'title_ms' => 'Semak Aktiviti Akaun Setiap Minggu',
                    'topic' => 'Monitoring accounts and transactions',
                    'topic_ms' => 'Pemantauan akaun dan transaksi',
                    'finding' => 'Unusual or unauthorised activity in business accounts may go unnoticed for a long time.',
                    'finding_ms' => 'Aktiviti luar biasa atau tanpa kebenaran dalam akaun perniagaan mungkin tidak disedari untuk tempoh yang lama.',
                    'description' => 'Make reviewing your bank and business account activity a short weekly routine.',
                    'description_ms' => 'Jadikan semakan akaun bank dan akaun perniagaan sebagai rutin mingguan yang ringkas.',
                    'steps' => [
                        'List the bank and business accounts to check.',
                        'Pick a fixed time each week for the check.',
                        'Review recent transactions and account changes for anything unexpected.',
                        'Report anything suspicious to your bank or platform straight away.',
                        'Keep a simple note of each check.',
                    ],
                    'steps_ms' => [
                        'Senaraikan akaun bank dan akaun perniagaan yang perlu disemak.',
                        'Pilih masa yang sama setiap minggu untuk membuat semakan.',
                        'Semak transaksi dan perubahan akaun terkini untuk sebarang perkara yang luar biasa.',
                        'Laporkan perkara yang mencurigakan kepada bank atau platform dengan segera.',
                        'Catat secara ringkas setiap semakan yang dibuat.',
                    ],
                ],
                [
                    'title' => 'Turn On Login Alerts for Business Accounts',
                    'title_ms' => 'Hidupkan Makluman Log Masuk untuk Akaun Perniagaan',
                    'topic' => 'Login alerts',
                    'topic_ms' => 'Makluman log masuk',
                    'finding' => 'Logins from unknown devices may not trigger any alert, so account takeovers could go undetected.',
                    'finding_ms' => 'Log masuk dari peranti yang tidak dikenali mungkin tidak menghasilkan sebarang makluman, jadi akaun yang diambil alih mungkin tidak disedari.',
                    'description' => 'Enable new-device and login notifications on your most important business accounts.',
                    'description_ms' => 'Hidupkan notifikasi log masuk dan peranti baharu untuk akaun perniagaan yang paling penting.',
                    'steps' => [
                        'List your most important accounts (email, banking, social media, online store).',
                        'Open each account\'s security settings.',
                        'Turn on login or new-device notifications.',
                        'Turn on two-step verification (MFA) where it is offered.',
                        'Make sure alerts go to a phone number or email you check often.',
                    ],
                    'steps_ms' => [
                        'Senaraikan akaun yang paling penting (e-mel, perbankan, media sosial dan kedai dalam talian).',
                        'Buka tetapan keselamatan bagi setiap akaun.',
                        'Hidupkan notifikasi log masuk atau peranti baharu.',
                        'Hidupkan pengesahan dua langkah (2FA) jika ada.',
                        'Pastikan makluman dihantar ke nombor telefon atau e-mel yang kerap anda semak.',
                    ],
                ],
                [
                    'title' => 'Adopt a Safe Link-Checking Habit',
                    'title_ms' => 'Biasakan Diri Menyemak Pautan Sebelum Menekannya',
                    'topic' => 'Safe handling of links',
                    'topic_ms' => 'Pautan yang mencurigakan',
                    'finding' => 'Links in delivery, courier or bank messages may be opened without being checked first.',
                    'finding_ms' => 'Pautan dalam mesej penghantaran, kurier atau bank mungkin ditekan tanpa disemak terlebih dahulu.',
                    'description' => 'Build a habit of verifying messages through official apps or websites instead of clicking links.',
                    'description_ms' => 'Biasakan diri menyemak melalui aplikasi atau laman web rasmi, bukan melalui pautan dalam mesej.',
                    'steps' => [
                        'Avoid clicking links in unexpected delivery, courier or bank messages.',
                        'Check claims by opening the official app or typing the official website yourself.',
                        'Save the official contact details of your bank and couriers.',
                        'Share this habit with anyone who handles business messages.',
                    ],
                    'steps_ms' => [
                        'Elakkan menekan pautan dalam mesej penghantaran, kurier atau bank yang tidak dijangka.',
                        'Semak sendiri melalui aplikasi rasmi atau dengan menaip alamat laman web rasmi.',
                        'Simpan maklumat hubungan rasmi bank dan syarikat kurier anda.',
                        'Kongsi amalan ini dengan sesiapa yang menguruskan mesej perniagaan.',
                    ],
                ],
            ],
            'respond' => [
                [
                    'title' => 'Write a Simple Incident Response Plan',
                    'title_ms' => 'Sediakan Pelan Tindak Balas Insiden yang Ringkas',
                    'topic' => 'Incident response plan',
                    'topic_ms' => 'Pelan tindak balas insiden',
                    'finding' => 'There is no plan for what to do if a business account or customer data is compromised.',
                    'finding_ms' => 'Belum ada pelan tentang apa yang perlu dibuat jika akaun perniagaan atau data pelanggan digodam.',
                    'description' => 'Prepare a short plan so you know exactly what to do first if something goes wrong.',
                    'description_ms' => 'Sediakan pelan ringkas supaya anda tahu langkah pertama yang perlu diambil jika berlaku masalah.',
                    'steps' => [
                        'List which passwords and accounts to secure first.',
                        'Write down who to contact: bank, platform providers and key staff.',
                        'Decide who in the business leads the response.',
                        'Keep the plan where you can reach it even if a device is lost.',
                        'Walk through the plan once with anyone involved.',
                    ],
                    'steps_ms' => [
                        'Senaraikan akaun dan kata laluan yang perlu diselamatkan dahulu.',
                        'Catat siapa yang perlu dihubungi: bank, penyedia platform dan pekerja utama.',
                        'Tetapkan siapa yang akan mengetuai tindakan apabila berlaku insiden.',
                        'Simpan pelan ini di tempat yang masih boleh dicapai walaupun peranti hilang.',
                        'Bincangkan pelan ini sekali dengan semua yang terlibat.',
                    ],
                ],
                [
                    'title' => 'Prepare Your Incident Reporting Contacts',
                    'title_ms' => 'Sediakan Senarai Hubungan untuk Melaporkan Insiden',
                    'topic' => 'Incident reporting',
                    'topic_ms' => 'Pelaporan insiden',
                    'finding' => 'You may not know how or where to report a cybersecurity incident quickly.',
                    'finding_ms' => 'Anda mungkin tidak tahu cara atau ke mana perlu melaporkan insiden keselamatan siber dengan segera.',
                    'description' => 'Gather the reporting contacts you would need in an emergency and keep them easy to find.',
                    'description_ms' => 'Kumpulkan nombor dan saluran laporan yang diperlukan ketika kecemasan, dan simpan di tempat yang mudah dicari.',
                    'steps' => [
                        'Find the fraud or security hotline for your bank.',
                        'Find out how to report a hacked account on your main platforms (e.g. Instagram, WhatsApp Business, your online store).',
                        'Note the national contacts for reporting cyber incidents and scams, such as NACSA, Cyber999 or the NSRC 997 hotline.',
                        'Save all contacts in one place that is easy to find.',
                    ],
                    'steps_ms' => [
                        'Dapatkan nombor talian penipuan atau keselamatan bank anda.',
                        'Ketahui cara melaporkan akaun yang digodam di platform utama anda (contohnya Instagram, WhatsApp Business atau kedai dalam talian).',
                        'Catat saluran rasmi untuk melaporkan insiden siber dan penipuan, seperti NACSA, Cyber999 atau talian NSRC 997.',
                        'Simpan semua maklumat ini di satu tempat yang mudah dicari.',
                    ],
                ],
                [
                    'title' => 'Prepare a Customer Data Breach Notice',
                    'title_ms' => 'Sediakan Notis Kebocoran Data Pelanggan',
                    'topic' => 'Customer breach communication',
                    'topic_ms' => 'Makluman kebocoran data',
                    'finding' => 'If customer data were exposed, there is no prepared way to inform affected customers.',
                    'finding_ms' => 'Jika data pelanggan terdedah, belum ada cara yang disediakan untuk memaklumkan pelanggan yang terjejas.',
                    'description' => 'Draft a notification message in advance so you can respond quickly and honestly if a breach happens.',
                    'description_ms' => 'Sediakan draf mesej makluman lebih awal supaya anda boleh bertindak dengan cepat dan telus jika berlaku kebocoran.',
                    'steps' => [
                        'Draft a short message explaining what happened and what data was affected.',
                        'Include what customers should do, such as changing passwords or watching for scams.',
                        'Decide how you would send it (WhatsApp, email or social media).',
                        'Note who you may need to inform under the PDPA.',
                        'Store the template with your incident response plan.',
                    ],
                    'steps_ms' => [
                        'Tulis mesej ringkas yang menerangkan apa yang berlaku dan data apa yang terjejas.',
                        'Nyatakan apa yang perlu dibuat oleh pelanggan, contohnya menukar kata laluan atau berwaspada dengan penipuan.',
                        'Tentukan saluran untuk menghantarnya (WhatsApp, e-mel atau media sosial).',
                        'Catat pihak yang mungkin perlu dimaklumkan di bawah PDPA.',
                        'Simpan templat ini bersama pelan tindak balas insiden anda.',
                    ],
                ],
            ],
            'recover' => [
                [
                    'title' => 'Set Up Regular Backups of Business Data',
                    'title_ms' => 'Buat Sandaran Data Perniagaan Secara Berkala',
                    'topic' => 'Data backups',
                    'topic_ms' => 'Sandaran data',
                    'finding' => 'Important business and customer records may not be backed up, so they could be lost permanently.',
                    'finding_ms' => 'Rekod penting perniagaan dan pelanggan mungkin tidak disandarkan, jadi ia boleh hilang terus.',
                    'description' => 'Start a simple, regular backup routine for the records your business depends on.',
                    'description_ms' => 'Mulakan rutin sandaran yang mudah untuk rekod yang penting bagi perniagaan anda.',
                    'steps' => [
                        'List the records you cannot afford to lose (orders, customer contacts, accounts).',
                        'Choose a backup location, such as cloud storage.',
                        'Make a first full backup.',
                        'Turn on automatic backups or set a weekly reminder.',
                    ],
                    'steps_ms' => [
                        'Senaraikan rekod yang tidak boleh hilang (tempahan, kenalan pelanggan dan akaun).',
                        'Pilih tempat untuk menyimpan sandaran, contohnya storan awan.',
                        'Buat sandaran penuh yang pertama.',
                        'Hidupkan sandaran automatik atau tetapkan peringatan setiap minggu.',
                    ],
                ],
                [
                    'title' => 'Store Backups Separately and Test a Restore',
                    'title_ms' => 'Simpan Sandaran di Tempat Berasingan dan Uji Pemulihannya',
                    'topic' => 'Backup testing',
                    'topic_ms' => 'Ujian sandaran',
                    'finding' => 'Backups may be kept only on the main device or never tested, so they might not work when needed.',
                    'finding_ms' => 'Sandaran mungkin hanya disimpan dalam peranti utama atau tidak pernah diuji, jadi ia mungkin tidak berfungsi apabila diperlukan.',
                    'description' => 'Keep a copy of your backups away from your main device and confirm you can actually restore them.',
                    'description_ms' => 'Simpan salinan sandaran jauh daripada peranti utama dan pastikan data benar-benar boleh dipulihkan.',
                    'steps' => [
                        'Keep at least one backup copy in cloud storage or on an external drive stored elsewhere.',
                        'Pick a few sample files to restore.',
                        'Restore them and check that they open correctly.',
                        'Repeat the restore test every few months.',
                    ],
                    'steps_ms' => [
                        'Simpan sekurang-kurangnya satu salinan sandaran di storan awan atau pemacu luaran di tempat lain.',
                        'Pilih beberapa fail sebagai contoh untuk dipulihkan.',
                        'Pulihkan fail tersebut dan pastikan ia boleh dibuka dengan betul.',
                        'Ulang ujian ini setiap beberapa bulan.',
                    ],
                ],
                [
                    'title' => 'Start a Simple Incident Review Routine',
                    'title_ms' => 'Jadikan Semakan Selepas Insiden Sebagai Rutin',
                    'topic' => 'Learning from incidents',
                    'topic_ms' => 'Pengajaran daripada insiden',
                    'finding' => 'Suspicious events and near-misses are not reviewed, so the same weaknesses may repeat.',
                    'finding_ms' => 'Kejadian mencurigakan dan insiden yang hampir berlaku tidak disemak semula, jadi kelemahan yang sama mungkin berulang.',
                    'description' => 'After any suspicious event, take a few minutes to record what happened and what you will change.',
                    'description_ms' => 'Selepas sebarang kejadian mencurigakan, luangkan beberapa minit untuk mencatat apa yang berlaku dan apa yang perlu diubah.',
                    'steps' => [
                        'Create a simple log for suspicious events and near-misses.',
                        'After each event, note what happened and how it was handled.',
                        'Decide on one change to prevent it happening again.',
                        'Set a reminder to redo this self-assessment every six months.',
                    ],
                    'steps_ms' => [
                        'Sediakan log ringkas untuk kejadian mencurigakan dan insiden yang hampir berlaku.',
                        'Selepas setiap kejadian, catat apa yang berlaku dan bagaimana ia ditangani.',
                        'Tentukan satu perubahan supaya ia tidak berulang.',
                        'Tetapkan peringatan untuk membuat penilaian ini semula setiap enam bulan.',
                    ],
                ],
            ],
        ];
    }
}
