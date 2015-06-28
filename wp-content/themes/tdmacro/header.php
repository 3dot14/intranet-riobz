<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package tdMacro
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<div class="container clearfix">
			<div class="site-branding pull-left">
				<?php if ( get_header_image() ) : ?>
				<div class="website-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<img src="<?php echo esc_url( get_header_image() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
					</a>
				</div><!-- .website-logo -->
				<?php endif;?>
				
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> Holi</h1>
				<h2 class="site-description"><?php bloginfo( 'description' ); ?> ¿Estoy pintado o que?</h2>
			</div><!-- .site-branding -->
			<div class="header-controls pull-right">
				<?php get_template_part( 'menu', 'social' ); ?>
			</div><!-- .header-controls -->
		</div><!-- .container -->
	</header><!-- #masthead -->

	<div class="container clearfix">
		<nav id="site-navigation" class="main-navigation" role="navigation" data-small-nav-title="<?php esc_attr_e( 'Navigation', 'tdmacro' ); ?>">
		<?php if ( has_nav_menu( 'primary' ) ): ?>
			<?php wp_nav_menu( array( 'container' => 'ul', 'menu_class' => 'nav-bar clearfix', 'theme_location' => 'primary') ); ?>
		<?php else: ?>
			<ul class="list-unstyled nav-bar clearfix">
				<?php wp_list_pages('title_li=' ); ?>
			</ul><!-- .nav-bar -->
		<?php endif; ?>
		</nav><!-- #site-navigation -->
	</div><!-- .container -->

	<div id="content" class="site-content">
