<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #5: specs — sophistication narrative + equipment / feature spec cards.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$pp_nichiyo_specs = array(
    array(
        'coord' => 'SYS_01',
        'title' => __('Bố cục tối giản', 'saigonhoreca'),
        'desc'  => __('Tông trắng – xám trung tính, sắp xếp thiết bị thông minh tối ưu hóa không gian.', 'saigonhoreca'),
    ),
    array(
        'coord' => 'SYS_02',
        'title' => __('Thông gió tỉ mỉ', 'saigonhoreca'),
        'desc'  => __('Hệ thống thông gió thiết kế kỹ lưỡng giữ không gian thông thoáng, thoải mái.', 'saigonhoreca'),
    ),
    array(
        'coord' => 'SYS_03',
        'title' => __('Ánh sáng tự nhiên', 'saigonhoreca'),
        'desc'  => __('Tận dụng cửa sổ lớn kết hợp đèn LED điều chỉnh cường độ cho không gian sáng sủa.', 'saigonhoreca'),
    ),
    array(
        'coord' => 'SYS_04',
        'title' => __('Inox tiêu chuẩn', 'saigonhoreca'),
        'desc'  => __('Vật dụng thép không gỉ thiết kế đặc biệt, bền bỉ và dễ vệ sinh theo chuẩn công nghiệp.', 'saigonhoreca'),
    ),
);
?>
<section class="pp-section-nichiyo pp-section-nichiyo--alt pp-specs-nichiyo scroll-reveal reveal-fade">
  <div class="pp-nichiyo-glow pp-nichiyo-glow--tr" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-specs-nichiyo__head">
      <span class="pp-text-nichiyo__divider pp-text-nichiyo__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-nichiyo__title"><?php echo esc_html__('Sự Tinh Tế của Saigon Horeca Trong Thiết Kế Bếp Căng Tin', 'saigonhoreca'); ?></h2>
      <div class="pp-text-nichiyo__body">
        <p><?php echo esc_html__('Saigon Horeca thể hiện sự tinh tế trong thiết kế bếp căng tin thông qua sự kết hợp hài hòa giữa đơn giản và tiện ích. Với tầm nhìn sáng tạo, không gian bếp được tạo ra với phong cách tối giản nhưng vẫn đảm bảo đầy đủ các tiện nghi và công nghệ hiện đại.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Mỗi chi tiết trong bếp được chăm chút tỉ mỉ, từ việc lựa chọn màu sắc trung tính như trắng và xám đến việc bố trí thiết bị và vật dụng bếp một cách thông minh và tiện lợi. Sự sắp xếp hợp lý giúp tối ưu hóa không gian và tạo ra một môi trường làm việc thoải mái và tiện nghi.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Bên cạnh đó, ánh sáng tự nhiên được tận dụng thông qua các cửa sổ lớn, tạo ra không gian sáng sủa và thoáng đãng. Các đèn led có thể điều chỉnh giúp làm tăng tính tiện ích và thẩm mỹ cho căn bếp. Việc sử dụng ánh sáng tự nhiên và đèn led điều chỉnh cường độ không chỉ làm tăng tính thẩm mỹ mà còn tạo ra một không gian làm việc sáng sủa và tạo động lực cho nhân viên.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Tất cả những yếu tố trên thể hiện sự tinh tế và am hiểu sâu sắc về nhu cầu của khách hàng, giúp Saigon Horeca định vị mình là một đối tác đáng tin cậy trong việc tạo ra những không gian bếp đẳng cấp và hiện đại, đáp ứng mọi nhu cầu của khách hàng, đặc biệt là trong lĩnh vực bếp căng tin.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-specs-nichiyo__grid">
      <?php foreach ($pp_nichiyo_specs as $i => $spec) : ?>
        <article class="pp-specs-nichiyo__card scroll-reveal reveal-up delay-<?php echo esc_attr((string) (($i + 1) * 100)); ?>">
          <span class="pp-specs-nichiyo__coord" aria-hidden="true"><?php echo esc_html($spec['coord']); ?></span>
          <h3 class="pp-specs-nichiyo__card-title"><?php echo esc_html($spec['title']); ?></h3>
          <p class="pp-specs-nichiyo__card-desc"><?php echo esc_html($spec['desc']); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
