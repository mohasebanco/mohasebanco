<?php
/**
 * Plugin Name: Darmandr SEO Index Fixes
 * Description: صفحات کم‌ارزش را از ایندکس و نقشه سایت بیرون می‌آورد و بودجه خزش را برای صفحات مهم آزاد می‌کند.
 * Version: 1.0.0
 * Author: درمان دکتر
 * Text Domain: darmandr-seo-index-fixes
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * برگه‌هایی که نباید ایندکس شوند.
 */
function drdr_seo_noindex_slugs() {
	return array(
		'wishlist',
		'products-compare',
		'footer-call-to-action-row',
		'backup-support',
		'checkout-2',
		'checkout',
		'cart',
		'cart-2',
		'my-account',
		'my-account-2',
	);
}

/**
 * برای برگه‌های کم‌ارزش و آرشیو برچسب، نوایندکس بگذار.
 */
function drdr_seo_force_noindex($robots) {
	if (is_admin()) {
		return $robots;
	}

	$should_noindex = false;

	if (is_tag() || is_tax('product_tag')) {
		$should_noindex = true;
	}

	if (is_page(drdr_seo_noindex_slugs())) {
		$should_noindex = true;
	}

	if ($should_noindex) {
		$robots['index'] = 'noindex';
		$robots['follow'] = 'follow';
	}

	return $robots;
}
add_filter('wp_robots', 'drdr_seo_force_noindex', 99);

/**
 * رنک‌مث: نوایندکس همان صفحات.
 */
function drdr_seo_rank_math_robots($robots) {
	if (is_tag() || is_tax('product_tag') || is_page(drdr_seo_noindex_slugs())) {
		$robots['index'] = 'noindex';
		$robots['follow'] = 'follow';
		unset($robots['max-snippet'], $robots['max-video-preview'], $robots['max-image-preview']);
	}
	return $robots;
}
add_filter('rank_math/frontend/robots', 'drdr_seo_rank_math_robots', 99);

/**
 * رنک‌مث: نقشه برچسب را خاموش کن تا ۵۵۸ آدرس ضعیف بودجه خزش را نخورند.
 */
add_filter('rank_math/sitemap/enable_taxonomy', function ($value, $taxonomy) {
	if (in_array($taxonomy, array('post_tag', 'product_tag'), true)) {
		return false;
	}
	return $value;
}, 10, 2);

/**
 * رنک‌مث: برگه‌های کم‌ارزش را از نقشه برگه حذف کن.
 */
add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
	if (empty($url) || !is_array($url)) {
		return $url;
	}

	$loc = isset($url['loc']) ? $url['loc'] : '';
	$blocked = array(
		'/wishlist',
		'/products-compare',
		'/footer-call-to-action-row',
		'/backup-support',
		'/checkout-2',
		'/checkout/',
		'/cart/',
		'/my-account',
	);

	foreach ($blocked as $needle) {
		if ($loc && false !== strpos($loc, $needle)) {
			return false;
		}
	}

	return $url;
}, 10, 3);

/**
 * کنونیکال غلط wishlist را خنثی نکن؛ فقط نوایندکس کافی است.
 * اگر صفحه واقعاً ریدایرکت شده، رنک‌مث مدیریت می‌کند.
 */

/**
 * لیست آدرس‌های مهم برای درخواست ایندکس (ادمین).
 */
function drdr_seo_priority_urls() {
	$urls = array(home_url('/'));

	$pages = get_posts(array(
		'post_type' => array('page', 'product', 'post'),
		'post_status' => 'publish',
		'posts_per_page' => 80,
		'orderby' => 'modified',
		'order' => 'DESC',
		'post_name__in' => null,
	));

	foreach ($pages as $p) {
		if (in_array($p->post_name, drdr_seo_noindex_slugs(), true)) {
			continue;
		}
		$urls[] = get_permalink($p);
	}

	return array_values(array_unique(array_filter($urls)));
}

/**
 * پینگ نقشه سایت به موتورهای جستجو هنگام فعال‌سازی و روزانه.
 */
function drdr_seo_ping_sitemaps() {
	$sitemap = home_url('/sitemap_index.xml');
	$targets = array(
		'https://www.google.com/ping?sitemap=' . rawurlencode($sitemap),
		'https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap),
	);

	foreach ($targets as $target) {
		wp_remote_get($target, array('timeout' => 8, 'blocking' => false));
	}
}
register_activation_hook(__FILE__, 'drdr_seo_ping_sitemaps');

if (!wp_next_scheduled('drdr_seo_daily_sitemap_ping')) {
	wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'drdr_seo_daily_sitemap_ping');
}
add_action('drdr_seo_daily_sitemap_ping', 'drdr_seo_ping_sitemaps');

register_deactivation_hook(__FILE__, function () {
	$timestamp = wp_next_scheduled('drdr_seo_daily_sitemap_ping');
	if ($timestamp) {
		wp_unschedule_event($timestamp, 'drdr_seo_daily_sitemap_ping');
	}
});

/**
 * صفحه ابزارک ادمین: لیست صفحات اولویت‌دار برای درخواست ایندکس در سرچ کنسول.
 */
function drdr_seo_admin_menu() {
	add_management_page(
		'ایندکس درمان‌دکتر',
		'ایندکس درمان‌دکتر',
		'manage_options',
		'drdr-seo-index',
		'drdr_seo_admin_page'
	);
}
add_action('admin_menu', 'drdr_seo_admin_menu');

function drdr_seo_admin_page() {
	if (!current_user_can('manage_options')) {
		return;
	}

	if (isset($_POST['drdr_seo_ping']) && check_admin_referer('drdr_seo_ping')) {
		drdr_seo_ping_sitemaps();
		echo '<div class="notice notice-success"><p>پینگ نقشه سایت ارسال شد.</p></div>';
	}

	$urls = drdr_seo_priority_urls();
	echo '<div class="wrap"><h1>ایندکس درمان‌دکتر</h1>';
	echo '<p>صفحات کم‌ارزش نوایندکس شدند و برچسب از نقشه سایت حذف می‌شود تا صفحات مهم بهتر ایندکس شوند.</p>';
	echo '<form method="post">';
	wp_nonce_field('drdr_seo_ping');
	echo '<p><button class="button button-primary" name="drdr_seo_ping" value="1">پینگ دوباره نقشه سایت</button></p>';
	echo '</form>';
	echo '<h2>آدرس‌های اولویت‌دار برای Request indexing در سرچ کنسول</h2>';
	echo '<textarea rows="20" style="width:100%;font-family:monospace" readonly>';
	echo esc_textarea(implode("\n", $urls));
	echo '</textarea></div>';
}
