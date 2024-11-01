<?php


/**
/**
 * @package SlidetoSubscribe
 */
/*
 * Plugin Name: Slide to Subscribe
 * Plugin URI: https://slidetosubscribe.com/dashboard
 * Description: Allow people to subscribe to your newsletters with just a slide.
 * Version: 1.1
 * Text Domain: slide-to-subscribe
 * Author: Slide to Subscribe
 * Author URI: https://slidetosubscribe.com
 * License: GPLv2 or later
 */


function s2s_render_text() {
	$widget_id = get_option('s2s_widget_id');
	$s2s_widget_height = get_option('s2s_widget_height');
	if (empty($s2s_widget_height)) {
		$s2s_widget_height = '500px';
	}
	$s2s_widget_width = get_option('s2s_widget_width');
	if (empty($s2s_widget_width)) {
		$s2s_widget_width = '600px';
	}
	if($widget_id){
		return '<iframe src="https://slidetosubscribe.com/embed/'.$widget_id.'/" height="'.$s2s_widget_height.'" width="'.$s2s_widget_width.'" class="s2s_widget_em" title="Slide to Subscribe" style="border:none;margin:5px auto;max-width:95%"></iframe>';
	} else {
		return;
	}

	
}
add_shortcode('slide-to-subscribe', 's2s_render_text');
$s2s_display_posts = get_option('s2s_display_posts');
if ($s2s_display_posts === '1') {
	add_filter( 'the_content', 's2s_add_to_posts' );
}
$s2s_display_floating = get_option('s2s_display_floating');
if ($s2s_display_floating === '1') {
	add_action( 'wp_head', 's2s_script' );
}
add_action( 'activated_plugin', 's2s_activation_redirect' );

function s2s_script() {
	$widget_id = get_option('s2s_widget_id');
    echo "<script>let s2s_widget_id='".$widget_id."';let s2s_script=document.createElement('script');s2s_script.type='text/javascript';s2s_script.src='https://slidetosubscribe.com/bar.js';s2s_script.async=true;document.head.appendChild(s2s_script);</script>";
}

function s2s_settings_page() {
	add_menu_page(
		'Slide to Subscribe', // title of the settings page
		'Slide to Subscribe', // title of the submenu
		'manage_options', // capability of the user to see this page
		's2s-settings-page', // slug of the settings page
		's2s_settings_page_html', // callback function to be called when rendering the page
		'dashicons-welcome-widgets-menus', // icon
		30 // order
	);
	add_action('admin_init', 's2s_settings_init');
}
add_action('admin_menu', 's2s_settings_page');

function s2s_settings_init() {
	add_settings_section(
		's2s-settings-section', // id of the section
		'Widget settings', // title to be displayed
		'', // callback function to be called when opening section
		's2s-settings-page' // page on which to display the section, this should be the same as the slug used in add_submenu_page()
	);

	// register the setting
	register_setting(
		's2s-settings-page', // option group
		's2s_widget_id'
	);

	// register the setting
	register_setting(
		's2s-settings-page', // option group
		's2s_display_posts'
	);

	// register the setting
	register_setting(
		's2s-settings-page', // option group
		's2s_display_floating'
	);

	// register the setting
	register_setting(
		's2s-settings-page', // option group
		's2s_widget_height'
	);

	// register the setting
	register_setting(
		's2s-settings-page', // option group
		's2s_widget_width'
	);

	add_settings_field(
		's2s-widget-id', // id of the settings field
		'My subscribe.to URL', // title
		's2s_settings_cb', // callback function
		's2s-settings-page', // page on which settings display
		's2s-settings-section' // section on which to show settings
	);
	add_settings_field(
		's2s-display-posts', // id of the settings field
		'Add to all posts', // title
		's2s_settings_posts', // callback function
		's2s-settings-page', // page on which settings display
		's2s-settings-section' // section on which to show settings
	);
	add_settings_field(
		's2s-widget-height', // id of the settings field
		'Custom widget height', // title
		's2s_settings_height', // callback function
		's2s-settings-page', // page on which settings display
		's2s-settings-section' // section on which to show settings
	);
	add_settings_field(
		's2s-widget-width', // id of the settings field
		'Custom widget width', // title
		's2s_settings_width', // callback function
		's2s-settings-page', // page on which settings display
		's2s-settings-section' // section on which to show settings
	);
	add_settings_field(
		's2s-display-floating', // id of the settings field
		'Display floating widget', // title
		's2s_settings_floating', // callback function
		's2s-settings-page', // page on which settings display
		's2s-settings-section' // section on which to show settings
	);

}

function s2s_settings_cb() {
	$s2s_widget_id = get_option('s2s_widget_id');
	?>
    <div id="titlediv">
    	<p>Insert your subscribe.to URL and click on "Save Changes". Then paste this in your posts or wherever you want the widget to appear: <code>[slide-to-subscribe]</code></p>
        <div style="margin: 25px 0;font-size: 17px;">subscribe.to/<input id="title" type="text" name="s2s_widget_id" style="width:150px;font-size:1em;" value="<?php echo $s2s_widget_id; ?>"></div>
        <div><a href="https://slidetosubscribe.com/login/?display=wp" target="_blank">Already have an account? Click to display your subscribe.to URL.</a></div>
    </div>
    <?php
}

function s2s_settings_height() {
	$s2s_widget_height = get_option('s2s_widget_height');
	if (empty($s2s_widget_height)) {
		$s2s_widget_height = '500px';
	}
	?>
    <div id="heightdiv">
    	<p>Optional: set a custom height for your widgets. The default is 500px. The value can be in px, % or vh. Examples: 500px or 80% or 70vh.</p>
    	<p>For the compact version of the widget, use a height of 100px or less.</p>
        <input id="height" type="text" name="s2s_widget_height" style="width:150px;font-size:1em;" value="<?php echo $s2s_widget_height; ?>">
    </div>
    <?php
}

function s2s_settings_width() {
	$s2s_widget_width = get_option('s2s_widget_width');
	if (empty($s2s_widget_width)) {
		$s2s_widget_width = '100%';
	}
	?>
    <div id="widthdiv">
    	<p>Optional: set a custom width for your widgets. The default is 100%. The value can be in px, % or vh. Examples: 500px or 80% or 70vh.</p>
        <input id="width" type="text" name="s2s_widget_width" style="width:150px;font-size:1em;" value="<?php echo $s2s_widget_width; ?>">
    </div>
    <?php
}

function s2s_settings_posts() {
	$s2s_widget_id = get_option('s2s_widget_id');
	$s2s_display_posts = get_option('s2s_display_posts');
	if( !isset( $s2s_display_posts ) ) {
		$s2s_display_posts = 0;
	} 
	
	?>
    <div id="titlediv">
        <p>Display the widget at the end of all posts (recommended):</p>
        <?php echo '<input type="checkbox" id="s2s_display_posts" name="s2s_display_posts" value="1"' . checked( 1, $s2s_display_posts, false ) . '/>';
		?>
    </div>
    <?php
}

function s2s_settings_floating() {
	$s2s_widget_id = get_option('s2s_widget_id');
	$s2s_display_floating = get_option('s2s_display_floating');
	if( !isset( $s2s_display_floating ) ) {
		$s2s_display_floating = 0;
	} 
	
	?>
    <div id="floatingdiv">
        <p>Display the floating subscribe bar on all pages (recommended):</p>
        <?php echo '<input type="checkbox" id="s2s_display_floating" name="s2s_display_floating" value="1"' . checked( 1, $s2s_display_floating, false ) . '/>';
		?>
    </div>
    <?php
}

function s2s_settings_page_html() {
	$s2s_widget_height = get_option('s2s_widget_height');
	$s2s_widget_width = get_option('s2s_widget_width');
	if (empty($s2s_widget_width)) {
		$s2s_widget_width = '600px';
	}
	if (empty($s2s_widget_height)) {
		$s2s_widget_height = '500px';
	}
	// check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}
	echo '<div class="wrap">';
	echo '<form method="POST" action="options.php">';
	$widget_id = get_option('s2s_widget_id');
	if (empty($widget_id)) {
		echo '<h1>Welcome! New to Slide to Subscribe? Slide to get started.</h1>';
		echo '<div>Already have an account? Skip this section and type your subscribe URL below.</div>';
		$iframe_src = 'https://slidetosubscribe.com/me/?s2s-ext-source=wp';
	} else {
		echo '<h1>Welcome! Here\'s your widget:</h1>';
		echo '<div><a href="https://slidetosubscribe.com/dashboard/" target="_blank">View subscribers ⧉</a></div>';
		$iframe_src = 'https://slidetosubscribe.com/embed/'.$widget_id.'/';
	}

	echo '<iframe src="'.$iframe_src.'" width="'.$s2s_widget_width.'" height="'.$s2s_widget_height.'" style="border:none;max-width:95%;margin: 0 auto;"></iframe>';
	
	settings_fields('s2s-settings-page');
	do_settings_sections('s2s-settings-page');
	submit_button();
	echo '</form>';
	echo '</div>';
}

function s2s_add_to_posts( $content ) {
  global $post;
  if( ! $post instanceof WP_Post ) return $content;

  switch( $post->post_type ) {
    case 'post':
      return $content . '[slide-to-subscribe]';

    default:
      return $content;
  }
}

function s2s_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=s2s-settings-page' ) ) );
    }
}