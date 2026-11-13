<?php
get_header();
?>
<main class="snd-wrap snd-main">
	<article class="snd-article" style="text-align:center">
		<h1>Page introuvable</h1>
		<p>La page demandée n'existe pas ou a été déplacée.</p>
		<p><a class="snd-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Retour à l'accueil</a></p>
	</article>
</main>
<?php
get_footer();
