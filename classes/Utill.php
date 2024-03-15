<?php

namespace StockManager;

class Utill {
    /**
     * Set the stoct Manager settings in plugin activation page.
     * @param mixed $links
     * @return array
     */
    public static function stock_manager_settings($links) {
        $plugin_links = [
            '<a href="' . admin_url('admin.php?page=woo-stock-manager-setting#&tab=settings&subtab=general') . '">' . __('Settings', 'woocommerce-stock-manager') . '</a>',
            '<a href="https://multivendorx.com/support-forum/forum/product-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __('Support', 'woocommerce-stock-manager') . '</a>',
            '<a href="https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=products-stock-manager" target="_blank">' . __('Docs', 'woocommerce-stock-manager') . '</a>'
        ];
        if (apply_filters('is_stock_manager_pro_inactive', true)) {
            $links['go_pro'] = '<a href="' . WOO_STOCK_MANAGER_PRO_SHOP_URL . '" class="stock-manager-pro-plugin" target="_blank">' . __('Get Pro', 'woocommerce-stock-manager') . '</a>';
        }
        return array_merge($plugin_links, $links);
    }

    /**
     * Html for Woocommerce plugin inactive notice.
     * @return void
     */
    public static function woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php
		// Translators: This message is to display the Product Stock Manager & Notifier for WooCommerce is inactive.
		printf(esc_html__('%1$sProduct Stock Manager & Notifier for WooCommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for the Product Stock Manager & Notifier for WooCommerce to work. Please %5$sinstall & activate WooCommerce%6$s'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_html( admin_url('plugins.php') ) . '">', '&nbsp;&raquo;</a>'); ?></p>
        </div>
        <?php
    }

    /**
     * Html for database migration notice.
     * @return void
     */
    public static function database_migration_notice() {
        // check if plugin vertion in databse is not same to current stock manager version
        $plugin_version = get_option( 'woo_stock_manager_version', '' );
        if ( $plugin_version != WOO_STOCK_MANAGER_PLUGIN_VERSION ) {
            ?>
            <div id="message" class="error">
                <p><?php esc_html( "The Product Stock Manager & Notifier for WooCommerce is experiencing configuration issues. To ensure proper functioning, kindly deactivate and then activate the plugin." ) ?></p>
            </div>
            <?php
        }
    }

    /**
     * Get the settings arry. Non set value is replaced with default value.
     * @return array
     */
    public static function get_form_settings_array() {
        $general_tab_settings = get_option('woo_stock_manager_general_tab_settings', []);
        $form_customization_tab_settings = get_option('woo_stock_manager_form_customization_tab_settings', []);
        $form_submission_tab_settings = get_option('woo_stock_manager_form_submission_tab_settings', []);
        $email_tab_settings = get_option('woo_stock_manager_email_tab_settings', []);
        
        return [
            'double_opt_in_success'             => $general_tab_settings['double_opt_in_success'] ?? '',
            'shown_interest_text'               => $general_tab_settings['shown_interest_text'] ?? '',
            'alert_success'                     => $form_submission_tab_settings['alert_success'] ?? '',
            'alert_email_exist'                 => $form_submission_tab_settings['alert_email_exist'] ?? '',
            'valid_email'                       => $form_submission_tab_settings['valid_email'] ?? '',
            'alert_unsubscribe_message'         => $form_submission_tab_settings['alert_unsubscribe_message'] ?? '',
            'email_placeholder_text'            => $form_customization_tab_settings['email_placeholder_text'] ?? '',
            'alert_text'                        => $form_customization_tab_settings['alert_text'] ?? '',
            'button_text'                       => $form_customization_tab_settings['button_text'] ?? '',
            'unsubscribe_button_text'           => $form_customization_tab_settings['unsubscribe_button_text'] ?? '',
            'alert_text_color'                  => $form_customization_tab_settings['alert_text_color'] ?? '',
            'button_background_color'           => $form_customization_tab_settings['button_background_color'] ?? '',
            'button_border_color'               => $form_customization_tab_settings['button_border_color'] ?? '',
            'button_text_color'                 => $form_customization_tab_settings['button_text_color'] ?? '',
            'button_background_color_onhover'   => $form_customization_tab_settings['button_background_color_onhover'] ?? '',
            'button_text_color_onhover'         => $form_customization_tab_settings['button_text_color_onhover'] ?? '',
            'button_border_color_onhover'       => $form_customization_tab_settings['button_border_color_onhover'] ?? '',
            'button_font_size'                  => $form_customization_tab_settings['button_font_size'] ?? '',
            'button_border_size'                => $form_customization_tab_settings['button_border_size'] ?? '',
            'button_border_radious'             => $form_customization_tab_settings['button_border_radious'] ?? '',
            'ban_email_domain_text'             => $email_tab_settings['ban_email_domain_text'] ?? '',
            'ban_email_address_text'            => $email_tab_settings['ban_email_address_text'] ?? '',
        ];
    }
}