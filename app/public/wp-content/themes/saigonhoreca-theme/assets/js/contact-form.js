document.addEventListener('DOMContentLoaded', function () {
    function gtmPush(data) {
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push(data);
        }
    }

    // Site-wide GTM tracking for Floating action buttons (Zalo, Phone)
    document.addEventListener('click', function (e) {
        const tracked = e.target.closest('[data-gtm-click]');
        if (!tracked) {
            return;
        }
        const action = tracked.getAttribute('data-gtm-click');
        const location = tracked.getAttribute('data-gtm-location') || 'floating_button';

        if (action === 'click_phone') {
            gtmPush({ event: 'click_phone', location: location });
        } else if (action === 'click_zalo') {
            gtmPush({ event: 'click_zalo', location: location });
        } else if (action === 'click_facebook') {
            gtmPush({ event: 'click_facebook', location: location });
        } else if (action === 'click_instagram') {
            gtmPush({ event: 'click_instagram', location: location });
        } else if (action === 'click_youtube') {
            gtmPush({ event: 'click_youtube', location: location });
        }
    });

    const contactForm = document.getElementById('saigonhouse-contact-form') || document.querySelector('.sh-contact-form__native');
    if (!contactForm) {
        return;
    }

    // Detect language from html tag
    const docLang = (document.documentElement.lang || 'vi').toLowerCase();
    const lang = docLang.startsWith('ja') ? 'ja' : (docLang.startsWith('en') ? 'en' : 'vi');

    const i18n = {
        vi: {
            sending: 'Đang gửi...',
            success: 'Cảm ơn bạn! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.',
            error_prefix: 'Có lỗi xảy ra: ',
            error_default: 'Vui lòng thử lại sau.',
            network_error: 'Lỗi kết nối. Vui lòng thử lại.',
            invalid_phone: 'Số điện thoại không đúng định dạng VN (VD: 0901 304 365)'
        },
        en: {
            sending: 'Sending...',
            success: 'Thank you! We will get back to you as soon as possible.',
            error_prefix: 'An error occurred: ',
            error_default: 'Please try again later.',
            network_error: 'Network connection error. Please try again.',
            invalid_phone: 'Invalid phone format (e.g. 0901 304 365)'
        },
        ja: {
            sending: '送信中...',
            success: 'お問い合わせありがとうございます。担当者より近日中にご連絡いたします。',
            error_prefix: 'エラーが発生しました: ',
            error_default: 'しばらくしてからもう一度お試しください。',
            network_error: '接続エラーが発生しました。もう一度お試しください。',
            invalid_phone: '電話番号の形式が正しくありません (例: 0901 304 365)'
        }
    }[lang];

    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const btnText = submitBtn ? (submitBtn.querySelector('span') || submitBtn) : null;
    const originalText = btnText ? btnText.innerText : '';

    if ('IntersectionObserver' in window) {
        const formObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    gtmPush({ event: 'form_view', form_id: 'contact-form' });
                    formObserver.unobserve(contactForm);
                }
            });
        }, { threshold: 0.3 });
        formObserver.observe(contactForm);
    }

    let formStartFired = false;
    contactForm.querySelectorAll('input, textarea, select').forEach(function (field) {
        field.addEventListener('input', function () {
            if (!formStartFired) {
                formStartFired = true;
                gtmPush({ event: 'form_start', form_id: 'contact-form' });
            }
        }, { once: false });
    });

    const phoneRegex = /^(0|\+84)(3[2-9]|5[6-9]|7[0-9]|8[1-9]|9[0-9])[0-9]{7}$/;

    function validatePhone(value) {
        return phoneRegex.test(value.replace(/[\s-]/g, ''));
    }

    const phoneInput = contactForm.querySelector('[name="contact_phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function () {
            const val = this.value.trim();
            if (val && !validatePhone(val)) {
                this.setCustomValidity(i18n.invalid_phone);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
        phoneInput.addEventListener('input', function () {
            this.setCustomValidity('');
        });
    }

    let statusDiv = document.getElementById('form-status-message');
    if (!statusDiv) {
        statusDiv = document.createElement('div');
        statusDiv.id = 'form-status-message';
        statusDiv.className = 'mt-4 text-sm font-medium hidden p-4 rounded-xl text-center';
        statusDiv.setAttribute('role', 'status');
        statusDiv.setAttribute('aria-live', 'assertive');
        contactForm.appendChild(statusDiv);
    }

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (phoneInput) {
            const phoneVal = phoneInput.value.trim();
            if (phoneVal && !validatePhone(phoneVal)) {
                gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: 'validation', error_field: 'phone' });
                phoneInput.setCustomValidity(i18n.invalid_phone);
                phoneInput.reportValidity();
                return;
            }
        }

        statusDiv.classList.add('hidden');
        statusDiv.className = 'mt-4 text-sm font-medium hidden p-4 rounded-xl text-center';

        if (submitBtn && btnText) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            btnText.innerText = i18n.sending;
        }

        const formData = new FormData(contactForm);
        formData.append('action', 'sh_submit_contact_form');

        const securityField = document.getElementById('sh_contact_nonce');
        if (securityField) {
            formData.append('security', securityField.value);
        }

        const ajaxUrl = (window.SH_DATA && window.SH_DATA.ajax_url) || '/wp-admin/admin-ajax.php';
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                statusDiv.classList.remove('hidden');
                if (data.success) {
                    statusDiv.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                    statusDiv.innerText = i18n.success;
                    contactForm.reset();
                    formStartFired = false;
                    gtmPush({ event: 'contact_form_success', form_id: 'contact-form' });
                } else {
                    statusDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
                    statusDiv.innerText = i18n.error_prefix + (data.data || i18n.error_default);

                    const errMsg = (data.data || '').toLowerCase();
                    const errType = errMsg.includes('nhiều') ? 'rate_limit' : 'server';
                    gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: errType });
                }
            })
            .catch(function () {
                statusDiv.classList.remove('hidden');
                statusDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
                statusDiv.innerText = i18n.network_error;
                gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: 'network' });
            })
            .finally(function () {
                if (submitBtn && btnText) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    btnText.innerText = originalText;
                }
            });
    });
});
