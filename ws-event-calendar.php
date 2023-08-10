<?php

/*
 * Plugin Name:       WS Event Calendar
 * Text Domain:       ws_event_calendar
 * Description:       Display event calendar
 * Version:           1.0.0
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

// YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
//   'https://update.web-systems.pl/?action=get_metadata&slug=ws_event_calendar',
//   __FILE__,
//   'ws_event_calendar'
// );


class WsInpostMapPlugin
{
  public function __construct()
  {
    load_plugin_textdomain('ws_event_calendar', false, dirname(plugin_basename(__FILE__)) . '/languages');
    $pluginManager = new WsEventCalendar\App\PluginManager;
    $pluginManager->run();
  }

  public static function activate()
  {
  }

  public static function deactivate()
  {
  }
}

register_activation_hook(__FILE__, [WsInpostMapPlugin::class, 'activate']);
register_deactivation_hook(__FILE__, [WsInpostMapPlugin::class, 'deactivate']);

new WsInpostMapPlugin();
