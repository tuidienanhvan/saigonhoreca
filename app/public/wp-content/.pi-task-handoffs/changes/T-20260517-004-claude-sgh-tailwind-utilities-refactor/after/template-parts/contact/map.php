<?php
/**
 * Template Part: Contact Map
 *
 * @package SaigonHouse
 */
?>
<?php
$_pid = get_queried_object_id();
$_map_hours = get_post_meta($_pid, '_sgh_map_hours', true) ?: 'Thứ 2 - Thứ 7: 8:00 - 17:30';
?>
<section class="sh-map" data-aos="fade-up">

    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.9245742886745!2d106.68742637570415!3d10.817088958444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528e1f26d7351%3A0x4632551475da4a6d!2zMjNEIE5ndXnDqm4gSOG7k25nLCBQaMaw4budbmcgMSwgR8OyIFbhuqVwLCBI4buTIENow60gTWluaCwgTUluaA!5e0!3m2!1sen!2s!4v1715678900000!5m2!1sen!2s"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Bản đồ Saigon House">
    </iframe>

    <div class="sh-map__card">
        <div class="sh-map__icon-wrap">
            <?php echo sh_icon('help-circle', 'sh-map__icon'); ?>
        </div>
        <div>
            <span class="sh-map__label">Giờ làm việc</span>
            <span class="sh-map__hours"><?php echo esc_html($_map_hours); ?></span>
        </div>
    </div>
</section>
