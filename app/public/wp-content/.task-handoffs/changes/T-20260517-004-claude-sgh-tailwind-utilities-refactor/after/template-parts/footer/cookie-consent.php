<?php
/**
 * Cookie Consent Banner
 * Minimal, elegant cookie consent bar. Saves choice in localStorage.
 *
 * @package SaigonHouse
 */
?>
<div id="sh-cookie-consent" class="sh-cookie-bar" role="dialog" aria-label="Cookie consent" aria-live="polite">
    <div class="sh-cookie-bar-inner">
        <p>
            Chúng tôi sử dụng cookie để cải thiện trải nghiệm của bạn trên website. Bằng cách tiếp tục sử dụng trang web, bạn đồng ý với <a href="/chinh-sach-bao-mat">chính sách cookie</a> của chúng tôi.
        </p>
        <div class="sh-cookie-btns">
            <button class="sh-cookie-accept" aria-label="Chấp nhận cookie">Chấp nhận</button>
            <button class="sh-cookie-decline" aria-label="Từ chối cookie">Từ chối</button>
        </div>
    </div>
</div>
<script>
(function(){
    if (localStorage.getItem('sh_cookies_accepted') !== null) return;
    var el = document.getElementById('sh-cookie-consent');
    if (el) el.style.display = 'block';

    function hide() { if (el) el.style.display = 'none'; }

    var accept = el && el.querySelector('.sh-cookie-accept');
    var decline = el && el.querySelector('.sh-cookie-decline');

    if (accept) accept.addEventListener('click', function() {
        localStorage.setItem('sh_cookies_accepted', '1');
        hide();
    });
    if (decline) decline.addEventListener('click', function() {
        localStorage.setItem('sh_cookies_accepted', '0');
        hide();
    });
})();
</script>
