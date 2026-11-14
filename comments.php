<?php
if ( post_password_required() ) {
	return;
}
?>
<section class="snd-comments" id="commentaires">
	<?php if ( have_comments() ) : ?>
		<h2 class="snd-comments-title"><?php echo esc_html( sprintf( _n( '%s commentaire', '%s commentaires', get_comments_number(), 'snd-media' ), number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol class="snd-comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 40 ) ); ?></ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php
	comment_form(
		array(
			'title_reply'        => 'Réagir à cet article',
			'title_reply_before' => '<h2 class="snd-comments-title">',
			'title_reply_after'  => '</h2>',
			'label_submit'       => 'Publier le commentaire',
			'class_submit'       => 'snd-btn',
		)
	);
	?>
</section>
