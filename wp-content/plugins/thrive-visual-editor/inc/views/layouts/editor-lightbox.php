<?php /* Display a layout for editing Thrive Lightbox posts */
global $is_thrive_theme;
$options = [];
if ( $is_thrive_theme ) {
	$options = thrive_get_options_for_post( get_the_ID() );
}
do_action( 'get_header' );
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?> style="margin-top: 0 !important;height:100%">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?> style="margin-top: 0 !important;height:100%">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> style="margin-top: 0 !important;height:100%">
<!--<![endif]-->
<?php
/** @var TCB_Lightbox $lightbox */
$lightbox            = $data['lightbox'];
$config              = $lightbox->globals();
$is_for_landing_page = $lightbox->meta( 'tve_lp_lightbox' );
?>
<head>
	<?php if ( $is_thrive_theme ) : ?>
		<?php tha_head_top(); ?>
	<?php endif ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="robots" content="noindex, nofollow"/>
	<title>
		<?php /* Genesis wraps the meta title into another <title> tag using this hook: genesis_doctitle_wrap. the following line makes sure this isn't called */ ?>
		<?php /* What if they change the priority at which this hook is registered ? :D */ ?>
		<?php remove_filter( 'wp_title', 'genesis_doctitle_wrap', 20 ); ?>
		<?php wp_title( '|', true, 'right' ); ?>
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<?php

	if ( $is_for_landing_page ) {
		$landing_page_config = tve_get_landing_page_config( $is_for_landing_page );
		if ( ! empty( $landing_page_config['fonts'] ) ) {
			foreach ( $landing_page_config['fonts'] as $font ) {
				echo "<link href='" . esc_url( $font ) . "' rel='stylesheet' type='text/css'/>";
			}
		}
	}

	wp_head();

	if ( $is_thrive_theme ) {
		if ( ! empty( $options['favicon'] ) ) {
			echo '<link rel="shortcut icon" href="' . esc_url( $options['favicon'] ) . '"/>';
		}

		if ( ! empty( $options['custom_css'] ) ) {
			echo '<style type="text/css">' . $options['custom_css'] . '</style>';
		}

		tha_head_bottom();
	}
	?>

</head>
<body <?php body_class( 'tve-l-open tve-o-hidden tve-lightbox-page' ); ?>>
<div class="bSe<?php echo $is_for_landing_page ? ' wrp cnt' : ''; ?>" style="display: none">
	<div class="awr"></div>
</div>
<?php /** X-Theme conflict - X-Theme reads the top offset of this element without checking if it exists - and it causes the editor not to load */ ?>
<div class="x-navbar-fixed-top-active">
	<div class="x-navbar-wrap"></div>
</div>
<div class="tve_p_lb_overlay" style="<?php echo esc_attr( $config['overlay']['css'] ); ?>"<?php echo $config['overlay']['custom_color']; ?>></div> <?php // phpcs:ignore ?>
<div class="tve_post_lightbox wrp cnt bSe">
	<article>
		<div class="tve_p_lb_background tve-scroll">
			<div class="tcb-lp-lb tve_editable tve_p_lb_content<?php echo esc_attr( $config['content']['class'] ); ?>"
				 style="<?php echo esc_attr( $config['content']['css'] ); ?>"<?php echo $config['content']['custom_color']; //phpcs:ignore ?>>
				<div class="tve_p_lb_inner" id="tve-p-scroller" style="<?php echo $config['inner']['css']; ?>">
					<?php
					while ( have_posts() ) {
						the_post();
						the_content();
					}
					?>
				</div>
				<a href="javascript:void(0)"
				   class="tve_p_lb_close<?php echo esc_attr( $config['close']['class'] ); ?>"
				   style="<?php echo esc_attr( $config['close']['css'] ); ?>"<?php echo $config['close']['custom_color'];  // phpcs:ignore ?>
				   title="<?php echo esc_attr__( 'Close', 'thrive-cb' ); ?>">x</a>
			</div>
			<div class="tve-spacer"></div>
		</div>
	</article>
</div>

<?php do_action( 'get_footer' ); ?>
<?php wp_footer(); ?>
<?php if ( ! is_editor_page() ) : ?>
	<script type="text/javascript">
		jQuery( document ).ready( function () {
			/* trigger lightbox opening */
			jQuery( '.tve_p_lb_content' ).trigger( 'tve.lightbox-open' );
		} );
	</script>
<?php endif ?>
</body>
</html>
