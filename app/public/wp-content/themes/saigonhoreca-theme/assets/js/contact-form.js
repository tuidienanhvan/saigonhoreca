document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('saigonhouse-contact-form');
    if (!contactForm) {
        return;
    }

    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector('span');
    const originalText = btnText.innerText;

    function gtmPush(data) {
        if (typeof dataLayer !== 'undefined') {
            dataLayer.push(data);
        }
    }

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
                this.setCustomValidity('Số điện thoại không đúng định dạng VN (VD: 0961 868 968)');
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
            if (!validatePhone(phoneVal)) {
                gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: 'validation', error_field: 'phone' });
                phoneInput.setCustomValidity('Số điện thoại không đúng định dạng VN (VD: 0961 868 968)');
                phoneInput.reportValidity();
                return;
            }
        }

        statusDiv.classList.add('hidden');
        statusDiv.className = 'mt-4 text-sm font-medium hidden p-4 rounded-xl text-center';

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        btnText.innerText = 'Đang gửi...';

        const formData = new FormData(contactForm);
        formData.append('action', 'sh_submit_contact_form');

        const securityField = document.getElementById('sh_contact_nonce');
        if (securityField) {
            formData.append('security', securityField.value);
        }

        fetch(saigonhouse_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                statusDiv.classList.remove('hidden');
                if (data.success) {
                    statusDiv.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
                    statusDiv.innerText = 'Cảm ơn bạn! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.';
                    contactForm.reset();
                    formStartFired = false;
                    gtmPush({ event: 'contact_form_success', form_id: 'contact-form' });
                } else {
                    statusDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
                    statusDiv.innerText = 'Có lỗi xảy ra: ' + (data.data || 'Vui lòng thử lại sau.');

                    const errMsg = (data.data || '').toLowerCase();
                    const errType = errMsg.includes('nhiều') ? 'rate_limit' : 'server';
                    gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: errType });
                }
            })
            .catch(function () {
                statusDiv.classList.remove('hidden');
                statusDiv.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
                statusDiv.innerText = 'Lỗi kết nối. Vui lòng thử lại.';
                gtmPush({ event: 'form_error', form_id: 'contact-form', error_type: 'network' });
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                btnText.innerText = originalText;
            });
    });

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
        }
    });
});
