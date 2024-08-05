<?php
/**
 * Plugin Name: Gravity Forms Custom Repeater Add-On
 * Description: A custom add-on to add repeater functionality to Gravity Forms.
 * Version: 1.0
 * Author: sns
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Register the custom repeater field
add_action('gform_loaded', 'register_custom_repeater_field', 10, 2);
function register_custom_repeater_field()
{
    require_once plugin_dir_path(__FILE__) . 'gravity-forms-repeater/includes/class-gf-custom-repeater.php';
    GF_Fields::register(new GF_Custom_Repeater_Field());
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'enqueue_custom_repeater_scripts');
function enqueue_custom_repeater_scripts()
{
    wp_enqueue_script('gf-custom-repeater', plugin_dir_url(__FILE__) . 'gravity-forms-repeater/assets/repeater.js', array('jquery'), '1.0', true);
    wp_enqueue_style('gf-custom-repeater', plugin_dir_url(__FILE__) . 'gravity-forms-repeater/assets/repeater.css', array(), '1.0');
}

// Handle custom repeater submission
add_action('gform_pre_submission', 'handle_custom_repeater_submission');
function handle_custom_repeater_submission($form)
{
    foreach ($form['fields'] as &$field) {
        if ($field->type === 'repeater_custom') {
            $field_value = rgpost('input_' . $field->id);

            // Log the raw data for debugging
            error_log('Raw submission data: ' . print_r($field_value, true));

            if (is_array($field_value)) {
                $processed_values = [];
                $total_kwh_day_summer = 0;
                $total_kwh_day_winter = 0;
                $total_watts = 0;

                // Restructure data to ensure correct format
                foreach ($field_value['appliance'] as $key => $appliance) {
                    $quantity = floatval($field_value['quantity'][$key] ?? 0);
                    $watts = floatval($field_value['watts'][$key] ?? 0); // Direct watts value
                    $hours_usage_summer = floatval($field_value['hours_usage_summer'][$key] ?? 0);
                    $hours_usage_winter = floatval($field_value['hours_usage_winter'][$key] ?? 0);
                    $kwh_day_summer = floatval($field_value['kwh_day_summer'][$key] ?? 0);
                    $kwh_day_winter = floatval($field_value['kwh_day_winter'][$key] ?? 0);

                    // Add to totals
                    $total_kwh_day_summer += $kwh_day_summer;
                    $total_kwh_day_winter += $kwh_day_winter;
                    $total_watts += $watts; // Sum of all watts

                    $processed_values[] = [
                        'appliance' => sanitize_text_field($appliance),
                        'other_appliance' => sanitize_text_field($field_value['other_appliance'][$key] ?? ''),
                        'quantity' => $quantity,
                        'watts' => $watts,
                        'hours_usage_summer' => $hours_usage_summer,
                        'hours_usage_winter' => $hours_usage_winter,
                        'kwh_day_summer' => $kwh_day_summer,
                        'kwh_day_winter' => $kwh_day_winter,
                    ];
                }

                // Include totals in the processed data
                $processed_values['totals'] = [
                    'total_kwh_day_summer' => $total_kwh_day_summer,
                    'total_kwh_day_winter' => $total_kwh_day_winter,
                    'total_watts' => $total_watts,
                ];

                // Log the processed data
                error_log('Processed submission data: ' . print_r($processed_values, true));

                $_POST['input_' . $field->id] = json_encode($processed_values); // Convert array to JSON string
            }
        }
    }
}
?>
