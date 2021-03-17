<?php
/**
 * Plugin Name: Ciudades del Perú para Woocommerce 2021
 * Description: Este es un plugin con los departementos y ciudades de Perú, originalmente creado por Lorenzo Cubas. Actualizado y subido por Sergio Tijero.
 * Version: 2.0.0
 * Author: Sergio Tijero Yupanqui
 * Credits: Lorenzo Cubas 
 * Author URI: https://sergiotijero.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ciudades-del-peru-para-woocommerce-2021
 * Domain Path: /languages
 * WC tested up to: 5.1.0
 * WC requires at least: 2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('plugins_loaded','states_places_peru_init',1);

function states_places_peru_smp_notices($classes, $notice){
    ?>
    <div class="<?php echo $classes; ?>">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function states_places_peru_init(){
    load_plugin_textdomain('ciudades-del-peru-para-woocommerce-2021',
        FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    /**
     * Check if WooCommerce is active
     */
    if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

        require_once ('includes/states-places.php');
        /**
         * Instantiate class
         */
        $GLOBALS['wc_states_places'] = new WC_States_Places_peru(__FILE__);


        require_once ('includes/filter-by-cities.php');

        add_filter( 'woocommerce_shipping_methods', 'add_filters_by_cities_method' );

        function add_filters_by_cities_method( $methods ) {
            $methods['filters_by_cities_shipping_method'] = 'Filters_By_Cities_Method';
            return $methods;
        }

        add_action( 'woocommerce_shipping_init', 'filters_by_cities_method' );

        $subs = __( '<strong>Muchas gracias pór instalar este plugin. ;)</strong> ', 'ciudades-del-peru-para-woocommerce-2021' ) .
            sprintf(__('%s', 'ciudades-del-peru-para-woocommerce-2021' ),
                '<a class="button button-primary" href="https://sergiotijero.com">' .
                __('Visita mi sitio web', 'ciudades-del-peru-para-woocommerce-2021') . '</a>' );

        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action('admin_notices', function() use($subs) {
                states_places_peru_smp_notices('notice notice-info is-dismissible', $subs);
            });
        }

    }
}


add_filter( 'woocommerce_default_address_fields', 'mrks_woocommerce_default_address_fields' );

function mrks_woocommerce_default_address_fields( $fields ) {
    if ($fields['city']['priority'] < $fields['state']['priority']){
        $state_priority = $fields['state']['priority'];
        $fields['state']['priority'] = $fields['city']['priority'];
        $fields['city']['priority'] = $state_priority;

    }
    return $fields;
}