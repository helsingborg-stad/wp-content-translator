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

define('WPCONTENTTRANSLATOR_PATH', plugin_dir_path(__FILE__));
define('WPCONTENTTRANSLATOR_URL', plugins_url('', __FILE__));
define('WPCONTENTTRANSLATOR_TEMPLATE_PATH', WPCONTENTTRANSLATOR_PATH . 'templates/');
define('WPCONTENTTRANSLATOR_LANGUAGES_JSON_PATH', WPCONTENTTRANSLATOR_PATH . 'source/languages.json');

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
