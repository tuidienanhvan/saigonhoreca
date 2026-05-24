<?php
/**
 * Website Features — Frontend Shortcodes & Functions
 * F3: Before/After Slider
 * F9: Financing Calculator
 * F15: Cookie Consent Granular
 * E9: Heatmap Integration Toggle
 * F11: Portfolio Filter
 * F12: Saved Favorites
 * F13: Social Proof Counters
 * F14: Live Chat Status
 * G4: FAQ Schema Shortcode
 * G7: Google Reviews Widget
 * G10: Newsletter Signup form
 * H7: Warranty Registration form
 * H8: Emergency Contact button
 * H10: Review Request Automation (cron)
 */

if (!defined('ABSPATH')) exit;

// ════════════════════════════════════════
// F3: Before/After Slider Shortcode
// Usage: [before_after before="url1" after="url2" label_before="Trước" label_after="Sau"]
// ════════════════════════════════════════
add_shortcode('before_after', function ($atts) {
    $a = shortcode_atts([
        'before'       => '',
        'after'        => '',
        'label_before' => 'Trước',
        'label_after'  => 'Sau',
        'height'       => '400px',
    ], $atts);

    $id = 'ba_' . wp_rand(1000, 9999);
    ob_start(); ?>
    <div class="sgh-before-after" id="<?php echo esc_attr($id); ?>"
         style="position:relative;overflow:hidden;border-radius:12px;cursor:col-resize;user-select:none;height:<?php echo esc_attr($a['height']); ?>;">
        <img src="<?php echo esc_url($a['after']); ?>" alt="<?php echo esc_attr($a['label_after']); ?>"
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
        <div class="sgh-ba-before" style="position:absolute;inset:0;width:50%;overflow:hidden;border-right:3px solid #fff;">
            <img src="<?php echo esc_url($a['before']); ?>" alt="<?php echo esc_attr($a['label_before']); ?>"
                 style="width:<?php echo esc_attr('calc(200% - ' . $a['height'] . ')'); ?>;max-width:none;height:100%;object-fit:cover;">
        </div>
        <div class="sgh-ba-divider" style="position:absolute;top:0;bottom:0;left:50%;width:3px;background:var(--bg-card, #fff);cursor:col-resize;">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:40px;height:40px;border-radius:50%;background:var(--bg-card, #fff);box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;font-size:18px;">⇔</div>
        </div>
        <span style="position:absolute;bottom:12px;left:12px;background:rgba(0,0,0,0.6);color:var(--on-brand, #fff);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;"><?php echo esc_html($a['label_before']); ?></span>
        <span style="position:absolute;bottom:12px;right:12px;background:rgba(0,0,0,0.6);color:var(--on-brand, #fff);padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;"><?php echo esc_html($a['label_after']); ?></span>
    </div>
    <script>
    (function(){
        var el   = document.getElementById('<?php echo esc_js($id); ?>');
        var bef  = el.querySelector('.sgh-ba-before');
        var div  = el.querySelector('.sgh-ba-divider');
        var drag = false;
        function move(x) {
            var rect = el.getBoundingClientRect();
            var pct  = Math.min(100, Math.max(0, ((x - rect.left) / rect.width) * 100));
            bef.style.width  = pct + '%';
            div.style.left   = pct + '%';
        }
        el.addEventListener('mousedown',  function(e){ drag=true; move(e.clientX); });
        el.addEventListener('touchstart', function(e){ drag=true; move(e.touches[0].clientX); }, {passive:true});
        window.addEventListener('mousemove',  function(e){ if(drag) move(e.clientX); });
        window.addEventListener('touchmove',  function(e){ if(drag) move(e.touches[0].clientX); }, {passive:true});
        window.addEventListener('mouseup',    function(){ drag=false; });
        window.addEventListener('touchend',   function(){ drag=false; });
    })();
    </script>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// F9: Financing Calculator Shortcode
// Usage: [financing_calc]
// ════════════════════════════════════════
add_shortcode('financing_calc', function ($atts) {
    $a = shortcode_atts(['title' => 'Tính toán trả góp'], $atts);
    ob_start(); ?>
    <div class="sgh-financing-calc" style="background:var(--bg-card, #fff);border:1px solid var(--border-default, #e5e7eb);border-radius:16px;padding:24px;max-width:480px;">
        <h3 style="font-size:18px;font-weight:700;margin:0 0 20px;color:var(--text-1, #1e293b);"><?php echo esc_html($a['title']); ?></h3>
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-1-muted, #64748b);margin-bottom:6px;">Giá trị công trình (triệu VNĐ)</label>
                <input type="number" id="sgh-loan-amount" value="1000" min="100" step="50"
                       style="width:100%;border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:15px;font-weight:600;color:var(--text-1, #1e293b);outline:none;box-sizing:border-box;">
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-1-muted, #64748b);margin-bottom:6px;">Vay bao nhiêu % (tối đa 70%)</label>
                <input type="range" id="sgh-loan-pct" min="10" max="70" value="50" step="5"
                       style="width:100%;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-top:4px;"><span>10%</span><span id="sgh-loan-pct-val" style="font-weight:700;color:var(--brand, #F9C349);">50%</span><span>70%</span></div>
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-1-muted, #64748b);margin-bottom:6px;">Kỳ hạn (tháng)</label>
                <select id="sgh-loan-term" style="width:100%;border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:15px;color:var(--text-1, #1e293b);background:var(--bg-card, #fff);outline:none;box-sizing:border-box;">
                    <option value="12">12 tháng (1 năm)</option>
                    <option value="24">24 tháng (2 năm)</option>
                    <option value="36" selected>36 tháng (3 năm)</option>
                    <option value="60">60 tháng (5 năm)</option>
                    <option value="120">120 tháng (10 năm)</option>
                    <option value="180">180 tháng (15 năm)</option>
                    <option value="240">240 tháng (20 năm)</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-1-muted, #64748b);margin-bottom:6px;">Lãi suất/năm (%)</label>
                <input type="number" id="sgh-loan-rate" value="8.5" min="1" max="20" step="0.1"
                       style="width:100%;border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:15px;font-weight:600;color:var(--text-1, #1e293b);outline:none;box-sizing:border-box;">
            </div>
            <div id="sgh-loan-result" style="background:linear-gradient(135deg,var(--brand, #F9C349),var(--brand-light, #ffb100));color:var(--on-brand, #000);border-radius:12px;padding:20px;display:none;">
                <div style="font-size:12px;opacity:0.8;margin-bottom:4px;">Trả góp hàng tháng</div>
                <div id="sgh-monthly-payment" style="font-size:32px;font-weight:800;margin-bottom:12px;"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:12px;">
                    <div><div style="opacity:0.7;">Tổng tiền vay</div><div id="sgh-principal" style="font-weight:700;font-size:14px;"></div></div>
                    <div><div style="opacity:0.7;">Tổng lãi phải trả</div><div id="sgh-total-interest" style="font-weight:700;font-size:14px;"></div></div>
                </div>
            </div>
            <button onclick="sghCalcFinancing()" style="background:var(--brand, #F9C349);color:var(--on-brand, #000);border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;transition:background 0.2s;"
                    onmouseover="this.style.background='var(--brand-dark, #ffb100)'" onmouseout="this.style.background='var(--brand, #F9C349)'">
                Tính ngay
            </button>
        </div>
    </div>
    <script>
    function sghCalcFinancing() {
        var amt    = parseFloat(document.getElementById('sgh-loan-amount').value) * 1e6;
        var pct    = parseFloat(document.getElementById('sgh-loan-pct').value) / 100;
        var term   = parseInt(document.getElementById('sgh-loan-term').value);
        var rate   = parseFloat(document.getElementById('sgh-loan-rate').value) / 100 / 12;
        var principal = amt * pct;
        var monthly = rate > 0 ? principal * rate * Math.pow(1+rate, term) / (Math.pow(1+rate, term) - 1) : principal / term;
        var totalPay = monthly * term;
        var fmt = function(n) { return Math.round(n).toLocaleString('vi-VN') + 'đ'; };
        document.getElementById('sgh-monthly-payment').textContent = fmt(monthly);
        document.getElementById('sgh-principal').textContent = fmt(principal);
        document.getElementById('sgh-total-interest').textContent = fmt(totalPay - principal);
        document.getElementById('sgh-loan-result').style.display = 'block';
    }
    document.getElementById('sgh-loan-pct').addEventListener('input', function() {
        document.getElementById('sgh-loan-pct-val').textContent = this.value + '%';
    });
    </script>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// F13: Social Proof Counters Shortcode
// Usage: [social_counters]
// ════════════════════════════════════════
add_shortcode('social_counters', function ($atts) {
    $a = shortcode_atts([
        'houses'     => '1200',
        'years'      => '8',
        'satisfaction' => '98',
        'provinces'  => '15',
    ], $atts);

    ob_start(); ?>
    <div class="sgh-social-counters" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:24px;text-align:center;">
        <?php
        $counters = [
            ['val' => $a['houses'],       'label' => 'Nhà đã xây', 'suffix' => '+'],
            ['val' => $a['years'],         'label' => 'Năm kinh nghiệm', 'suffix' => '+'],
            ['val' => $a['satisfaction'],  'label' => 'Hài lòng', 'suffix' => '%'],
            ['val' => $a['provinces'],     'label' => 'Tỉnh thành', 'suffix' => '+'],
        ];
        foreach ($counters as $c): ?>
            <div class="sgh-counter-item" data-target="<?php echo esc_attr($c['val']); ?>"
                 style="padding:20px;background:var(--bg-card, #fff);border:1px solid var(--bg-alt, #f1f5f9);border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <div class="sgh-counter-number" style="font-size:40px;font-weight:800;color:var(--brand, #ffb100);line-height:1;">
                    0<span><?php echo esc_html($c['suffix']); ?></span>
                </div>
                <div style="font-size:14px;color:var(--text-1-muted, #64748b);font-weight:600;margin-top:8px;"><?php echo esc_html($c['label']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
    (function() {
        function animateCounters() {
            document.querySelectorAll('.sgh-counter-item').forEach(function(el) {
                var target = parseInt(el.dataset.target);
                var numEl  = el.querySelector('.sgh-counter-number');
                var suffix = numEl.querySelector('span').textContent;
                var start  = 0;
                var step   = target / 60;
                var timer  = setInterval(function() {
                    start += step;
                    if (start >= target) { start = target; clearInterval(timer); }
                    numEl.innerHTML = Math.floor(start).toLocaleString() + '<span>' + suffix + '</span>';
                }, 25);
            });
        }
        if ('IntersectionObserver' in window) {
            var obs = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting) { animateCounters(); obs.disconnect(); }
            }, { threshold: 0.3 });
            obs.observe(document.querySelector('.sgh-social-counters'));
        } else {
            animateCounters();
        }
    })();
    </script>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// F14: Live Chat Status (based on working hours)
// Usage: [live_chat_status]
// ════════════════════════════════════════
add_shortcode('live_chat_status', function ($atts) {
    $a = shortcode_atts([
        'open_hour'  => 8,
        'close_hour' => 22,
        'phone'      => '',
    ], $atts);

    $tz   = new DateTimeZone('Asia/Ho_Chi_Minh');
    $now  = new DateTime('now', $tz);
    $hour = (int) $now->format('G');
    $open = ($hour >= (int) $a['open_hour'] && $hour < (int) $a['close_hour']);

    ob_start(); ?>
    <div class="sgh-chat-status" style="display:inline-flex;align-items:center;gap:10px;padding:10px 18px;border-radius:999px;background:<?php echo $open ? '#f0fdf4' : '#fef2f2'; ?>;border:1px solid <?php echo $open ? '#bbf7d0' : '#fecaca'; ?>;">
        <span style="width:10px;height:10px;border-radius:50%;background:<?php echo $open ? 'var(--success, #22c55e)' : '#ef4444'; ?>;<?php echo $open ? 'animation:sghBlink 1.5s infinite;' : ''; ?>"></span>
        <span style="font-size:14px;font-weight:600;color:<?php echo $open ? 'var(--success, #16a34a)' : 'var(--danger, #dc2626)'; ?>;">
            <?php echo $open ? 'Đang online — Phản hồi ngay' : 'Ngoài giờ làm việc (' . $a['open_hour'] . ':00–' . $a['close_hour'] . ':00)'; ?>
        </span>
        <?php if ($a['phone'] && !$open): ?>
            <a href="tel:<?php echo esc_attr($a['phone']); ?>" style="font-size:13px;color:var(--text-1-muted, #64748b);text-decoration:none;">📞 <?php echo esc_html($a['phone']); ?></a>
        <?php endif; ?>
    </div>
    <style>@keyframes sghBlink{0%,100%{opacity:1}50%{opacity:0.4}}</style>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// G4: FAQ Schema Shortcode
// Usage: [faq_schema] wraps [faq_item q="..." a="..."] items
// ════════════════════════════════════════
add_shortcode('faq_item', function ($atts, $content = '') {
    $a = shortcode_atts(['q' => '', 'a' => ''], $atts);
    return '<div class="sgh-faq-item" data-q="' . esc_attr($a['q']) . '" data-a="' . esc_attr($a['a']) . '">' .
           '<details style="border:1px solid var(--border-default, #e5e7eb);border-radius:10px;margin-bottom:8px;overflow:hidden;">' .
           '<summary style="padding:16px 20px;font-weight:600;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;outline:none;">' .
           '<style>summary::-webkit-details-marker {display:none;} summary {list-style:none;}</style>' .
           '<span>' . esc_html($a['q']) . '</span><span style="font-size:24px !important;line-height:1;width:24px;height:24px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">+</span></summary>' .
           '<div style="padding:0 20px 16px;color:var(--text-1-2, #475569);line-height:1.7;">' . wp_kses_post($a['a']) . '</div>' .
           '</details></div>';
});

add_shortcode('faq_schema', function ($atts, $content = '') {
    $content = do_shortcode($content);
    preg_match_all('/data-q="([^"]+)" data-a="([^"]+)"/', $content, $m);
    $pairs = [];
    foreach ($m[1] as $i => $q) {
        $pairs[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $m[2][$i]]];
    }
    $schema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $pairs];
    return '<div class="sgh-faq-wrap">' . $content . '</div>' .
           '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
});

// ════════════════════════════════════════
// G10: Newsletter Signup (stores in leads CPT)
// Usage: [newsletter_signup]
// ════════════════════════════════════════
add_shortcode('newsletter_signup', function ($atts) {
    $a = shortcode_atts(['title' => 'Nhận tips thiết kế hàng tháng', 'btn' => 'Đăng ký miễn phí'], $atts);
    ob_start(); ?>
    <div class="sgh-newsletter" style="background:linear-gradient(135deg,var(--brand, #F9C349),var(--brand-light, #ffb100));border-radius:16px;padding:32px;text-align:center;color:var(--on-brand, #000);">
        <div style="font-size:20px;font-weight:700;margin-bottom:8px;"><?php echo esc_html($a['title']); ?></div>
        <div style="font-size:14px;opacity:0.85;margin-bottom:20px;">Không spam. Chỉ 1 email/tháng với nội dung giá trị.</div>
        <form class="sgh-newsletter-form" style="display:flex;gap:8px;max-width:400px;margin:0 auto;flex-wrap:wrap;justify-content:center;">
            <input type="email" name="email" placeholder="Email của bạn" required
                   style="flex:1;min-width:200px;border:none;border-radius:8px;padding:12px 16px;font-size:14px;color:var(--text-1, #1e293b);outline:none;">
            <button type="submit" style="background:#1e293b;color:var(--on-brand, #fff);border:none;border-radius:8px;padding:12px 20px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">
                <?php echo esc_html($a['btn']); ?>
            </button>
        </form>
        <div class="sgh-newsletter-msg" style="margin-top:12px;font-size:13px;min-height:20px;"></div>
    </div>
    <?php return ob_get_clean();
});

add_action('wp_ajax_sgh_newsletter_subscribe', 'sgh_newsletter_subscribe');
add_action('wp_ajax_nopriv_sgh_newsletter_subscribe', 'sgh_newsletter_subscribe');
function sgh_newsletter_subscribe() {
    // Rate limit: 5 subscriptions per IP per hour
    $ip_hash = substr(md5($_SERVER['REMOTE_ADDR'] ?? ''), 0, 16);
    $rate_key = 'sgh_nlsub_' . $ip_hash;
    $count = (int) get_transient($rate_key);
    if ($count > 5) { wp_send_json_error(['message' => 'Rate limit exceeded.']); }
    set_transient($rate_key, $count + 1, HOUR_IN_SECONDS);

    $email = sanitize_email($_POST['email'] ?? '');
    if (!is_email($email)) { wp_send_json_error(['message' => 'Email không hợp lệ.']); }

    // Check duplicate
    $existing = get_posts(['post_type' => 'sh_lead', 'meta_key' => '_pi_lead_email', 'meta_value' => $email, 'numberposts' => 1]);
    if ($existing) { wp_send_json_success(['message' => 'Email đã đăng ký rồi. Cảm ơn!']); }

    $lead_id = wp_insert_post([
        'post_title'  => 'Newsletter: ' . $email,
        'post_type'   => 'sh_lead',
        'post_status' => 'publish',
    ]);
    update_post_meta($lead_id, '_pi_lead_email', $email);
    update_post_meta($lead_id, '_pi_lead_source', 'newsletter');
    update_post_meta($lead_id, '_pi_lead_status', 'new');

    wp_send_json_success(['message' => 'Đăng ký thành công! Cảm ơn bạn.']);
}

// ════════════════════════════════════════
// H7: Warranty Registration Shortcode
// Usage: [warranty_form]
// ════════════════════════════════════════
add_shortcode('warranty_form', function () {
    ob_start(); ?>
    <div class="sgh-warranty-form" style="max-width:480px;background:var(--bg-card, #fff);border:1px solid var(--border-default, #e5e7eb);border-radius:16px;padding:28px;">
        <h3 style="font-size:18px;font-weight:700;margin:0 0 20px;color:var(--text-1, #1e293b);">Đăng ký bảo hành</h3>
        <form id="sgh-warranty" style="display:flex;flex-direction:column;gap:14px;">
            <?php wp_nonce_field('sgh_warranty_nonce', 'warranty_nonce'); ?>
            <input type="text" name="name" placeholder="Họ và tên *" required style="border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
            <input type="tel" name="phone" placeholder="Số điện thoại *" required style="border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
            <input type="text" name="address" placeholder="Địa chỉ công trình" style="border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
            <input type="date" name="handover_date" placeholder="Ngày bàn giao" style="border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;">
            <textarea name="message" placeholder="Ghi chú thêm" rows="3" style="border:1px solid var(--border-default, #d1d5db);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:vertical;"></textarea>
            <button type="submit" style="background:var(--brand, #F9C349);color:var(--on-brand, #000);border:none;border-radius:8px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;">Đăng ký bảo hành</button>
            <div class="sgh-warranty-msg"></div>
        </form>
    </div>
    <?php return ob_get_clean();
});

add_action('wp_ajax_sgh_warranty_register', 'sgh_warranty_register');
add_action('wp_ajax_nopriv_sgh_warranty_register', 'sgh_warranty_register');
function sgh_warranty_register() {
    if (!wp_verify_nonce($_POST['warranty_nonce'] ?? '', 'sgh_warranty_nonce')) {
        wp_send_json_error(['message' => 'Lỗi xác thực.']);
    }
    $name    = sanitize_text_field($_POST['name'] ?? '');
    $phone   = sanitize_text_field($_POST['phone'] ?? '');
    $address = sanitize_text_field($_POST['address'] ?? '');
    $date    = sanitize_text_field($_POST['handover_date'] ?? '');
    $msg     = sanitize_textarea_field($_POST['message'] ?? '');

    if (!$name || !$phone) wp_send_json_error(['message' => 'Vui lòng điền đầy đủ thông tin.']);

    $lead_id = wp_insert_post(['post_title' => 'Bảo hành: ' . $name, 'post_type' => 'sh_lead', 'post_status' => 'publish']);
    update_post_meta($lead_id, '_pi_lead_name', $name);
    update_post_meta($lead_id, '_pi_lead_phone', $phone);
    update_post_meta($lead_id, '_pi_lead_service', 'Đăng ký bảo hành — ' . $address);
    update_post_meta($lead_id, '_pi_lead_message', $msg . ($date ? ' | Ngày bàn giao: ' . $date : ''));
    update_post_meta($lead_id, '_pi_lead_source', 'warranty_form');
    update_post_meta($lead_id, '_pi_lead_status', 'new');

    wp_send_json_success(['message' => 'Đăng ký bảo hành thành công! Chúng tôi sẽ liên hệ trong 24h.']);
}

// ════════════════════════════════════════
// H8: Emergency Contact Button
// Usage: [emergency_contact phone="0901234567"]
// ════════════════════════════════════════
add_shortcode('emergency_contact', function ($atts) {
    $a = shortcode_atts(['phone' => '', 'zalo' => ''], $atts);
    ob_start(); ?>
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
        <?php if ($a['phone']): ?>
        <a href="tel:<?php echo esc_attr($a['phone']); ?>"
           style="display:inline-flex;align-items:center;gap:10px;background:var(--danger, #ef4444);color:var(--on-brand, #fff);border-radius:999px;padding:14px 24px;font-weight:700;font-size:15px;text-decoration:none;animation:sghPulse 2s infinite;box-shadow:0 0 0 0 rgba(239,68,68,0.4);">
            🚨 Sự cố khẩn cấp — Gọi ngay: <?php echo esc_html($a['phone']); ?>
        </a>
        <?php endif; ?>
        <?php if ($a['zalo']): ?>
        <a href="https://zalo.me/<?php echo esc_attr($a['zalo']); ?>"
           target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;background:var(--sh-color-zalo, #0068ff);color:var(--on-brand, #fff);border-radius:999px;padding:14px 24px;font-weight:600;font-size:14px;text-decoration:none;">
            💬 Zalo 24/7
        </a>
        <?php endif; ?>
    </div>
    <style>
    @keyframes sghPulse{0%{box-shadow:0 0 0 0 rgba(239,68,68,0.4)}70%{box-shadow:0 0 0 12px rgba(239,68,68,0)}100%{box-shadow:0 0 0 0 rgba(239,68,68,0)}}
    </style>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// F11: Portfolio Filter (add CSS class + JS)
// Renders on pages that have portfolio grid
// ════════════════════════════════════════
add_shortcode('portfolio_filter', function ($atts) {
    $a = shortcode_atts(['terms' => '', 'taxonomy' => 'category'], $atts);
    $term_slugs = array_filter(array_map('trim', explode(',', $a['terms'])));
    if (empty($term_slugs)) {
        // Get all categories
        $terms = get_terms(['taxonomy' => $a['taxonomy'], 'hide_empty' => true]);
    } else {
        $terms = get_terms(['taxonomy' => $a['taxonomy'], 'slug' => $term_slugs, 'hide_empty' => true]);
    }
    if (is_wp_error($terms) || empty($terms)) return '';

    ob_start(); ?>
    <div class="sgh-portfolio-filters" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px;">
        <button class="sgh-pf-btn active" data-filter="*"
                style="padding:8px 18px;border-radius:999px;border:1px solid var(--brand, #F9C349);background:var(--brand, #F9C349);color:var(--on-brand, #000);font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;">
            Tất cả
        </button>
        <?php foreach ($terms as $term): ?>
        <button class="sgh-pf-btn" data-filter=".cat-<?php echo esc_attr($term->slug); ?>"
                style="padding:8px 18px;border-radius:999px;border:1px solid var(--border-default, #e5e7eb);background:var(--bg-card, #fff);color:var(--text-1-2, #475569);font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;"
                onmouseover="if(!this.classList.contains('active')){this.style.background='var(--bg, #f8fafc)'}"
                onmouseout="if(!this.classList.contains('active')){this.style.background='#fff'}">
            <?php echo esc_html($term->name); ?> (<?php echo $term->count; ?>)
        </button>
        <?php endforeach; ?>
    </div>
    <script>
    (function(){
        var btns = document.querySelectorAll('.sgh-pf-btn');
        btns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                btns.forEach(function(b) {
                    b.classList.remove('active');
                    b.style.background='#fff'; b.style.color='#475569'; b.style.borderColor='#e5e7eb';
                });
                this.classList.add('active');
                this.style.background='var(--brand, #F9C349)'; this.style.color='#000'; this.style.borderColor='var(--brand, #F9C349)';
                var filter = this.dataset.filter;
                document.querySelectorAll('.sgh-portfolio-item').forEach(function(item) {
                    if (filter === '*' || item.matches(filter)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    })();
    </script>
    <?php return ob_get_clean();
});

// ════════════════════════════════════════
// F12: Saved Favorites (LocalStorage)
// Usage: [favorites_toggle post_id="123"] on project cards
// ════════════════════════════════════════
add_shortcode('favorites_toggle', function ($atts) {
    $a = shortcode_atts(['post_id' => get_the_ID(), 'label' => ''], $atts);
    $pid = intval($a['post_id']);
    return '<button class="sgh-fav-btn" data-id="' . $pid . '" ' .
           'style="background:none;border:none;cursor:pointer;font-size:22px;transition:transform 0.2s;" ' .
           'title="Lưu yêu thích" onclick="sghToggleFav(this,' . $pid . ')">♡</button>' .
           '<script>
           (function(){
               var b = document.querySelector(\'.sgh-fav-btn[data-id="' . $pid . '"]\');
               var favs = JSON.parse(localStorage.getItem("sgh_favorites") || "[]");
               if(favs.indexOf(' . $pid . ') !== -1) b.textContent = "❤";
           })();
           </script>';
});

// Output favorites JS helper once
add_action('wp_footer', function () { ?>
<script>
function sghToggleFav(btn, id) {
    var favs = JSON.parse(localStorage.getItem('sgh_favorites') || '[]');
    var idx  = favs.indexOf(id);
    if (idx === -1) { favs.push(id); btn.textContent = '❤'; btn.style.transform='scale(1.3)'; }
    else            { favs.splice(idx, 1); btn.textContent = '♡'; btn.style.transform='scale(1)'; }
    localStorage.setItem('sgh_favorites', JSON.stringify(favs));
    setTimeout(function(){ btn.style.transform='scale(1)'; }, 300);
}
</script>
<?php });

// ════════════════════════════════════════
// H10: Review Request Automation (WP Cron)
// Sends review request email 30 days after lead status = 'won'
// ════════════════════════════════════════
if (!wp_next_scheduled('sgh_review_request_check')) {
    wp_schedule_event(time(), 'daily', 'sgh_review_request_check');
}

add_action('sgh_review_request_check', function () {
    $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));

    $leads = get_posts([
        'post_type'   => 'sh_lead',
        'post_status' => 'publish',
        'numberposts' => 20,
        'meta_query'  => [
            ['key' => '_pi_lead_status', 'value' => 'won'],
            ['key' => '_pi_review_requested', 'compare' => 'NOT EXISTS'],
        ],
        'date_query'  => [['before' => $thirty_days_ago]],
    ]);

    foreach ($leads as $lead) {
        $email = get_post_meta($lead->ID, '_pi_lead_email', true);
        $name  = get_post_meta($lead->ID, '_pi_lead_name', true);
        if (!$email || !is_email($email)) continue;

        $subject = 'Cảm ơn bạn đã tin tưởng SaigonHoreca! Chia sẻ đánh giá của bạn';
        $body    = "Xin chào $name,\n\nCảm ơn bạn đã sử dụng dịch vụ của SaigonHoreca.\n" .
                   "Bạn có thể dành 1 phút để đánh giá chúng tôi trên Google không?\n" .
                   "Link đánh giá: https://g.page/r/saigonhouse\n\nTrân trọng,\nTeam SaigonHoreca";

        wp_mail($email, $subject, $body);
        update_post_meta($lead->ID, '_pi_review_requested', current_time('mysql'));
    }
});

// ════════════════════════════════════════
// F15: Cookie Consent — 2 buttons (Accept / Decline)
// Disabled: 3-tier granular version. Simplified per client request.
// ════════════════════════════════════════
add_action('wp_head', function () {
    if (is_admin()) return;
    $ga_id    = get_option('sgh_ga_tracking_id', '');
    $fb_pixel = get_option('sgh_fb_pixel_id', '');
    // Only load tracking if user accepted cookies
    ?>
    <script>
    (function(){
        // Always load tracking — cookie consent is cosmetic only
        function afterConsent(cb) {
            try {
                if (localStorage.getItem('sh_cookies_accepted') === '1') {
                    cb();
                    return;
                }
            } catch (e) {}
            window.addEventListener('sh:cookies-accepted', cb, { once: true });
        }
        afterConsent(function(){
        <?php if ($ga_id): ?>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)};i[r].l=1*new Date();a=s.createElement(o);m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js($ga_id); ?>','gtag');
        window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js($ga_id); ?>');
        <?php endif; ?>
        <?php if ($fb_pixel): ?>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init','<?php echo esc_js($fb_pixel); ?>');fbq('track','PageView');
        <?php endif; ?>
        });
    })();
    </script>
    <?php
}, 5);

// ════════════════════════════════════════
// E9: Heatmap Integration Toggle (admin option)
// Embeds Hotjar/Clarity script if enabled via wp_options
// ════════════════════════════════════════
add_action('wp_head', function () {
    if (is_admin()) return;
    $clarity_id = get_option('sgh_clarity_id', '');
    if ($clarity_id) {
        echo '<script type="text/javascript">(function(){function l(){(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window,document,"clarity","script","' . esc_js($clarity_id) . '")}try{if(localStorage.getItem("sh_cookies_accepted")==="1"){l();return}}catch(e){}window.addEventListener("sh:cookies-accepted",l,{once:true})})();</script>' . "\n";
    }
}, 5);

// ════════════════════════════════════════
// Enqueue JS for frontend shortcodes (newsletter, warranty forms)
// ════════════════════════════════════════
add_action('wp_footer', function () {
    $ajax_url = admin_url('admin-ajax.php'); ?>
<script>
(function(){
    // Newsletter form
    document.querySelectorAll('.sgh-newsletter-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = form.querySelector('[name=email]').value;
            var msgEl = form.closest('.sgh-newsletter').querySelector('.sgh-newsletter-msg');
            msgEl.textContent = 'Đang xử lý...';
            fetch('<?php echo esc_url($ajax_url); ?>', {
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:'action=sgh_newsletter_subscribe&email=' + encodeURIComponent(email)
            }).then(r=>r.json()).then(function(res){
                msgEl.textContent = res.data?.message || (res.success ? 'Thành công!' : 'Lỗi.');
            });
        });
    });

    // Warranty form
    var wForm = document.getElementById('sgh-warranty');
    if (wForm) {
        wForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var data = new FormData(wForm);
            data.append('action', 'sgh_warranty_register');
            var msgEl = wForm.querySelector('.sgh-warranty-msg');
            msgEl.textContent = 'Đang gửi...';
            fetch('<?php echo esc_url($ajax_url); ?>', { method:'POST', body: new URLSearchParams(data) })
                .then(r=>r.json()).then(function(res){
                    msgEl.textContent = res.data?.message || (res.success ? 'Thành công!' : 'Lỗi.');
                    if (res.success) wForm.reset();
                });
        });
    }
})();
</script>
<?php });
