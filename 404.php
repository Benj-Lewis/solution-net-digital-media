<?php
get_header();
?>
<main class="snd-wrap snd-404">
	<p class="snd-404-code">404</p>
	<h1>Cette page est introuvable</h1>
	<p>Elle a peut-être été déplacée ou supprimée. Essayez une recherche ou revenez à l'accueil.</p>
	<?php get_search_form(); ?>
	<p><a class="snd-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l'accueil</a></p>
</main>
<?php
get_footer();
