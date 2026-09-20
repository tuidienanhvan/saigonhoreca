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

$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

// Nhãn "Reply to" của core → nhãn của theme, qua gettext.
// Site KHÔNG có gói ngôn ngữ core (không có wp-content/languages, WPLANG rỗng) nên core
// luôn in tiếng Anh "Reply to NAME" — filter này là bản dịch DUY NHẤT, gỡ đi là site tiếng
// Việt hiện tiếng Anh. Nhưng bản cũ gõ cứng "Trả lời", nên trang /en (theme nạp en_US.mo)
// cũng bị ép tiếng Việt. Đi qua __() thì trang tiếng Việt y như cũ, còn /en dịch được
// ngay khi msgid "Trả lời" có trong languages/en_US.po.
if (!has_filter('comment_reply_link', 'sgh_comment_reply_label')) {
    function sgh_comment_reply_label($link) {
        $nhan = esc_attr__('Trả lời', 'saigonhoreca') . ' ';
        return str_replace(
            ['data-replyto="Reply to ', 'aria-label="Reply to '],
            ['data-replyto="' . $nhan, 'aria-label="' . $nhan],
            $link
        );
    }
    add_filter('comment_reply_link', 'sgh_comment_reply_label', 10, 1);
}

// Multilingual time ago helper
function sh_time_ago($timestamp) {
    $lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';
    $diff = current_time('timestamp') - $timestamp;

    if ($lang === 'ja') {
        if ($diff < 60) return 'たった今';
        if ($diff < 3600) return floor($diff / 60) . '分前';
        if ($diff < 86400) return floor($diff / 3600) . '時間前';
        if ($diff < 604800) return floor($diff / 86400) . '日前';
        return wp_date('Y/m/d H:i', $timestamp);
    }

    if ($lang === 'en') {
        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' mins ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        return wp_date('M j, Y \a\t H:i', $timestamp);
    }

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

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) :
            $older_text = ($lang === 'ja') ? '← 前のコメント' : (($lang === 'en') ? '← Older Comments' : '← Cũ hơn');
            $newer_text = ($lang === 'ja') ? '次のコメント →' : (($lang === 'en') ? 'Newer Comments →' : 'Mới hơn →');
        ?>
            <nav style="display:flex;justify-content:space-between;margin-top:2rem;font-size:0.875rem">
                <div><?php previous_comments_link($older_text); ?></div>
                <div><?php next_comments_link($newer_text); ?></div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
        $closed_text = ($lang === 'ja') ? 'この記事のコメントは締め切られました。' : (($lang === 'en') ? 'Comments are closed for this article.' : 'Bình luận đã đóng cho bài viết này.');
    ?>
        <p class="sh-comments__closed"><?php echo esc_html($closed_text); ?></p>
    <?php endif; ?>

    <?php
    if (comments_open()) :
        wp_enqueue_script('comment-reply');

        $c_title_reply    = ($lang === 'ja') ? 'コメントを残す' : (($lang === 'en') ? 'Leave a Comment' : 'Để lại bình luận');
        $c_title_reply_to = ($lang === 'ja') ? '%s に返信' : (($lang === 'en') ? 'Reply to %s' : 'Trả lời %s');
        $c_cancel_link    = ($lang === 'ja') ? '✕ キャンセル' : (($lang === 'en') ? '✕ Cancel' : '✕ Hủy');
        $c_label_submit   = ($lang === 'ja') ? 'コメントを送信' : (($lang === 'en') ? 'Submit Comment' : 'Gửi bình luận');
        $c_label_author   = ($lang === 'ja') ? 'お名前' : (($lang === 'en') ? 'Full Name' : 'Họ tên');
        $c_ph_author      = ($lang === 'ja') ? '山田 太郎' : (($lang === 'en') ? 'John Doe' : 'Nhập họ tên');
        $c_label_comment  = ($lang === 'ja') ? 'コメント' : (($lang === 'en') ? 'Comment' : 'Bình luận');
        $c_ph_comment     = ($lang === 'ja') ? 'コメントを入力してください...' : (($lang === 'en') ? 'Write your comment...' : 'Viết bình luận của bạn...');

        echo '<div>';
        comment_form([
            'id_form'              => 'commentform',
            'class_container'      => 'sh-comment-form__wrapper',
            'title_reply'          => $c_title_reply,
            'title_reply_to'       => $c_title_reply_to,
            'title_reply_before'   => '<h3 id="reply-title" class="sh-comment-form__title">',
            'title_reply_after'    => '</h3>',
            'cancel_reply_before'  => ' <small class="sh-comment-form__cancel">',
            'cancel_reply_after'   => '</small>',
            'cancel_reply_link'    => $c_cancel_link,
            'label_submit'         => $c_label_submit,
            'submit_button'        => '<button type="submit" id="%2$s" class="sh-comment-form__submit">' . sh_icon('send', 'sh-comment-form__send-icon') . ' %4$s</button>',
            'submit_field'         => '<div class="form-submit" style="margin-top:1rem">%1$s %2$s</div>',
            'logged_in_as'         => '',
            'comment_notes_before' => '',
            'comment_notes_after'  => '',
            'class_form'           => 'sh-comment-form',
            'comment_field'        => '
                <div class="sh-comment-form__fields">
                    <div class="sh-comment-form__group">
                        <label for="author" class="sh-comment-form__label">' . esc_html($c_label_author) . ' <span>*</span></label>
                        <input id="author" name="author" type="text" required class="sh-comment-form__input" value="' . esc_attr($commenter['comment_author']) . '" placeholder="' . esc_attr($c_ph_author) . '">
                    </div>
                    <div class="sh-comment-form__group">
                        <label for="email" class="sh-comment-form__label">Email <span>*</span></label>
                        <input id="email" name="email" type="email" required class="sh-comment-form__input" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="email@example.com">
                    </div>
                    <div class="sh-comment-form__group sh-comment-form__group--full">
                        <label for="comment" class="sh-comment-form__label">' . esc_html($c_label_comment) . ' <span>*</span></label>
                        <textarea id="comment" name="comment" required rows="4" class="sh-comment-form__textarea" placeholder="' . esc_attr($c_ph_comment) . '"></textarea>
                    </div>
                </div>',
            'fields' => [],
        ]);
        echo '</div>';
    endif;
    ?>

</section>
