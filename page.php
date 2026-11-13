<?php
get_header();
?>
<main class="snd-wrap snd-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'snd-article' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="snd-content"><?php the_content(); ?></div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
