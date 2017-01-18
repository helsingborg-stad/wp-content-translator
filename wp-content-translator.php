<?php

/**
 * Plugin Name:       Wp Content Translator
 * Plugin URI:        https://github.com/helsingborg-stad/wp-content-translator
 * Description:       Minimalistic content translation in WordPress.
 * Version:           0.0.1
 * Author:            Sebastian Thulin, Kristoffer Svanmark
 * Author URI:        http://helsingborg.se
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wp-content-translator
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

//Require php 7
register_activation_hook(__FILE__, function () {
    if (substr(phpversion(), 0, 1) != 7) {
        die("ERROR: Lowest PHP version supported is 7.0");
    }
});

define('WPCONTENTTRANSLATOR_PATH', plugin_dir_path(__FILE__));
define('WPCONTENTTRANSLATOR_URL', plugins_url('', __FILE__));
define('WPCONTENTTRANSLATOR_CONFIG_PATH', WPCONTENTTRANSLATOR_PATH . 'source/php/Configuration/');
define('WPCONTENTTRANSLATOR_TEMPLATE_PATH', WPCONTENTTRANSLATOR_PATH . 'templates/');

load_plugin_textdomain('wp-content-translator', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once WPCONTENTTRANSLATOR_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once WPCONTENTTRANSLATOR_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new ContentTranslator\Vendor\Psr4ClassLoader();
$loader->addPrefix('ContentTranslator', WPCONTENTTRANSLATOR_PATH);
$loader->addPrefix('ContentTranslator', WPCONTENTTRANSLATOR_PATH . 'source/php/');
$loader->register();

// Start application
new ContentTranslator\App();
