<?php
namespace Sleek\Settings;

# Better keep these here so we don't misstype anything
const SETTINGS_NAME = 'sleek_settings';
const SETTINGS_SECTION_NAME = 'sleek_settings_section';

####################
# Add settings field
function add_setting ($name, $type = 'text', $label = null) {
	$label = $label ?? __(\Sleek\Utils\convert_case($name, 'title'), 'sleek');

	add_settings_field(SETTINGS_NAME . '_' . $name, $label, function () use ($name, $type, $label) {
		$options = get_option(SETTINGS_NAME);

		if ($type == 'textarea') {
			echo '<textarea name="' . SETTINGS_NAME . '[' . $name . ']" rows="6" cols="40">' . ($options[$name] ?? '') . '</textarea>';
		}
		else {
			echo '<input type="text" name="' . SETTINGS_NAME . '[' . $name . ']" value="' . ($options[$name] ?? '') . '">';
		}
	}, SETTINGS_SECTION_NAME, SETTINGS_SECTION_NAME);
}

####################
# Get settings field
function get_setting ($name) {
	$options = get_option(SETTINGS_NAME);

	return $options[$name] ?? null;
}

################
# Add admin page
add_action('admin_menu', function () {
	add_options_page(__('Sleek settings', 'sleek'), 'Sleek', 'manage_options', 'sleek-settings', function () {
		?>
		<div class="wrap">
			<h1><?php _e('Sleek settings', 'sleek') ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields(SETTINGS_NAME) ?>
				<?php do_settings_sections(SETTINGS_SECTION_NAME) ?>
				<button><?php _e('Save settings', 'sleek') ?></button>
			</form>
		</div>
		<?php
	});
});

##################
# Add our settings
add_action('admin_init', function () {
	register_setting(SETTINGS_NAME, SETTINGS_NAME, function ($input) {
		# TODO: Validate
		return $input;
	});

	add_settings_section(SETTINGS_SECTION_NAME, false, function () {
		# NOTE: Mandatory function but we don't need it...
	}, SETTINGS_SECTION_NAME); # NOTE: WP Docs says this should be the add_options_page slug but that doesn't work. It needs to be the same as is later passed to do_settings_section

	# Built-in fields
	add_setting('google_maps_api_key', 'text', __('Google Maps API Key', 'sleek'));
	# TODO: Move to sleek-google-search
#	add_setting('google_search_api_key', 'text', __('Google Search API Key', 'sleek'));
#	add_setting('google_search_engine_id', 'text', __('Google Search Engine ID', 'sleek'));
	add_setting('head_code', 'textarea', esc_html__('Code inside <head>', 'sleek'));
	add_setting('foot_code', 'textarea', esc_html__('Code just before </body>', 'sleek'));
	add_setting('cookie_consent', 'textarea', esc_html__('Cookie consent text', 'sleek'));
	add_setting('site_notice', 'textarea', esc_html__('Site notice', 'sleek'));
});

########
# Header
add_action('wp_head', function () {
	# Custom head code
	if ($code = get_setting('head_code')) {
		echo $code;
	}

	# Cookie consent text
	if ($consent = get_setting('cookie_consent')) {
		$cookieConsent = $consent;
	}
	else {
		$cookieUrl = get_option('wp_page_for_privacy_policy') ? get_permalink(get_option('wp_page_for_privacy_policy')) : 'https://cookiesandyou.com/';
		$cookieConsent = apply_filters('sleek_cookie_consent', sprintf(__('We use cookies to bring you the best possible experience when browsing our site. <a href="%s" target="_blank">Read more</a> | <a href="#" class="close">Accept</a>', 'sleek'), $cookieUrl), $cookieUrl);
	}

	echo '<script>SLEEK_COOKIE_CONSENT = ' . json_encode($cookieConsent) . '</script>';
});

########
# Footer
add_action('wp_footer', function () {
	# Custom foot code
	if ($code = get_setting('foot_code')) {
		echo $code;
	}

	# Google Maps
	if ($key = get_setting('google_maps_api_key')) {
		echo "<script>
			window.gmAsyncInit = function () {};

			function gmInit (cb) {
				if (window.google && window.google.maps) {
					cb(window.google);
				}
				else {
					var oldGMInit = window.gmAsyncInit;

					window.gmAsyncInit = function () {
						oldGMInit();
						cb(window.google);
					};
				}
			}
		</script>";
	}
});

############################
# Include google maps JS api
add_action('wp_enqueue_scripts', function () {
	if ($key = get_setting('google_maps_api_key')) {
		wp_register_script('google_maps', 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&callback=gmAsyncInit', [], null, true);
		wp_enqueue_script('google_maps');
	}
});

################################
# Add Google Maps API Key to ACF
add_action('init', function () {
	if ($key = get_setting('google_maps_api_key')) {
		add_filter('acf/fields/google_map/api', function ($api) use ($key) {
			$api['key'] = $key;

			return $api;
		});
	}
});
