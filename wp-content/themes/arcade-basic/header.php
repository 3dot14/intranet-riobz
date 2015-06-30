<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <main>
 * and the left sidebar conditional
 *
 * @since 1.0.0
 */
?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" <?php language_attributes(); ?>><!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if IE]><script src="<?php echo BAVOTASAN_THEME_URL; ?>/library/js/html5.js"></script><![endif]-->
	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/library/calendar/css/calendar.css" type="text/css" media="screen" />
</head>
<?php
$bavotasan_theme_options = bavotasan_theme_options();
$space_class = '';
?>
<body <?php body_class(); ?>>

	<div id="page">

		<header id="header">
			<nav id="site-navigation" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<h3 class="sr-only"><?php _e( 'Main menu', 'arcade' ); ?></h3>
				<a class="sr-only" href="#primary" title="<?php esc_attr_e( 'Skip to content', 'arcade' ); ?>"><?php _e( 'Skip to content', 'arcade' ); ?></a>

				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				    </button>
				</div>

				<div class="collapse navbar-collapse">
					<?php
					$menu_class = ( is_rtl() ) ? ' navbar-right' : '';
					wp_nav_menu( array( 'theme_location' => 'primary', 'container' => '', 'menu_class' => 'nav navbar-nav' . $menu_class, 'fallback_cb' => 'bavotasan_default_menu' ) );
					?>
				</div>
			</nav><!-- #site-navigation -->

			 <div class="title-card-wrapper">
                <div class="title-card">
    				<div id="site-meta">
    					<h1 id="">
    						<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php //bloginfo( 'name' ); ?> <img src="<?= esc_url( home_url() );?>/wp-content/uploads/2015/06/Riobzlogo.png" width="15%" alt=""></a>
    					
    					</h1>

    					<?php /*if ( $bavotasan_theme_options['header_icon'] ) { ?>
    					<i class="fa <?php echo $bavotasan_theme_options['header_icon']; ?>"></i>
    					<?php } else {
    						$space_class = ' class="margin-top"';
    					} */?>

    					<h2 id="site-description"<?php echo $space_class; ?>>
    						<?php bloginfo( 'description' ); ?>
    					</h2>
						<?php
						/**
						 * You can overwrite the defeault 'See More' text by defining the 'BAVOTASAN_SEE_MORE'
						 * constant in your child theme's function.php file.
						 */
						if ( ! defined( 'BAVOTASAN_SEE_MORE' ) )
							define( 'BAVOTASAN_SEE_MORE', __( 'See More', 'arcade' ) );
						global $current_user;
						get_currentuserinfo();
						$usuario = esc_attr($current_user->user_login);
						if($usuario==null){
							
						?> 
							<a href="index.php/login" id="more-site" class="btn btn-default btn-lg">Log in</a>	
						<?php
						} 
						else{
						?>
    						<a href="#" id="more-site" class="btn btn-default btn-lg">Ver mas</a>
    					<?php } ?>
    				</div>

    				<?php
    				// Header image section
    				//bavotasan_header_images();
    				?>

    				<img class="header-img" alt="" src="<?= esc_url( home_url() );?>/wp-content/uploads/2015/06/cropped-login-background.png" style="position: absolute; left: 0px; top: -283px; right: auto; bottom: auto; height: auto; width: 100%;">
				</div>
			</div>

		</header>

		<main>
			<br>
			<div class="row">
				<div class="col-md-3 col-md-offset-8">
					<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url() ); ?>">
						<label>
							<span class="sr-only"><?php _ex( 'Search for:', 'label', 'arcade' ); ?></span>
							<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'arcade' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
						</label>
					</form>
				</div>
			</div>