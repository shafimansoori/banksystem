<?php

return [
    'minimum_match' => 0.25,

    'fallback_response' => 'Üzgünüm, bunu anlayamadım. Lütfen başka bir şekilde anlatır mısınız?',

    'stop_words' => [
        'ben', 'sen', 'o', 'biz', 'siz', 'onlar', 'mıyım', 'mısın', 'mısınız', 'mıyız', 've', 'ya', 'de', 'da', 'mi', 'mu', 'mu?', 'mü', 'mı',
        'bir', 'bu', 'şu', 'ile', 'için', 'ne', 'nasıl', 'hangi', 'hakkında', 'var', 'yok', 'kaç', 'kadar', 'gibi'
    ],

    'intents' => [
        [
            'tag' => 'greeting',
            'patterns' => [
                'merhaba',
                'selam',
                'günaydın',
                'iyi günler',
                'iyi akşamlar',
                'nasılsın',
                'hey',
                'hello',
                'hi'
            ],
            'responses' => [
                'Merhaba! Size nasıl yardımcı olabilirim?',
                'Selam! Bankacılık işlemlerinizde yardımcı olmaktan memnuniyet duyarım.',
                'İyi günler! Hesaplarınız, kartlarınız veya transferlerinizle ilgili sorularınızı cevaplayabilirim.'
            ],
        ],
        [
            'tag' => 'account_balance',
            'patterns' => [
                'hesap bakiyem',
                'bakiyemi öğrenebilir miyim',
                'ne kadar param var',
                'mevcut bakiye nedir',
                'hesap bakiyesi',
                'para durumum',
                'balance',
                'account balance',
                'how much money'
            ],
            'responses' => [
                'Hesap bakiyenizi görmek için sol menüden "Accounts" sayfasına ulaşabilir veya ilgili hesabın detaylarını açabilirsiniz.',
                'Bakiyenizi görüntülemek için dashboard üzerindeki hesap kartlarını inceleyebilirsiniz. Daha detaylı bilgi için "Accounts" sekmesine gidin.',
                'Güncel bakiyelerinizi dashboard\'da görüntüleyebilir veya detaylı raporlar için hesap sayfalarını ziyaret edebilirsiniz.'
            ],
        ],
        [
            'tag' => 'money_transfer',
            'patterns' => [
                'para gönder',
                'havale yap',
                'transfer et',
                'para aktar',
                'money transfer',
                'send money',
                'eft yap',
                'havale işlemi',
                'para transferi'
            ],
            'responses' => [
                'Para transferi yapmak için "Accounts" sayfasından hesabınızı seçin ve "Transfer" düğmesini kullanın.',
                'Havale işlemleri için hesap sayfanızdan "Send Money" seçeneğini kullanabilirsiniz. IBAN bilgilerini doğru girmeyi unutmayın.',
                'Para transferi için gerekli bilgileri (alıcı IBAN, tutar, açıklama) hazırlayın ve "Accounts" > "Transfer" menüsünü kullanın.'
            ],
        ],
        [
            'tag' => 'card_block',
            'patterns' => [
                'kartımı bloke et',
                'kart dondur',
                'kartım çalındı',
                'kartım kayboldu',
                'block card',
                'freeze card',
                'lost card',
                'stolen card',
                'kart iptal'
            ],
            'responses' => [
                'Kartınızı bloke etmek için "Cards" sayfasına gidin ve ilgili kartın yanındaki "Block" düğmesini kullanın.',
                'Acil durumlarda kartınızı hemen dondurmak için "Cards" menüsünden kartınızı seçin ve güvenlik seçeneklerini kullanın.',
                'Kart güvenliği için "Cards" sayfasından anında blokaj işlemi yapabilirsiniz. Kayıp/çalıntı durumu için destek ekibiyle de iletişime geçin.'
            ],
        ],
        [
            'tag' => 'new_card_request',
            'patterns' => [
                'yeni kart istiyorum',
                'kart başvurusu',
                'new card',
                'apply for card',
                'kredi kartı başvuru',
                'banka kartı al',
                'card application'
            ],
            'responses' => [
                'Yeni kart başvurusu için "Cards" sayfasındaki "Apply for New Card" seçeneğini kullanabilirsiniz.',
                'Kart başvuruları "Cards" menüsünden yapılmaktadır. Kredi kartı veya banka kartı seçeneklerini inceleyebilirsiniz.',
                'Yeni kart talebi için gereken belgeler ve süreç hakkında "Cards" sayfasında bilgi bulabilirsiniz.'
            ],
        ],
        [
            'tag' => 'bill_payment',
            'patterns' => [
                'fatura öde',
                'borç öde',
                'ödeme yap',
                'bill payment',
                'pay bills',
                'elektrik faturası',
                'su faturası',
                'telefon faturası',
                'kredi kartı borcu'
            ],
            'responses' => [
                'Fatura ödemelerinizi "Accounts" sayfasından "Pay Bills" seçeneği ile yapabilirsiniz.',
                'Otomatik ödeme talimatları için "Bills" menüsünü kullanarak düzenli ödemelerinizi ayarlayabilirsiniz.',
                'Tüm fatura türleri için ödeme seçenekleri hesap sayfanızda mevcuttur. QR kod ile de ödeme yapabilirsiniz.'
            ],
        ],
        [
            'tag' => 'recent_transactions',
            'patterns' => [
                'son işlemlerim',
                'geçmiş işlemler',
                'işlem geçmişi',
                'son harcamalar',
                'hangi işlemler yapıldı',
                'transaction history',
                'recent transactions',
                'last transactions'
            ],
            'responses' => [
                'Son işlemlerinizi görmek için ilgili hesabın kartındaki "Transactions" bağlantısını kullanabilir veya üst menüden "Transactions" sayfasını açabilirsiniz.',
                'İşlem geçmişinizi kontrol etmek için "Accounts" sayfasından bir hesabı seçip "History" sekmesini görüntüleyin.',
                'Detaylı işlem raporları ve filtreleme seçenekleri için "Transactions" sayfasını ziyaret edin.'
            ],
        ],
        [
            'tag' => 'loan_inquiry',
            'patterns' => [
                'kredi başvurusu',
                'borç al',
                'loan application',
                'personal loan',
                'ihtiyaç kredisi',
                'konut kredisi',
                'taşıt kredisi',
                'credit application'
            ],
            'responses' => [
                'Kredi başvuruları için şubemizle iletişime geçebilir veya online başvuru formlarını kullanabilirsiniz.',
                'Kredi seçenekleri ve faiz oranları hakkında detaylı bilgi için müşteri temsilcimizle görüşebilirsiniz.',
                'Gelir durumunuza uygun kredi seçenekleri için "Inbox" bölümünden talebinizi iletebilirsiniz.'
            ],
        ],
        [
            'tag' => 'exchange_rates',
            'patterns' => [
                'döviz kurları',
                'kur bilgisi',
                'exchange rates',
                'currency rates',
                'dolar kuru',
                'euro kuru',
                'sterlin kuru',
                'foreign exchange'
            ],
            'responses' => [
                'Güncel döviz kurları için "Currencies" sayfasını ziyaret edebilirsiniz.',
                'Döviz alım-satım işlemleri ve kurlar hakkında bilgi "Currencies" menüsünde bulunmaktadır.',
                'Anlık kur bilgileri ve döviz hesabı açma seçenekleri için ilgili sayfayı inceleyebilirsiniz.'
            ],
        ],
        [
            'tag' => 'atm_locations',
            'patterns' => [
                'atm nerede',
                'en yakın atm',
                'şube nerede',
                'atm locations',
                'nearest atm',
                'branch locations',
                'bankamatik',
                'şube adresi'
            ],
            'responses' => [
                'ATM ve şube lokasyonları için "Bank Locations" sayfasını kullanabilirsiniz.',
                'En yakın ATM\'leri bulmak için lokasyon hizmetlerimizden yararlanabilirsiniz.',
                'Şube adresleri ve çalışma saatleri "Locations" menüsünde listelenmiştir.'
            ],
        ],
        [
            'tag' => 'account_opening',
            'patterns' => [
                'hesap açmak istiyorum',
                'yeni hesap',
                'open account',
                'new account',
                'hesap açılışı',
                'account opening',
                'tasarruf hesabı',
                'vadesiz hesap'
            ],
            'responses' => [
                'Yeni hesap açmak için "Accounts" sayfasındaki "Open New Account" seçeneğini kullanabilirsiniz.',
                'Hesap türleri ve avantajları hakkında bilgi için hesap açma formunu inceleyebilirsiniz.',
                'Online hesap açma işlemi için gerekli belgeler ve süreç hakkında detaylı bilgi mevcuttur.'
            ],
        ],
        [
            'tag' => 'security_concern',
            'patterns' => [
                'güvenlik sorunu',
                'şüpheli işlem',
                'hesabım hacklendi',
                'security issue',
                'suspicious transaction',
                'fraud alert',
                'dolandırıcılık',
                'güvenlik',
                'şifre unuttum'
            ],
            'responses' => [
                'Güvenlik konularında acil yardım için müşteri hizmetlerimizle derhal iletişime geçin.',
                'Şüpheli işlemler için hesabınızı derhal bloke ettirin ve destek ekibimizi arayın.',
                'Güvenlik problemi yaşıyorsanız, "Inbox" bölümünden acil destek talep edebilirsiniz.'
            ],
        ],
        [
            'tag' => 'currency_rates',
            'patterns' => [
                'döviz kurları',
                'kur bilgisi',
                'hangi para birimleri var',
                'para birimi listesi',
                'currency',
                'foreign currency',
                'döviz',
                'exchange'
            ],
            'responses' => [
                'Sistemde kayıtlı para birimlerini sol menüden "Currencies" sekmesinde bulabilirsiniz. Buradan yeni para birimi eklemek de mümkün.',
                'Döviz bilgilerini görmek için "Currencies" sayfasına gidin. Burada mevcut tüm para birimleri listelenir.',
                'Döviz kurları ve işlemleri için "Currencies" menüsünü kullanarak güncel bilgilere erişebilirsiniz.'
            ],
        ],
        [
            'tag' => 'thanks',
            'patterns' => [
                'teşekkürler',
                'çok sağ ol',
                'minnettarım',
                'yardımcı oldun',
                'thank you',
                'thanks',
                'appreciate it'
            ],
            'responses' => [
                'Rica ederim! Başka bir sorunuz olursa her zaman buradayım.',
                'Ne demek, her zaman yardımcı olmaktan memnuniyet duyarım.',
                'Size yardımcı olabildiysem ne mutlu bana! Başka ihtiyacınız olursa yazabilirsiniz.'
            ],
        ],
        [
            'tag' => 'goodbye',
            'patterns' => [
                'hoşça kal',
                'görüşürüz',
                'sonra konuşuruz',
                'bay bay',
                'goodbye',
                'bye',
                'see you later'
            ],
            'responses' => [
                'Görüşmek üzere! Herhangi bir zamanda tekrar yazabilirsiniz.',
                'Hoşça kalın! Yardıma ihtiyaç duyarsanız sadece yazmanız yeterli.',
                'İyi günler! Bankacılık işlemlerinizde her zaman yanınızdayım.'
            ],
        ],
        [
            'tag' => 'contact_support',
            'patterns' => [
                'destek',
                'yardım merkezi',
                'kiminle iletişime geçebilirim',
                'size nasıl ulaşırım',
                'müşteri hizmetleri',
                'support',
                'help center',
                'customer service',
                'contact us'
            ],
            'responses' => [
                'Destek ekibimize "Inbox" bölümünden mesaj gönderebilirsiniz. En kısa sürede dönüş yapılacaktır.',
                'Yardım almak için sol menüden "Inbox" sayfasına giderek yeni bir mesaj oluşturabilirsiniz.',
                'Müşteri hizmetleri için mesaj sisteminizi kullanabilir veya acil durumlar için telefon desteği alabilirsiniz.'
            ],
        ],
    ],
];


