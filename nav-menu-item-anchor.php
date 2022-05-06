<?php

/**
 * Plugin Name:       Nav Menu Item Hash
 * Plugin URI:        https://github.com/nlemoine/nav-menu-item-hash
 * Description:       Adds a hash field to WordPress nav menu item.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Nicolas Lemoine
 * Author URI:        https://github.com/nlemoine
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nav-menu-item-hash
 * Domain Path:       /languages.
 */

namespace Hellonico\MenuItemHash;

const HASH_META_KEY = '_menu_item_hash';

/**
 * Add menu item hash field.
 *
 * @param string        $item_id
 * @param WP_Post       $menu_item
 * @param int           $depth
 * @param stdClass|null $args
 * @param int           $current_object_id
 *
 * @return void
 */
function add_menu_item_hash_field($item_id, $menu_item, $depth, $args, $current_object_id)
{
    ?>
    <p class="field-hash field-hash description description-wide">
        <label for="edit-menu-item-hash-<?php echo $item_id; ?>">
            <?php \_e('Hash', 'nav-menu-item-hash'); ?><br />
            <input type="text" id="edit-menu-item-hash-<?php echo $item_id; ?>" class="widefat edit-menu-item-hash" name="menu-item-hash[<?php echo $item_id; ?>]" value="<?php echo \esc_attr($menu_item->hash); ?>" />
        </label>
    </p>
    <?php
}
\add_action('wp_nav_menu_item_custom_fields', __NAMESPACE__.'\\add_menu_item_hash_field', 10, 5);

/**
 * Add hash column to menu advanced settings.
 *
 * @param array $columns
 *
 * @return array
 */
function add_hash_column($columns)
{
    $columns['hash'] = \__('Hash', 'nav-menu-item-hash');

    return $columns;
}
\add_filter('manage_nav-menus_columns', __NAMESPACE__.'\\add_hash_column', 11);

/**
 * Save menu item hash value.
 *
 * @param int   $menu_id
 * @param int   $menu_item_db_id
 * @param array $args
 *
 * @return void
 */
function save_menu_item_hash($menu_id, $menu_item_db_id, $args)
{
    if (!isset($_REQUEST['menu-item-hash'][$menu_item_db_id])) {
        return;
    }
    $hash = \sanitize_text_field($_REQUEST['menu-item-hash'][$menu_item_db_id]);
    \update_post_meta($menu_item_db_id, namespace\HASH_META_KEY, $hash);
}
\add_action('wp_update_nav_menu_item', __NAMESPACE__.'\\save_menu_item_hash', 10, 4);

/**
 * Add hash to $menu_item.
 *
 * @param object $menu_item
 *
 * @return object
 */
function hydrate_menu_item_hash($menu_item)
{
    $menu_item->hash = \get_post_meta($menu_item->ID, namespace\HASH_META_KEY, true);

    return $menu_item;
}
\add_filter('wp_setup_nav_menu_item', __NAMESPACE__.'\\hydrate_menu_item_hash');

/**
 * Add hash to menu item URL.
 *
 * @param array $atts {
 *
 *     @var string $title        title attribute
 *     @var string $target       target attribute
 *     @var string $rel          the rel attribute
 *     @var string $href         the href attribute
 *     @var string $aria-current The aria-current attribute.
 * }
 *
 * @param object   $menu_item
 * @param stdClass $args
 * @param int      $depth
 *
 * @return array
 */
function add_menu_item_hash($atts, $menu_item, $args, $depth)
{
    if (empty($menu_item->hash) || empty($attrs['href'])) {
        return $atts;
    }
    $atts['href'] = $atts['href'].'#'.$menu_item->hash;

    return $atts;
}
\add_filter('nav_menu_link_attributes', __NAMESPACE__.'\\add_menu_item_hash', 10, 4);
