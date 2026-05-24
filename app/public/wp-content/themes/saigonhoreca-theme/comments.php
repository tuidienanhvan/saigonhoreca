<?php
/**
 * Comments Template — SaigonHoreca
 *
 * - Always shows name + email fields (even when logged in)
 * - No login required
 * - Nested replies with indent
 * - Vietnamese text
 * - Dark mode via var(--*)
 */
if (post_password_required()) return;

// Force Vietnamese text in reply link attributes
add_filter('comment_reply_link', function($link) {
    $link = str_replace('data-replyto="Reply to ', 'data-replyto="Trả lời ', $link);
    $link = str_replace('aria-label="Reply to ', 'aria-label="Trả lời ', $link);
    return $link;
}, 10, 1);

// Vietnamese time ago helper
function sh_time_ago($timestamp) {
    $diff = current_time('timestamp') - $timestamp;
    if ($diff < 60) return 'vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    return wp_date('d/m/Y', $timestamp) . ' lúc ' . wp_date('H:i', $timestamp);
}

// Pre-fill from cookie or logged-in user
$commenter = wp_get_current_commenter();
$user = wp_get_current_user();
if ($user->exists()) {
    $commenter['comment_author']       = $commenter['comment_author'] ?: $user->display_name;
    $commenter['comment_author_email'] = $commenter['comment_author_email'] ?: $user->user_email;
}
?>

<section id="comments" class="sh-comments">

    <?php if (have_comments()) : ?>
        <h2 class="sh-comments__title">
            <?php echo sh_icon('message-circle', 'sh-comments__title-icon'); ?>
            <?php printf(_n('%s bình luận', '%s bình luận', get_comments_number(), 'saigonhoreca'), number_format_i18n(get_comments_number())); ?>
        </h2>

        <ul class="sh-comments__list">
            <?php wp_list_comments([
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 48,
                'max_depth'   => 3,
                'callback'    => function($comment, $args, $depth) {
                    ?>
                    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('sh-comment'); ?>>
                        <div class="sh-comment__inner">
                            <div class="sh-comment__avatar"><?php echo get_avatar($comment, 48); ?></div>
                            <div class="sh-comment__body">
                                <div class="sh-comment__meta">
                                    <span class="sh-comment__author"><?php comment_author(); ?></span>
                                    <span class="sh-comment__date"><?php echo esc_html(sh_time_ago(get_comment_time('U'))); ?></span>
                                </div>
                                <div class="sh-comment__text"><?php comment_text(); ?></div>
                                <?php if ($comment->comment_approved == '0') : ?>
                                    <p style="font-size:0.75rem;color:var(--warning,#d97706);margin-top:0.5rem;font-style:italic">Bình luận đang chờ duyệt.</p>
                                <?php endif; ?>
                                <div class="sh-comment__actions">
                                    <?php comment_reply_link(array_merge($args, [
                                        'depth'      => $depth,
                                        'max_depth'  => $args['max_depth'],
                                        'reply_text' => sh_icon('corner-up-left', 'sh-comment__reply-icon') . ' Trả lời',
                                    ])); ?>
                                </div>
                            </div>
                        </div>
                    <?php
                },
            ]); ?>
        </ul>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav style="display:flex;justify-content:space-between;margin-top:2rem;font-size:0.875rem">
                <div><?php previous_comments_link('← Cũ hơn'); ?></div>
                <div><?php next_comments_link('Mới hơn →'); ?></div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="sh-comments__closed">Bình luận đã đóng cho bài viết này.</p>
    <?php endif; ?>

    <?php
    if (comments_open()) :
        wp_enqueue_script('comment-reply');

        echo '<div>';
        comment_form([
            'id_form'              => 'commentform',
            'class_container'      => 'sh-comment-form__wrapper',
            'title_reply'          => 'Để lại bình luận',
            'title_reply_to'       => 'Trả lời %s',
            'title_reply_before'   => '<h3 id="reply-title" class="sh-comment-form__title">',
            'title_reply_after'    => '</h3>',
            'cancel_reply_before'  => ' <small class="sh-comment-form__cancel">',
            'cancel_reply_after'   => '</small>',
            'cancel_reply_link'    => '✕ Hủy',
            'label_submit'         => 'Gửi bình luận',
            'submit_button'        => '<button type="submit" id="%2$s" class="sh-comment-form__submit">' . sh_icon('send', 'sh-comment-form__send-icon') . ' %4$s</button>',
            'submit_field'         => '<div class="form-submit" style="margin-top:1rem">%1$s %2$s</div>',
            'logged_in_as'         => '',
            'comment_notes_before' => '',
            'comment_notes_after'  => '',
            'class_form'           => 'sh-comment-form',
            'comment_field'        => '
                <div class="sh-comment-form__fields">
                    <div class="sh-comment-form__group">
                        <label for="author" class="sh-comment-form__label">Họ tên <span>*</span></label>
                        <input id="author" name="author" type="text" required class="sh-comment-form__input" value="' . esc_attr($commenter['comment_author']) . '" placeholder="Nhập họ tên">
                    </div>
                    <div class="sh-comment-form__group">
                        <label for="email" class="sh-comment-form__label">Email <span>*</span></label>
                        <input id="email" name="email" type="email" required class="sh-comment-form__input" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="email@example.com">
                    </div>
                    <div class="sh-comment-form__group sh-comment-form__group--full">
                        <label for="comment" class="sh-comment-form__label">Bình luận <span>*</span></label>
                        <textarea id="comment" name="comment" required rows="4" class="sh-comment-form__textarea" placeholder="Viết bình luận của bạn..."></textarea>
                    </div>
                </div>',
            'fields' => [],
        ]);
        echo '</div>';
    endif;
    ?>

</section>
