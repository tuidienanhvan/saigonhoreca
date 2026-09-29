<?php
/**
 * Saigon Horeca — Multilingual Home & UI Translation Engine
 *
 * Provides complete, professional B2B Japanese (JA), English (EN),
 * and Vietnamese (VI) translations for the front page, hero carousel,
 * services, work process, why-choose, testimonials, and common UI elements.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

/**
 * Get the current active language code ('vi', 'en', 'ja').
 */
function sgh_get_current_lang(): string {
    if (defined('PI_CURRENT_LANGUAGE') && in_array(PI_CURRENT_LANGUAGE, ['vi', 'en', 'ja'], true)) {
        return PI_CURRENT_LANGUAGE;
    }
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (preg_match('#^/ja(/|$)#', $uri)) return 'ja';
    if (preg_match('#^/en(/|$)#', $uri)) return 'en';
    return 'vi';
}

/**
 * Master dictionary for homepage and theme UI components.
 */
function sgh_home_strings(?string $lang = null): array {
    static $dict = null;
    if ($dict === null) {
        $dict = [
            // ── Hero Carousel ──
            'hero_btn_project' => [
                'vi' => 'CHI TIẾT DỰ ÁN',
                'en' => 'VIEW PROJECT',
                'ja' => 'プロジェクト詳細',
            ],
            'hero_btn_contact' => [
                'vi' => 'LIÊN HỆ NGAY',
                'en' => 'CONTACT US',
                'ja' => '今すぐお問い合わせ',
            ],
            'hero_btn_explore' => [
                'vi' => 'XEM DỰ ÁN',
                'en' => 'EXPLORE PROJECTS',
                'ja' => 'プロジェクトを見る',
            ],
            'hero_fallback_title' => [
                'vi' => 'BẾP & BAR CÔNG NGHIỆP THẬT BẢN SẮC',
                'en' => 'AUTHENTIC COMMERCIAL KITCHEN & BAR SPACES',
                'ja' => '本質を極めた業務用厨房＆バー空間',
            ],
            'hero_fallback_subtitle' => [
                'vi' => 'Tư vấn thiết kế, sản xuất thiết bị inox và thi công trọn gói hệ thống bếp công nghiệp nhà hàng, quầy bar cao cấp.',
                'en' => 'Turnkey consulting, design, custom stainless steel fabrication, and installation for high-end restaurant kitchens and bars.',
                'ja' => '高級レストラン・バー向けに、業務用厨房・特注ステンレス什器の設計・製造・施工を一括でご提案します。',
            ],
            'hero_subtitle_default' => [
                'vi' => 'Tư vấn thiết kế, sản xuất và thi công trọn gói bếp nhà hàng chuyên nghiệp.',
                'en' => 'Professional commercial kitchen design, custom fabrication, and turnkey installation.',
                'ja' => '専門的な飲食店厨房の設計・製造・施工を一括でご提案します。',
            ],
            'hero_subtitle_prefix' => [
                'vi' => 'Dự án thiết bị bếp công nghiệp và quầy bar cao cấp tại ',
                'en' => 'Premium commercial kitchen & bar equipment project at ',
                'ja' => 'における高級業務用厨房設備・バー設備プロジェクト: ',
            ],

            // ── Featured Projects ──
            'featured_projects_title' => [
                'vi' => 'Những dự án tiêu biểu của Saigon Horeca',
                'en' => 'Featured Projects by Saigon Horeca',
                'ja' => 'SAIGON HORECAの代表的なプロジェクト',
            ],
            'featured_projects_desc' => [
                'vi' => 'Hệ thống bếp công nghiệp, quầy bar và giải pháp horeca cho hơn 50 nhà hàng, khách sạn, cà phê cao cấp tại Việt Nam — từ chuỗi F&B Michelin đến boutique restaurants.',
                'en' => 'Commercial kitchen systems, bar equipment, and Horeca solutions for over 50 luxury restaurants, hotels, and cafes across Vietnam — from Michelin-guide chains to boutique concepts.',
                'ja' => 'ベトナム国内50軒以上の高級レストラン、ホテル、カフェに向けた業務用厨房システム、バー設備、Horecaソリューション — ミシュラン掲載店からブティックレストランまで幅広く展開。',
            ],
            'featured_projects_cta' => [
                'vi' => 'Xem tất cả dự án',
                'en' => 'View all projects',
                'ja' => 'すべてのプロジェクトを見る',
            ],
            'stat_clients' => [
                'vi' => 'Khách Hàng',
                'en' => 'Clients',
                'ja' => 'お客様',
            ],
            'stat_projects' => [
                'vi' => 'Dự Án',
                'en' => 'Projects',
                'ja' => 'プロジェクト',
            ],
            'stat_designs' => [
                'vi' => 'Thiết Kế',
                'en' => 'Designs',
                'ja' => '設計',
            ],

            // ── Services: Section 1 (Commercial Kitchen Equipment) ──
            's1_eyebrow' => [
                'vi' => 'Saigon Horeca',
                'en' => 'Saigon Horeca',
                'ja' => 'Saigon Horeca',
            ],
            's1_title' => [
                'vi' => 'THIẾT BỊ BẾP CÔNG NGHIỆP CAO CẤP',
                'en' => 'PREMIUM COMMERCIAL KITCHEN EQUIPMENT',
                'ja' => '高級業務用厨房設備',
            ],
            's1_paras' => [
                'vi' => [
                    'Saigon Horeca là đơn vị uy tín trong ngành <strong>bếp công nghiệp cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp, cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói hệ thống bếp nhà hàng.',
                    'Saigon Horeca cung cấp giải pháp thiết bị bếp công nghiệp toàn diện cho các nhà hàng, nhà máy canteen. Từ khảo sát, tư vấn, thiết kế đến lắp đặt và vận hành, chúng tôi đảm bảo toàn bộ khu vực nhà bếp được tối ưu hoá, mang lại hiệu quả cao và sự an tâm tuyệt đối cho khách hàng.',
                    'Với kinh nghiệm làm việc cùng các nhà hàng đạt Michelin Guide 2023 như Sol Kitchen &amp; Bar, Saigon Horeca sẽ tư vấn giải pháp giúp bếp nhà hàng và bếp công nghiệp của bạn trở nên tối ưu và đẳng cấp.',
                ],
                'en' => [
                    'Saigon Horeca is a trusted leader in <strong>premium commercial kitchen equipment</strong> for restaurants, bars, and industrial catering, delivering turnkey consulting, design, fabrication, and installation.',
                    'We provide comprehensive commercial kitchen solutions for high-end restaurants, hotels, and canteen kitchens. From site survey, consultation, and CAD layout to installation and commissioning, we ensure seamless operation, safety compliance, and maximum operational efficiency.',
                    'With extensive experience partnering with Michelin Guide restaurants such as Sol Kitchen &amp; Bar, Saigon Horeca elevates your culinary workspace into a world-class, high-performance environment.',
                ],
                'ja' => [
                    'Saigon Horecaは、レストラン、バー、給食施設向けの<strong>高級業務用厨房設備</strong>において確固たる信頼を誇る専門企業です。設計コンサルティングから製造、施工、設置まで一貫したワンストップソリューションを提供します。',
                    'ホテル、高級レストラン、ファクトリーキャンティーンに至るまで、包括的な業務用厨房機器ソリューションを展開。現地調査・レイアウト設計から機器導入・試運転まで、厨房スペース全体の最適化と安全運用を徹底支援します。',
                    'ミシュランガイド選出のSol Kitchen &amp; Barをはじめとするトップブランドとの豊富な実績を活かし、お客様の飲食ビジネスを成功に導く最高峰の厨房環境をご提案いたします。',
                ],
            ],
            's1_cta_label' => [
                'vi' => 'Liên Hệ Ngay Hôm Nay',
                'en' => 'Contact Us Today',
                'ja' => '今すぐお問い合わせ',
            ],

            // ── Services: Section 2 (Kitchen Renovation) ──
            's2_eyebrow' => [
                'vi' => 'Dịch vụ chính',
                'en' => 'Core Services',
                'ja' => '主要サービス',
            ],
            's2_title' => [
                'vi' => 'CẢI TẠO BẾP NHÀ HÀNG HIỆN ĐẠI HIỆU QUẢ',
                'en' => 'MODERN & EFFICIENT RESTAURANT KITCHEN RENOVATION',
                'ja' => '飲食店厨房のモダンかつ効率的な改装',
            ],
            's2_paras' => [
                'vi' => [
                    'Saigon Horeca là đơn vị uy tín trong ngành <strong>bếp công nghiệp cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp, cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói hệ thống bếp nhà hàng.',
                    'Với kinh nghiệm hơn 10 năm và đội ngũ chuyên gia tận tâm, chúng tôi tự tin mang đến dịch vụ <strong>cải tạo bếp nhà hàng chuyên nghiệp</strong>, giúp nâng cao trải nghiệm ẩm thực cho khách hàng và tăng cường hiệu suất làm việc của đội ngũ đầu bếp.',
                    'Saigon Horeca cam kết bảo trì định kỳ, bảo hành lâu dài để đảm bảo thiết bị luôn hoạt động ổn định. Chúng tôi luôn đồng hành cùng đối tác, sẵn sàng hỗ trợ mọi lúc để mang lại sự an tâm và hiệu quả tối ưu cho doanh nghiệp của bạn.',
                ],
                'en' => [
                    'We specialize in upgrading and modernizing operating commercial kitchens with minimal downtime, re-engineering workflows to meet international hygiene and safety standards.',
                    'With over a decade of hands-on expertise and certified technicians, our <strong>turnkey kitchen renovation services</strong> dramatically enhance kitchen productivity, chef ergonomics, and culinary output.',
                    'Saigon Horeca provides scheduled preventive maintenance and long-term warranty support, guaranteeing 24/7 reliability for your food & beverage enterprise.',
                ],
                'ja' => [
                    '稼働中の厨房設備を最新規格へと刷新し、国際的な衛生管理基準と動線効率を高める<strong>飲食店厨房の全面リニューアル</strong>をご提供します。',
                    '10年以上の実績と熟練エンジニアチームによる専門的なリノベーションで、シェフの作業効率を最大化し、厨房の生産性と安全性を飛躍的に向上させます。',
                    '定期保守点検および長期保証体制を完備し、設備の安定稼働を約束。飲食ビジネスの信頼できるパートナーとして迅速かつ手厚くサポートします。',
                ],
            ],

            // ── Services: Section 3 (Exhaust & Fresh Air Ventilation) ──
            's3_eyebrow' => [
                'vi' => 'HVAC chuyên dụng',
                'en' => 'Dedicated HVAC',
                'ja' => '専用HVAC',
            ],
            's3_title' => [
                'vi' => 'GIẢI PHÁP HỆ THỐNG HÚT KHÓI & CẤP KHÍ TƯƠI CAO CẤP',
                'en' => 'PREMIUM EXHAUST HOOD & FRESH AIR VENTILATION SYSTEMS',
                'ja' => '高級排気フード＆外気導入システムソリューション',
            ],
            's3_paras' => [
                'vi' => [
                    'Saigon Horeca chuyên cung cấp dịch vụ tư vấn thiết kế, thi công và lắp đặt trọn gói <strong>hệ thống hút khói, cấp khí tươi cao cấp</strong> cho nhà hàng, quán bar và bếp ăn công nghiệp. Với nhiều năm kinh nghiệm triển khai hệ thống cho những nhà hàng nổi tiếng đạt sao Michelin tại TP.HCM như Sol Kitchen &amp; Bar, Yuzu Omakase, Bambino, Little Bear…',
                    'Đội ngũ chuyên gia của Saigon Horeca sẽ tư vấn và cung cấp giải pháp đảm bảo các tiêu chuẩn thiết kế hệ thống hút khói và cấp khí tươi bếp nhà hàng để giúp tối ưu, thẩm mỹ, đảm bảo hiệu năng cho bếp công nghiệp của khách hàng.',
                ],
                'en' => [
                    'Saigon Horeca engineers custom-built <strong>exhaust hoods and make-up fresh air ventilation systems</strong> tailored for high-output commercial kitchens, omakase counters, and izakayas across Ho Chi Minh City.',
                    'Our engineering team calculates exact airflow CFM and static pressure metrics, ensuring optimal air quality, fire-suppression compliance, and grease extraction for Michelin-starred venues like Sol Kitchen &amp; Bar, Yuzu Omakase, Bambino, and Little Bear.',
                ],
                'ja' => [
                    'Saigon Horecaは、高級レストラン、バー、オープンキッチン向けに<strong>排気フードおよび給排気・外気導入システム</strong>の設計から施工までを一括で手掛けます。Sol Kitchen &amp; Bar、Yuzu Omakase、Bambinoなど数々の名店で実証された最高水準の換気性能を提供します。',
                    '確かな流体力学計算に基づき、十分な排気風量と静音性、防火・油煙捕集性能を両立。調理スタッフが快適に作業できるクリーンな厨房環境を創出します。',
                ],
            ],

            // ── Services: Section 4 (Bespoke Bar Design) ──
            's4_eyebrow' => [
                'vi' => 'Bespoke Bar',
                'en' => 'Bespoke Bar',
                'ja' => 'オーダーメイドバー',
            ],
            's4_title' => [
                'vi' => 'TƯ VẤN THIẾT KẾ QUẦY BAR',
                'en' => 'BESPOKE BAR COUNTER DESIGN & FABRICATION',
                'ja' => 'バーデザインコンサルティング',
            ],
            's4_paras' => [
                'vi' => [
                    'Saigon Horeca cũng cung cấp dịch vụ tư vấn <strong>thiết kế quầy bar trọn gói</strong>, từ ý tưởng đến hoàn thiện, giúp bạn tạo ra không gian quầy bar hiện đại cao cấp, thu hút khách hàng ngay từ cái nhìn đầu tiên.',
                    'Saigon Horeca cung cấp giải pháp thiết kế quầy bar cafe, quầy bar nhà hàng và quầy bar trong bếp với thiết kế riêng tối ưu cho từng không gian. Dù là quầy bar cafe ấm cúng, quầy bar nhà hàng sang trọng hay quầy bar trong bếp gọn gàng, chúng tôi đảm bảo mọi thiết kế đều phát huy tối đa công năng và thẩm mỹ, phù hợp với phong cách của bạn.',
                ],
                'en' => [
                    'We deliver comprehensive <strong>bespoke bar station design and custom stainless steel fabrication</strong>, transforming concepts into stunning, ergonomically optimized cocktail and coffee bars.',
                    'From specialty coffee counters to high-volume craft cocktail stations, our SUS304 stainless steel cocktail stations, speed rails, ice wells, and glass rinsers ensure peak bartender speed and peerless visual appeal.',
                ],
                'ja' => [
                    'コンセプト立案から詳細設計、SUS304ステンレス特注什器の製作まで、<strong>バーカウンター全体のオーダーメイド設計</strong>を手掛けます。',
                    'カフェカウンター、本格カクテルバーステーション、オープンキッチン一体型バーなど、空間に合わせた最適な機能美を追求。バーテンダーのスムーズなオペレーションと洗練された店舗デザインを実現します。',
                ],
            ],

            // ── Consult CTA ──
            'consult_title' => [
                'vi' => 'Tư vấn giải pháp hệ thống bếp công nghiệp',
                'en' => 'Commercial Kitchen Solutions Consultation',
                'ja' => '業務用厨房システムのご提案',
            ],
            'consult_desc' => [
                'vi' => 'Chúng tôi tự tin mang đến dịch vụ tư vấn giải pháp tối ưu, cung cấp thiết bị bếp công nghiệp cho các quầy bar và nhà bếp công nghiệp trong ngành công nghiệp dịch vụ lưu trú như khách sạn, khu nghỉ dưỡng và nhà hàng.',
                'en' => 'We are confident in delivering optimized solutions and premium commercial kitchen & bar equipment for hotels, luxury resorts, and high-end restaurants.',
                'ja' => 'ホテル・リゾート・レストラン等の宿泊・飲食業界向けに、最適なソリューションのご提案、ならびにバーおよび業務用厨房設備の提供に自信を持っています。',
            ],
            'consult_btn' => [
                'vi' => 'Đặt lịch hẹn',
                'en' => 'Schedule Consultation',
                'ja' => '相談を予約する',
            ],

            // ── Work Process ──
            'process_title' => [
                'vi' => 'Quy Trình Làm Việc',
                'en' => 'Work Process',
                'ja' => '業務フロー',
            ],
            'process_intro' => [
                'vi' => '<strong>Saigon Horeca</strong> tự tin cung cấp những dịch vụ chất lượng và cung cấp thiết bị <strong>bếp công nghiệp & quầy bar</strong> tốt nhất cho khách hàng.',
                'en' => '<strong>Saigon Horeca</strong> is committed to delivering premium services and the finest <strong>commercial kitchen & bar equipment</strong> for our clients.',
                'ja' => '<strong>Saigon Horeca</strong> は、お客様に最高品質のサービスと最高峰の<strong>業務用厨房＆バー設備</strong>をお届けすることをお約束します。',
            ],
            'step1_title' => [
                'vi' => 'Tư Vấn',
                'en' => 'Consulting',
                'ja' => 'カウンセリング',
            ],
            'step1_desc' => [
                'vi' => 'Đội ngũ chuyên gia của chúng tôi trong quá trình vận hành <strong>nhà bếp công nghiệp và quầy bar</strong> sẽ luôn lắng nghe và trao đổi ý tưởng một cách cẩn thận với khách hàng để tư vấn giải pháp tốt nhất và tối ưu nhất cho doanh nghiệp của bạn.',
                'en' => 'Our experienced commercial kitchen and bar specialists listen closely to your operational needs to deliver the most efficient and cost-effective layout.',
                'ja' => '経験豊富な専門チームが、厨房およびバーの運用動線・ご要望を丁寧にヒアリングし、事業に最適なソリューションを提案します。',
            ],
            'step2_title' => [
                'vi' => 'Thiết Kế',
                'en' => 'Design & Engineering',
                'ja' => '設計',
            ],
            'step2_desc' => [
                'vi' => 'Những ý tưởng thiết kế mang đến cho khách hàng khu vực <strong>nhà bếp/bar đa dụng</strong> và thoải mái cho nhân viên nhà bếp/bar, từ đó tạo ra không gian gọn gàng và hiệu quả.',
                'en' => 'Custom CAD layouts and 3D perspectives create an ergonomic, multi-functional kitchen/bar space that maximizes chef comfort and productivity.',
                'ja' => '人間工学に基づいたCADレイアウトと3D設計により、スタッフが効率的かつ快適に作業できる機能的な厨房空間を設計します。',
            ],
            'step3_title' => [
                'vi' => 'Thi Công',
                'en' => 'Fabrication & Installation',
                'ja' => '厨房設備施工',
            ],
            'step3_desc' => [
                'vi' => 'Dịch vụ thi công lắp đặt thiết bị của chúng tôi là sự kết hợp giữa việc <strong>hiểu rõ về tất cả sản phẩm</strong> và triển khai theo thiết kế chi tiết và rõ ràng trước đó.',
                'en' => 'Precision stainless steel fabrication and rigorous on-site installation ensure seamless fit, safety compliance, and robust reliability.',
                'ja' => '各製品の特性を熟知したプロフェッショナルが、詳細図面に沿って精密なSUS304機器の据付と試運転を確実に実施します。',
            ],

            // ── Why Choose ──
            'why_eyebrow' => [
                'vi' => 'Why Saigon Horeca',
                'en' => 'Why Saigon Horeca',
                'ja' => 'SAIGON HORECAを選ぶ理由',
            ],
            'why_title' => [
                'vi' => 'Tại sao nên chọn thiết bị bếp Saigon Horeca?',
                'en' => 'Why Choose Saigon Horeca Kitchen Equipment?',
                'ja' => 'SAIGON HORECAの厨房設備を選ぶ理由',
            ],
            'usp1_title' => [
                'vi' => 'Kinh nghiệm tư vấn thiết kế bếp nhà hàng cho đối tác lớn',
                'en' => 'Extensive Consulting Experience for Major Enterprise Partners',
                'ja' => '大手パートナー向け飲食店厨房設計コンサルティングの経験',
            ],
            'usp1_desc' => [
                'vi' => 'Sài Gòn Horeca là đơn vị đã tư vấn, thiết kế và thi công bếp nhà hàng cho rất nhiều khách hàng lớn như <strong>Hoàng An Nhiên, ZumWhere, Circle K, Bambino</strong>…',
                'en' => 'Saigon Horeca has successfully engineered turnkey kitchens for premier brands including <strong>Hoang An Nhien Group, ZumWhere, Circle K, Bambino</strong>, and Michelin Guide restaurants.',
                'ja' => '<strong>Hoang An Nhien Group、ZumWhere、Circle K、Bambino</strong>をはじめとする大手飲食チェーンやミシュラン掲載店への豊富な設計・納入実績を誇ります。',
            ],
            'usp2_title' => [
                'vi' => 'Dịch vụ toàn diện và chuyên nghiệp',
                'en' => 'Comprehensive & Professional End-to-End Service',
                'ja' => '包括的かつ専門的なワンストップサービス',
            ],
            'usp2_desc' => [
                'vi' => 'Saigon Horeca cung cấp dịch vụ toàn diện từ <strong>tư vấn thiết kế, lắp đặt đến hỗ trợ vận hành kinh doanh</strong>. Tư duy thiết kế hiện đại phù hợp với từng dự án.',
                'en' => 'We offer full-lifecycle support from <strong>consulting and custom CAD design to installation, training, and operational support</strong> tailored to each venue.',
                'ja' => '<strong>コンサルティングから設計・施工、導入後の運用支援まで</strong>、包括的なワンストップサービスを提供。各プロジェクトに最適な設計を実現します。',
            ],
            'usp3_title' => [
                'vi' => 'Thiết bị chính hãng, bảo hành lâu dài',
                'en' => 'Genuine Equipment with Long-Term Warranty',
                'ja' => '正規メーカー製品と充実した長期保証',
            ],
            'usp3_desc' => [
                'vi' => '100% thiết bị nhập khẩu chính hãng từ các thương hiệu hàng đầu thế giới (Rational, Winterhalter, Hoshizaki...) kèm cam kết bảo trì định kỳ và hỗ trợ kỹ thuật 24/7.',
                'en' => '100% genuine imported equipment from world-leading manufacturers (Rational, Winterhalter, Hoshizaki, etc.) backed by scheduled maintenance and 24/7 technical support.',
                'ja' => 'Rational、Winterhalter、Hoshizakiなど世界屈指のトップブランドから直輸入した正規品のみを扱い、定期点検と24時間体制のアフターサポートを提供します。',
            ],

            // ── Testimonials ──
            'testi_title' => [
                'vi' => 'Cảm nhận từ khách hàng',
                'en' => 'Client Testimonials',
                'ja' => 'お客様の声',
            ],
            'testi_subtitle' => [
                'vi' => 'Xem thêm reviews của khách hàng về <strong>SAIGON HORECA</strong> tại <a href="https://www.facebook.com/saigonhoreca" target="_blank" rel="noopener">Facebook Reviews</a>',
                'en' => 'Explore verified reviews about <strong>SAIGON HORECA</strong> on our <a href="https://www.facebook.com/saigonhoreca" target="_blank" rel="noopener">Facebook Reviews</a>',
                'ja' => '<strong>SAIGON HORECA</strong> に対するクライアントからの評価とレビュー（<a href="https://www.facebook.com/saigonhoreca" target="_blank" rel="noopener">Facebook Reviews</a>）',
            ],
            'testi_1' => [
                'vi' => 'Tôi rất hài lòng với dịch vụ của Saigon Horeca. Tư vấn rõ ràng, thiết kế bếp hợp lý, thi công nhanh và đúng yêu cầu. Đội ngũ làm việc chuyên nghiệp, hỗ trợ nhiệt tình. Bếp sau thi công vận hành rất tốt. Cảm ơn Saigon Horeca!',
                'en' => 'Extremely impressed with Saigon Horeca. Clear consulting, ergonomic kitchen layout, rapid and on-schedule installation. The kitchen runs flawlessly. Thank you Saigon Horeca!',
                'ja' => 'Saigon Horecaのサービスに大変満足しています。的確なコンサルティング、使いやすい厨房動線の設計、そして迅速で確実な施工。オープン後も設備は極めて順調に稼働しています。心から感謝しています！',
            ],
            'testi_2' => [
                'vi' => 'Mình rất rất ấn tượng với sự chuyên nghiệp và sáng tạo của đội ngũ thiết kế. Từng chi tiết trong không gian nhà hàng Mâm Mâm Eatery & Lounge đều được chăm chút tỉ mỉ, đúng theo phong cách mong muốn. Thi công nhanh gọn, đúng tiến độ và hỗ trợ tận tình. Một trải nghiệm hợp tác tuyệt vời — xứng đáng 5 sao!',
                'en' => 'Deeply impressed with the design team’s creativity and dedication. Every corner of Mam Mam Eatery & Lounge was crafted to perfection, matching our vision precisely. Seamless execution and exceptional support — a true 5-star experience!',
                'ja' => '設計チームのプロ意識と創造性に深く感銘を受けました。「Mam Mam Eatery & Lounge」のあらゆるディテールが細部まで妥協なく仕上げられ、理想通りの空間が完成しました。納期通りの迅速な施工と親身なサポート、まさに星5つの素晴らしいパートナーです！',
            ],
            'testi_3' => [
                'vi' => '1 Đội Ngũ Tư Vấn & Thiết Kế Đẹp, Chỉnh Chu, Tiện Lợi<br>Thương hiệu làm việc Uy Tín — Chuyên Nghiệp — Chất Lượng<br>5 Sao cho Horeca Sài Gòn',
                'en' => 'Exceptional consulting & design team: aesthetic, precise, and practical.<br>A trustworthy, professional, and high-quality brand.<br>5 Stars for Saigon Horeca!',
                'ja' => '美しく機能的で、細部に至るまで行き届いた設計・コンサルティング。<br>信頼性・専門性・クオリティのすべてを兼ね備えたブランドです。<br>Saigon Horecaに星5つをお贈りします！',
            ],

            // ── Archive Product & UI Strings ──
            'archive_product_badge' => [
                'vi' => 'Sản phẩm',
                'en' => 'Products',
                'ja' => '製品',
            ],
            'archive_product_title' => [
                'vi' => 'Sản Phẩm',
                'en' => 'Products',
                'ja' => '製品一覧',
            ],
            'archive_product_desc' => [
                'vi' => 'Thiết bị bếp công nghiệp, quầy bar và giải pháp horeca chuyên nghiệp.',
                'en' => 'Commercial kitchen equipment, bespoke bar stations, and turnkey Horeca solutions.',
                'ja' => '業務用厨房機器、バーカウンター設備、プロフェッショナルなHorecaソリューション。',
            ],
            'archive_search_title' => [
                'vi' => 'Kết quả tìm kiếm: "%s"',
                'en' => 'Search Results: "%s"',
                'ja' => '検索結果: "%s"',
            ],
            'archive_search_desc' => [
                'vi' => 'Tìm thấy %d sản phẩm phù hợp với từ khóa của bạn.',
                'en' => 'Found %d products matching your keyword.',
                'ja' => '検索条件に一致する製品が %d 点見つかりました。',
            ],
            'archive_product_count' => [
                'vi' => 'Tổng <strong>%d</strong> sản phẩm',
                'en' => 'Total <strong>%d</strong> products',
                'ja' => '全 <strong>%d</strong> 点の製品',
            ],
            'archive_tab_products' => [
                'vi' => 'Sản phẩm',
                'en' => 'Products',
                'ja' => '製品',
            ],
            'archive_tab_categories' => [
                'vi' => 'Danh mục hàng',
                'en' => 'Categories',
                'ja' => '商品カテゴリー',
            ],
            'filter_categories_title' => [
                'vi' => 'Danh mục sản phẩm',
                'en' => 'Product Categories',
                'ja' => '製品カテゴリー',
            ],
            'filter_brands_title' => [
                'vi' => 'Thương hiệu',
                'en' => 'Brands',
                'ja' => '取扱いブランド',
            ],
            'filter_aria_label' => [
                'vi' => 'Lọc sản phẩm',
                'en' => 'Filter products',
                'ja' => '製品フィルター',
            ],
            'search_placeholder' => [
                'vi' => 'Tìm kiếm sản phẩm…',
                'en' => 'Search products…',
                'ja' => '製品を検索…',
            ],
            'search_btn' => [
                'vi' => 'Tìm',
                'en' => 'Search',
                'ja' => '検索',
            ],
            'search_label' => [
                'vi' => 'Tìm kiếm sản phẩm',
                'en' => 'Search products',
                'ja' => '製品検索',
            ],
            'archive_empty_title' => [
                'vi' => 'Chưa có sản phẩm',
                'en' => 'No products found',
                'ja' => '製品が見つかりません',
            ],
            'archive_empty_desc' => [
                'vi' => 'Hiện tại danh mục này chưa có sản phẩm. Vui lòng quay lại sau.',
                'en' => 'There are currently no products in this category. Please check back later.',
                'ja' => '現在、このカテゴリーには製品が登録されていません。後ほどご確認ください。',
            ],
            'archive_empty_btn' => [
                'vi' => 'Xem tất cả sản phẩm',
                'en' => 'View all products',
                'ja' => 'すべての製品を見る',
            ],
            'pagination_prev' => [
                'vi' => 'Trang trước',
                'en' => 'Previous page',
                'ja' => '前のページ',
            ],
            'pagination_next' => [
                'vi' => 'Trang sau',
                'en' => 'Next page',
                'ja' => '次のページ',
            ],
            'pagination_aria' => [
                'vi' => 'Trang sản phẩm',
                'en' => 'Product pages',
                'ja' => '製品ページナビゲーション',
            ],
            'product_view_details' => [
                'vi' => 'Xem chi tiết',
                'en' => 'View Details',
                'ja' => '詳細を見る',
            ],

            // ── Single Product Hero & Specs ──
            'product_trust_genuine_title' => [
                'vi' => '100% Chính Hãng',
                'en' => '100% Genuine',
                'ja' => '100% 正規メーカー品',
            ],
            'product_trust_genuine_desc' => [
                'vi' => 'Đầy đủ chứng chỉ CO/CQ',
                'en' => 'Full CO/CQ certification',
                'ja' => 'CO/CQ原産地・品質証明書完備',
            ],
            'product_trust_warranty_title' => [
                'vi' => 'Bảo Hành 12–24 Tháng',
                'en' => '12–24 Months Warranty',
                'ja' => '12〜24ヶ月保証',
            ],
            'product_trust_warranty_desc' => [
                'vi' => 'Bảo trì kỹ thuật tận nơi',
                'en' => 'On-site technical maintenance',
                'ja' => '専任エンジニアによる現地保守対応',
            ],
            'product_trust_install_title' => [
                'vi' => 'Giao & Lắp Toàn Quốc',
                'en' => 'Nationwide Delivery & Setup',
                'ja' => 'ベトナム全国配送・据付施工',
            ],
            'product_trust_install_desc' => [
                'vi' => 'Khảo sát kỹ thuật miễn phí',
                'en' => 'Free engineering site survey',
                'ja' => '現地エンジニアによる事前調査無料',
            ],
            'product_spec_sku' => [
                'vi' => 'Mã SKU',
                'en' => 'SKU Code',
                'ja' => 'SKUコード',
            ],
            'product_spec_brand' => [
                'vi' => 'Thương hiệu',
                'en' => 'Brand',
                'ja' => 'ブランド',
            ],
            'product_spec_condition' => [
                'vi' => 'Tình trạng',
                'en' => 'Condition',
                'ja' => '状態',
            ],
            'product_spec_condition_val' => [
                'vi' => 'Mới 100% Nguyên Kiện',
                'en' => '100% Brand New in Box',
                'ja' => '新品・未開封正規品',
            ],
            'product_spec_origin' => [
                'vi' => 'Xuất xứ',
                'en' => 'Origin',
                'ja' => '原産国',
            ],
            'product_origin_default' => [
                'vi' => 'Nhập khẩu chính hãng',
                'en' => 'Genuine Imported',
                'ja' => '正規輸入品',
            ],
            'product_cta_quote_title' => [
                'vi' => 'Yêu Cầu Báo Giá Dự Án',
                'en' => 'Request Project Quote',
                'ja' => 'プロジェクト見積もりを依頼',
            ],
            'product_cta_quote_subtitle' => [
                'vi' => 'Chiết khấu nhà hàng, khách sạn & đại lý',
                'en' => 'B2B discounts for restaurants, hotels & partners',
                'ja' => '飲食店・ホテル・法人向け特別価格対応',
            ],
            'product_cta_consult_title' => [
                'vi' => 'Tư Vấn Kỹ Thuật 24/7',
                'en' => '24/7 Technical Consultation',
                'ja' => '専門技術相談（24/7）',
            ],
            'product_cta_catalogue' => [
                'vi' => 'Xem Tài Liệu & Catalogue Hãng',
                'en' => 'View Manufacturer Specs & Catalogue',
                'ja' => 'メーカー製品カタログ・仕様書を見る',
            ],
            'product_b2b_header' => [
                'vi' => 'Dịch Vụ B2B Trọn Gói Tại SaigonHoreca:',
                'en' => 'Turnkey B2B Services at SaigonHoreca:',
                'ja' => 'SAIGON HORECAの包括的B2Bサービス:',
            ],
            'product_b2b_item1' => [
                'vi' => 'Khảo sát layout mặt bằng bếp công nghiệp theo tiêu chuẩn 1 chiều HACCP.',
                'en' => 'Commercial kitchen layout survey adhering to one-way HACCP standards.',
                'ja' => 'HACCPワンウェイ衛生動線基準に準拠した厨房レイアウト事前調査。',
            ],
            'product_b2b_item2' => [
                'vi' => 'Hỗ trợ đấu nối hệ thống điện 3 pha, cấp thoát nước & hút mùi chuyên nghiệp.',
                'en' => 'Professional connection for 3-phase power, water supply/drainage & exhaust hoods.',
                'ja' => '三相動力電源、給排水、排気ダクト設備の専門的な接続工事対応。',
            ],
            'product_b2b_item3' => [
                'vi' => 'Kho linh kiện chính hãng sẵn có, xử lý sự cố kỹ thuật trong vòng 2–4 giờ.',
                'en' => 'In-stock genuine spare parts with 2–4 hour technical dispatch response.',
                'ja' => '純正スペアパーツ常備、トラブル発生時2〜4時間以内の迅速な技術対応。',
            ],
            'product_related_title' => [
                'vi' => 'Sản phẩm tương tự',
                'en' => 'Related Products',
                'ja' => '関連製品',
            ],
        ];
    }

    if ($lang !== null) {
        $out = [];
        foreach ($dict as $key => $values) {
            $out[$key] = $values[$lang] ?? ($values['en'] ?? ($values['vi'] ?? ''));
        }
        return $out;
    }

    return $dict;
}

/**
 * Get a localized string for a specific key.
 */
function sgh_home_t(string $key, ?string $lang = null) {
    if ($lang === null) {
        $lang = sgh_get_current_lang();
    }
    $dict = sgh_home_strings();
    if (isset($dict[$key][$lang])) {
        return $dict[$key][$lang];
    }
    if (isset($dict[$key]['en'])) {
        return $dict[$key]['en'];
    }
    return $dict[$key]['vi'] ?? '';
}

/**
 * Universal shortcut for localized strings across all theme templates.
 */
function sgh_t(string $key, ?string $lang = null) {
    return sgh_home_t($key, $lang);
}
