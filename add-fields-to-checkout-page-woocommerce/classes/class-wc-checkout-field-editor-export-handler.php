<?php
if (!defined('ABSPATH')) exit;

class WC_Checkout_Field_Editor_Export_Handler {

    private $fields;

    /**
     * __construct()
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */
    public function __construct() {
        $this->fields = $this->get_fields();

        // Customer / Order CSV Export column headers/data
        add_filter('wc_customer_order_csv_export_order_headers', array($this, 'wcfe_order_csv_export_order_headers'), 10, 2);
        add_filter('wc_customer_order_csv_export_order_row', array($this, 'wcfe_customer_order_csv_export_order_row'), 10, 4);
    }

    /**
     * Adds support for Customer/Order CSV Export by adding a vendor column header
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */
    public function wcfe_order_csv_export_order_headers($headers, $csv_generator) {
        $field_headers = array();
        foreach ($this->fields as $name => $options) {
            $field_headers[$name] = $name;
        }
        return array_merge($headers, $field_headers);
    }

    /**
     * Adds support for Customer/Order CSV Export by adding checkout editor field data
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */
    public function wcfe_customer_order_csv_export_order_row($order_data, $order, $csv_generator) {
        $field_data = array();

        foreach ($this->fields as $name => $options) {
            $field_data[$name] = get_post_meta($order->id, $name, true);
        }

        $new_order_data = array();

        if (isset($csv_generator->order_format) && ($csv_generator->order_format == 'default_one_row_per_item' || $csv_generator->order_format == 'legacy_one_row_per_item')) {
            foreach ($order_data as $data) {
                $new_order_data[] = array_merge($field_data, (array)$data);
            }
        } else {
            $new_order_data = array_merge($field_data, $order_data);
        }

        return $new_order_data;
    }

    /**
     * Get all checkout fields
     *
     * @package WooCommerce Checkout & Account Field Editor
     * @since 1.0
     */
    private function get_fields() {
        $fields = array();

        $billing_fields = get_option('wc_fields_billing');
        if ($billing_fields !== false) {
            $fields = array_merge($fields, $billing_fields);
        }

        $shipping_fields = get_option('wc_fields_shipping');
        if ($shipping_fields !== false) {
            $fields = array_merge($fields, $shipping_fields);
        }

        $additional_fields = get_option('wc_fields_additional');
        if ($additional_fields !== false) {
            $fields = array_merge($fields, $additional_fields);
        }

        return $fields;
    }
}