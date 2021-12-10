<?php
namespace Sleek\Settings;

# Better keep these here so we don't misstype anything
const SETTINGS_NAME = 'sleek_settings';
const SETTINGS_SECTION_NAME = 'sleek_settings_section';

####################
# Add settings field
function add_setting ($name, $type = 'text', $label = null) {
	$label = $label ?? __(\Sleek\Utils\convert_case($name, 'title'), 'sleek_admin');

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
	# Translators: This is the title of the settings page (Settings -> Sleek inside the WP admin) for the theme Sleek
	add_options_page(__('Sleek settings', 'sleek_admin'), 'Sleek', 'manage_options', 'sleek-settings', function () {
		?>
		<div class="wrap">
			<h1><?php _e('Sleek settings', 'sleek_admin') ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields(SETTINGS_NAME) ?>
				<?php do_settings_sections(SETTINGS_SECTION_NAME) ?>
				<button class="button button-primary"><?php _e('Save settings', 'sleek_admin') ?></button>
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
	add_setting('head_code', 'textarea', esc_html__('Code inside <head>', 'sleek_admin'));
	add_setting('foot_code', 'textarea', esc_html__('Code just before </body>', 'sleek_admin'));
});

########
# Header
add_action('wp_head', function () {
	if ($code = get_setting('head_code')) {
		echo $code;
	}
});

########
# Footer
add_action('wp_footer', function () {
	if ($code = get_setting('foot_code')) {
		echo $code;
	}
});
