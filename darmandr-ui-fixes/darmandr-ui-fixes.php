<?php
/**
 * Plugin Name: Darmandr UI Fixes
 * Description: دکمه تماس شناور و بازگشت به بالا را بالای منوی موبایل می‌آورد و زبان ویجت کریسپ را فارسی می‌کند.
 * Version: 1.0.0
 * Author: درمان دکتر
 * Text Domain: darmandr-ui-fixes
 */

if (!defined('ABSPATH')) {
	exit;
}

define('DRDR_UI_FIXES_VERSION', '1.0.0');
define('DRDR_UI_FIXES_URL', plugin_dir_url(__FILE__));

/**
 * زبان کریسپ باید قبل از لود اسکریپت ویجت ست شود.
 * افزونه رسمی مقدار fa-ir می‌گذارد که معتبر نیست و متن‌ها انگلیسی می‌شود.
 */
function drdr_ui_fixes_force_crisp_locale_early() {
	?>
	<script id="drdr-ui-fixes-crisp-locale">
	window.CRISP_RUNTIME_CONFIG = window.CRISP_RUNTIME_CONFIG || {};
	window.CRISP_RUNTIME_CONFIG.locale = 'fa';
	</script>
	<?php
}
add_action('wp_head', 'drdr_ui_fixes_force_crisp_locale_early', 0);

function drdr_ui_fixes_enqueue_assets() {
	wp_enqueue_style(
		'drdr-ui-fixes',
		DRDR_UI_FIXES_URL . 'assets/fixes.css',
		array(),
		DRDR_UI_FIXES_VERSION
	);

	wp_enqueue_script(
		'drdr-ui-fixes',
		DRDR_UI_FIXES_URL . 'assets/fixes.js',
		array(),
		DRDR_UI_FIXES_VERSION,
		true
	);
}
add_action('wp_enqueue_scripts', 'drdr_ui_fixes_enqueue_assets', 99);
