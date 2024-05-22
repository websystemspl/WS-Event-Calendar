<?php

/*
 * Plugin Name:       WS Event Calendar
 * Text Domain:       ws-event-calendar
 * Description:       Display event calendar
 * Version:           1.0.1
 * Requires at least: 6.0
 * Author:            Web Systems
 * Author URI:        https://www.web-systems.pl/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 */

if (!defined('WPINC')) {
  die;
}

if (!defined('WS_EVENT_CALENDAR_PLUGIN_DIR_PATH')) {
  define('WS_EVENT_CALENDAR_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WS_EVENT_CALENDAR_PLUGIN_DIR_URL')) {
  define('WS_EVENT_CALENDAR_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
}

require __DIR__ . '/vendor/autoload.php';

class WsEventCalendar
{
  public function __construct()
  {
    load_plugin_textdomain('web-systems-events-calendar', false, dirname(plugin_basename(__FILE__)) . '/languages');
    $pluginManager = new WsEventCalendar\App\PluginManager;
    $pluginManager->run();
  }

  public static function activate()
  {
    \flush_rewrite_rules();
  }

  public static function deactivate()
  {
    \flush_rewrite_rules();
  }
}

register_activation_hook(__FILE__, [WsEventCalendar::class, 'activate']);
register_deactivation_hook(__FILE__, [WsEventCalendar::class, 'deactivate']);

new WsEventCalendar();
