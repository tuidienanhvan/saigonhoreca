<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-trd">
    <div class="pp-hero-trd__media" style="background-image:url('<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-mat-tien-nha-hang.webp'); ?>'); background-size:cover; background-position:center;" data-ken-burns></div>
    <div class="pp-hero-trd__overlay" aria-hidden="true"></div>
    <div class="pp-hero-trd__content">
        <!-- Royal Glassmorphism Architectural Card -->
        <div class="pp-hero-trd__card">
            <!-- Royal Crown & Serif R Emblem Logo in SVG -->
            <div class="pp-hero-trd__emblem" aria-hidden="true">
                <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Vương miện hoàng gia vàng gold tinh xảo -->
                    <path d="M38 28 L44 33 L50 20 L56 33 L62 28 L59 40 H41 Z" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linejoin="round"/>
                    <circle cx="38" cy="26" r="1.5" fill="var(--gold)"/>
                    <circle cx="50" cy="18" r="1.8" fill="var(--gold)"/>
                    <circle cx="62" cy="26" r="1.5" fill="var(--gold)"/>
                    <path d="M39 42 H61" stroke="var(--gold)" stroke-width="1.5" stroke-linecap="round"/>

                    <!-- Chữ R Serif hoàng gia to lớn kiêu hãnh ở dưới -->
                    <text x="51" y="78" font-family="'Playfair Display', 'Didot', 'Georgia', serif" font-size="42" font-weight="900" fill="var(--gold)" text-anchor="middle">R</text>
                </svg>
            </div>

            <h1 class="pp-hero-trd__title">THE ROYAL</h1>
            <span class="pp-hero-trd__tagline">ALL DAY DINING</span>
            
            <div class="pp-hero-trd__gold-divider" aria-hidden="true"></div>

            <h2 class="pp-hero-trd__subhead"><?php echo esc_html__('Sự giao thoa giữa tinh hoa phương Tây và tâm hồn Việt', 'saigonhoreca'); ?></h2>

            <!-- Location Metadata Tag with SVG Location Pin -->
            <div class="pp-hero-trd__location-tag">
                <svg width="12" height="15" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: var(--gold); display: block;">
                    <path d="M6 0C2.69 0 0 2.69 0 6C0 10.5 6 16 6 16C6 16 12 10.5 12 6C12 2.69 9.31 0 6 0ZM6 8.25C4.76 8.25 3.75 7.24 3.75 6C3.75 4.76 4.76 3.75 6 3.75C7.24 3.75 8.25 4.76 8.25 6C8.25 7.24 7.24 8.25 6 8.25Z" fill="currentColor"/>
                </svg>
                <span>41-47 Dong Du, Ben Nghe Ward, District 1, HCMC</span>
            </div>
        </div>
    </div>
</section>
