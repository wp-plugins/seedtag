<?php
/**
 * Plugin Name: seedtag
 * Plugin URI:  http://www.seedtag.com
 * Description: Instala seedtag de forma facil. Etiqueta tus fotos y gana dinero con ellas! Si tienes algún problema <a href="mailto:info@seedtag.com" target="_blank">contacta con nosotros</a>
 * Version:     0.1.3
 * Author:      <b>seedtag</b> team
 * Author URI:  http://www.seedtag.com/team
 * License:     GPLv2+
 * Text Domain: seedtagwp
 * Domain Path: /languages
 */

/**
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'SEEDTAGWP_VERSION', '0.1.3' );
define( 'SEEDTAGWP_URL',     plugin_dir_url( __FILE__ ) );
define( 'SEEDTAGWP_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function seedtagwp_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'seedtagwp' );
	load_textdomain( 'seedtagwp', WP_LANG_DIR . '/seedtagwp/seedtagwp-' . $locale . '.mo' );
	load_plugin_textdomain( 'seedtagwp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Activate the plugin
 */
function seedtagwp_activate() {
	seedtagwp_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'seedtagwp_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function seedtagwp_deactivate() {

}
register_deactivation_hook( __FILE__, 'seedtagwp_deactivate' );


add_action( 'init', 'seedtagwp_init' );


class seedtagwp_general_setting {
    function __construct( ) {
        add_filter( 'admin_init' , array( $this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'seedtagwp_token', 'esc_attr' );
        add_settings_field('seedtagwp_token', '<label for="seedtagwp_token">'.__('Seedtag ID' , 'seedtagwp_token' ).'</label>' , array($this, 'fields_html') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'seedtagwp_token', '' );
        echo '<input type="text" id="seedtagwp_token" name="seedtagwp_token" value="' 
        . $value 
        . '" placeholder="XXXX-XXXX-XX" /> <span>'
        . '<a href="http://www.seedtag.com/control/publisher/guide" target="_blanc">'
                . 'Aquí encontrarás tu código'
        . '</a> (debes estar logueado)</span>';
    }
}

$new_general_setting = new seedtagwp_general_setting();



function seedtagwp_pasteCode() {
    $codeTemplate = file_get_contents(dirname(__FILE__) . '/assets/seedtagCode.html');
    $stId = get_option( 'seedtagwp_token', '' );
    $code = str_replace('#st-token#', $stId, $codeTemplate); 
    
    if($stId) {        
        echo $code;
    }
}

add_action('wp_head', 'seedtagwp_pasteCode');


// Add settings link on plugin page
function seedtagwp_plugin_settings_link($links) {
    $settings_link = '<a href="options-general.php#seedtagwp_token">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'seedtagwp_plugin_settings_link' );