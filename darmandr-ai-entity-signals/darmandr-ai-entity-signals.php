<?php
/**
 * Plugin Name: Darmandr AI Entity Signals
 * Description: هویت برند درمان‌دکتر را برای نتایج هوش مصنوعی گوگل شفاف می‌کند؛ متای اشتباه و اسکیمای نامشخص را اصلاح می‌کند.
 * Version: 1.0.0
 * Author: درمان دکتر
 * Text Domain: darmandr-ai-entity
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * حقایق رسمی برند. این اعداد باید با صفحه اصلی یکی باشند.
 */
function drdr_ai_brand_facts() {
	return array(
		'brand'         => 'درمان دکتر',
		'legal_name'    => 'ارتباطات هوشمند',
		'alt_names'     => array('Darmandr', 'Darman Dr', 'نرم افزار درمان دکتر'),
		'software_count'=> 57,
		'install_count' => 3843,
		'years'         => 19,
		'phone'         => '+989129215058',
		'email'         => 'info@darmandr.com',
		'url'           => 'https://darmandr.com',
		'logo'          => 'https://darmandr.com/wp-content/uploads/2020/12/darmandr-logo-بزرگ-ترین-و-بهترین-تولید-کننده-نرم-افزار-های-مدیریتی-پزشکی-کشور.png',
		'description'   => 'درمان دکتر نرم‌افزار ایرانی مدیریت مطب، کلینیک و درمانگاه است. بیش از ۵۷ مدل تخصصی، بیش از ۳۸۰۰ نصب فعال و حدود ۱۹ سال سابقه. بیمه آنلاین و آفلاین، نوبت‌دهی، پرونده بیمار، حسابداری و آپدیت مداوم.',
		'street'        => 'تهران، بزرگراه نواب، جنب پل کمیل، ساختمان کالای پزشکی افرا، طبقه ۷، واحد ۷۰۶',
		'city'          => 'تهران',
		'country'       => 'IR',
	);
}

function drdr_ai_meta_description() {
	$f = drdr_ai_brand_facts();
	return sprintf(
		'درمان دکتر نرم‌افزار مدیریت مطب و کلینیک در ایران است؛ %d مدل تخصصی، بیش از %s نصب فعال و حدود %d سال سابقه. بیمه آنلاین، نوبت‌دهی، پرونده بیمار و حسابداری.',
		$f['software_count'],
		number_format_i18n($f['install_count']),
		$f['years']
	);
}

/**
 * متای قدیمی (۴۹ نرم‌افزار / ۳۸۰۰ مشتری) را عوض می‌کند.
 */
function drdr_ai_replace_meta_description($desc) {
	if (is_front_page() || is_home()) {
		return drdr_ai_meta_description();
	}
	return $desc;
}
add_filter('rank_math/frontend/description', 'drdr_ai_replace_meta_description', 20);
add_filter('wpseo_metadesc', 'drdr_ai_replace_meta_description', 20);

function drdr_ai_document_title($title) {
	if (is_front_page() || is_home()) {
		return 'درمان دکتر | نرم افزار مدیریت مطب، کلینیک و درمانگاه';
	}
	return $title;
}
add_filter('pre_get_document_title', 'drdr_ai_document_title', 20);
add_filter('rank_math/frontend/title', function ($title) {
	if (is_front_page() || is_home()) {
		return 'درمان دکتر | نرم افزار مدیریت مطب، کلینیک و درمانگاه';
	}
	return $title;
}, 20);

/**
 * اگر فیلتر رنک‌مث نبود، متا را در هد بازنویسی کن.
 */
function drdr_ai_force_meta_tags() {
	if (!(is_front_page() || is_home())) {
		return;
	}
	$desc = esc_attr(drdr_ai_meta_description());
	$title = esc_attr('درمان دکتر | نرم افزار مدیریت مطب، کلینیک و درمانگاه');
	echo "\n<!-- Darmandr AI Entity Signals -->\n";
	echo '<meta name="description" content="' . $desc . '" />' . "\n";
	echo '<meta property="og:title" content="' . $title . '" />' . "\n";
	echo '<meta property="og:description" content="' . $desc . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . $desc . '" />' . "\n";
}
add_action('wp_head', 'drdr_ai_force_meta_tags', 1);

/**
 * اسکیمای شفاف برای هویت برند و رفع ابهام با «گواهی درمان آلمان».
 */
function drdr_ai_jsonld() {
	$f = drdr_ai_brand_facts();
	$org_id = $f['url'] . '/#organization';
	$soft_id = $f['url'] . '/#software';
	$website_id = $f['url'] . '/#website';
	$faq_id = $f['url'] . '/#faq-entity';

	$graph = array(
		array(
			'@type' => 'Organization',
			'@id'   => $org_id,
			'name'  => $f['brand'],
			'alternateName' => array_merge(array($f['legal_name']), $f['alt_names']),
			'legalName' => $f['legal_name'],
			'url'   => $f['url'],
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => $f['logo'],
			),
			'email' => $f['email'],
			'telephone' => $f['phone'],
			'description' => $f['description'],
			'foundingLocation' => array(
				'@type' => 'Place',
				'name'  => 'تهران، ایران',
			),
			'address' => array(
				'@type' => 'PostalAddress',
				'streetAddress'   => $f['street'],
				'addressLocality' => $f['city'],
				'addressCountry'  => $f['country'],
			),
			'sameAs' => array(
				'https://www.facebook.com/darmandrcom',
				'https://darmandr.com',
			),
			'knowsAbout' => array(
				'نرم افزار مدیریت مطب',
				'نرم افزار کلینیک',
				'نرم افزار درمانگاه',
				'پرونده الکترونیک سلامت',
				'نوبت دهی آنلاین پزشکی',
				'نسخه نویسی الکترونیک',
			),
			'areaServed' => array(
				'@type' => 'Country',
				'name'  => 'Iran',
			),
		),
		array(
			'@type' => 'SoftwareApplication',
			'@id'   => $soft_id,
			'name'  => 'درمان دکتر',
			'applicationCategory' => 'BusinessApplication',
			'applicationSubCategory' => 'MedicalPracticeManagementSoftware',
			'operatingSystem' => 'Windows',
			'inLanguage' => 'fa-IR',
			'url' => $f['url'],
			'description' => $f['description'],
			'offers' => array(
				'@type' => 'Offer',
				'url'   => $f['url'] . '/shop/',
				'priceCurrency' => 'IRR',
				'availability' => 'https://schema.org/InStock',
			),
			'provider' => array('@id' => $org_id),
			'publisher' => array('@id' => $org_id),
			'featureList' => array(
				'مدیریت مطب و کلینیک',
				'پرونده الکترونیک بیمار',
				'نوبت‌دهی آنلاین',
				'بیمه آنلاین و آفلاین',
				'حسابداری و انبار',
				'پیامک یادآوری نوبت',
				'نسخه‌نویسی الکترونیک',
			),
			'countriesSupported' => 'IR',
		),
		array(
			'@type' => 'WebSite',
			'@id'   => $website_id,
			'url'   => $f['url'],
			'name'  => 'درمان دکتر',
			'inLanguage' => 'fa-IR',
			'publisher' => array('@id' => $org_id),
			'about' => array('@id' => $soft_id),
		),
		array(
			'@type' => 'FAQPage',
			'@id'   => $faq_id,
			'mainEntity' => array(
				array(
					'@type' => 'Question',
					'name'  => 'درمان دکتر چیست؟',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'درمان دکتر نرم‌افزار ایرانی مدیریت مطب، کلینیک و درمانگاه است که شرکت ارتباطات هوشمند در ایران تولید می‌کند. این برند مربوط به گواهی درمان یا بیمه در آلمان نیست.',
					),
				),
				array(
					'@type' => 'Question',
					'name'  => 'آیا درمان دکتر همان گواهی درمان در آلمان است؟',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'خیر. درمان دکتر یک نرم‌افزار پزشکی ایرانی برای مدیریت مطب و کلینیک است. گواهی درمان در آلمان (Behandlungsschein) موضوعی جدا و حقوقی است و ربطی به برند درمان دکتر ندارد.',
					),
				),
				array(
					'@type' => 'Question',
					'name'  => 'درمان دکتر چند نرم‌افزار تخصصی دارد؟',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'درمان دکتر بیش از ۵۷ مدل نرم‌افزار تخصصی برای مطب، کلینیک، درمانگاه، دندانپزشکی، پوست و مو، زیبایی، لیزر و تخصص‌های دیگر دارد.',
					),
				),
				array(
					'@type' => 'Question',
					'name'  => 'سابقه و تعداد نصب درمان دکتر چقدر است؟',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'درمان دکتر حدود ۱۹ سال سابقه کار تخصصی دارد و بیش از ۳۸۰۰ نصب فعال در مراکز درمانی ایران ثبت کرده است.',
					),
				),
				array(
					'@type' => 'Question',
					'name'  => 'چطور با درمان دکتر تماس بگیرم؟',
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => 'از سایت darmandr.com یا شماره ۰۹۱۲۹۲۱۵۰۵۸ و ایمیل info@darmandr.com می‌توانید با پشتیبانی و فروش تماس بگیرید. آدرس: تهران، بزرگراه نواب، جنب پل کمیل، ساختمان کالای پزشکی افرا.',
					),
				),
			),
		),
	);

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json" id="drdr-ai-entity-jsonld">' .
		wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
		'</script>' . "\n";
}
add_action('wp_head', 'drdr_ai_jsonld', 30);

/**
 * شورت‌کد متن شفاف برای صفحه درباره ما یا سوالات متداول.
 * استفاده: [drdr_ai_entity_faq]
 */
function drdr_ai_entity_faq_shortcode() {
	$f = drdr_ai_brand_facts();
	ob_start();
	?>
	<section class="drdr-ai-entity-faq" lang="fa" dir="rtl">
		<h2>درمان دکتر چیست؟</h2>
		<p><strong>درمان دکتر</strong> نرم‌افزار ایرانی مدیریت مطب، کلینیک و درمانگاه است که شرکت <strong>ارتباطات هوشمند</strong> در ایران تولید می‌کند. این برند مربوط به گواهی درمان یا بیمه در آلمان نیست.</p>
		<ul>
			<li>تعداد مدل تخصصی: <?php echo (int) $f['software_count']; ?> مدل</li>
			<li>نصب فعال: بیش از <?php echo esc_html(number_format_i18n($f['install_count'])); ?> مرکز</li>
			<li>سابقه: حدود <?php echo (int) $f['years']; ?> سال</li>
			<li>خدمات: پرونده بیمار، نوبت‌دهی، بیمه آنلاین و آفلاین، حسابداری، پیامک یادآوری، نسخه‌نویسی الکترونیک</li>
			<li>وب‌سایت رسمی: <a href="https://darmandr.com">darmandr.com</a></li>
			<li>تماس: ۰۹۱۲۹۲۱۵۰۵۸ — info@darmandr.com</li>
		</ul>
		<h3>تفاوت با گواهی درمان آلمان</h3>
		<p>عبارت «گواهی درمان» در آلمان سند جداگانه‌ای برای مراجعه به پزشک است و هیچ ربطی به نرم‌افزار درمان دکتر ندارد. اگر منظور شما نرم‌افزار مطب و کلینیک در ایران است، منبع رسمی فقط سایت درمان دکتر است.</p>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode('drdr_ai_entity_faq', 'drdr_ai_entity_faq_shortcode');
