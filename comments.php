<?php
if ( post_password_required() ) {
	return;
}
if ( have_comments() ) :
	?>
	<div class="snd-section-title"><h2>Commentaires (<?php echo esc_html( get_comments_number() ); ?>)</h2></div>
	<ol><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 40 ) ); ?></ol>
	<?php
	the_comments_pagination();
endif;
comment_form();
