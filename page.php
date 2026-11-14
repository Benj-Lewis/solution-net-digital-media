<?php
get_header();
?>
<main class="snd-wrap snd-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'snd-page-article' ); ?>>
			<header class="snd-page-head">
				<nav class="snd-crumbs" aria-label="Fil d'Ariane"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a><span>/</span><?php the_title(); ?></nav>
				<h1 class="snd-article-title"><?php the_title(); ?></h1>
			</header>
			<div class="snd-prose snd-prose--page"><?php the_content(); ?></div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
