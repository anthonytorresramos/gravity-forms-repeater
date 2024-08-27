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

// Global appliances array
function gf_custom_repeater_get_appliances() {
    return [
        "HEATING" => [
            "Elec Hot Water (type?)" => [
                "label" => "Heating Appliance",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 3500,
                    "hours_summer" => 3,
                    "hours_winter" => 3,
                ],
            ],
            "Air Conditioning E load" => [
                "label" => "Air Conditioning",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 2500,
                    "hours_summer" => 2,
                    "hours_winter" => 2,
                ],
            ],
            "Bar or elec heaters" => [
                "label" => "Bar or Electric Heaters",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1000,
                    "hours_summer" => 0,
                    "hours_winter" => 2,
                ],
            ],
            "Elect cook top" => [
                "label" => "Electric Cook Top",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1000,
                    "hours_summer" => 0.5,
                    "hours_winter" => 0.5,
                ],
            ],
        ],
        "KITCHEN" => [
            "Elec Oven" => [
                "label" => "Electric Oven",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1500,
                    "hours_summer" => 0.5,
                    "hours_winter" => 0.5,
                ],
            ],
            "Elect cook top" => [
                "label" => "Electric Cook Top",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1000,
                    "hours_summer" => 0.5,
                    "hours_winter" => 0.5,
                ],
            ],
            "Dishwasher" => [
                "label" => "Dishwasher",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 2000,
                    "hours_summer" => 1,
                    "hours_winter" => 1,
                ],
            ],
            "Kettle" => [
                "label" => "Kettle",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 2000,
                    "hours_summer" => 0.2,
                    "hours_winter" => 0.2,
                ],
            ],
            "Toaster" => [
                "label" => "Toaster",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1500,
                    "hours_summer" => 0.1,
                    "hours_winter" => 0.1,
                ],
            ],
      
            "Fridge Freezer" => [
                "label" => "Fridge Freezer",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 175,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
            "Chest Freezer" => [
                "label" => "Chest Freezer",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 150,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
            "Air Fryer" => [
                "label" => "Air Fryer",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1500,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
        ],
        "PUMPS" => [
            "Pool pump" => [
                "label" => "Pool Pump",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 500,
                    "hours_summer" => 5,
                    "hours_winter" => 2,
                ],
            ],
            "Sewage sytem pump etc" => [
                "label" => "Sewage System Pump",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 400,
                    "hours_summer" => 12,
                    "hours_winter" => 12,
                ],
            ],
            "Water pump" => [
                "label" => "Water Pump",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 1000,
                    "hours_summer" => 1,
                    "hours_winter" => 1,
                ],
            ],
            "Washing machine (cold w)" => [
                "label" => "Washing Machine (Cold Water)",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 500,
                    "hours_summer" => 1,
                    "hours_winter" => 1,
                ],
            ],
        ],
        "LIGHTING/ENTERTAINMENT" => [
            "LED FLOOD LIGHT" => [
                "label" => "LED Flood Light",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 50,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
            "LED lights" => [
                "label" => "LED Lights",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 20,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
            "Ceiling Fans" => [
                "label" => "Ceiling Fans",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 30,
                    "hours_summer" => 2,
                    "hours_winter" => 0,
                ],
            ],
            "TV" => [
                "label" => "Television",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 1,
                    "watts" => 50,
                    "hours_summer" => 2,
                    "hours_winter" => 2,
                ],
            ],
            "LED lights (multiple)" => [
                "label" => "LED Lights (Multiple)",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 20,
                    "watts" => 8,
                    "hours_summer" => 4,
                    "hours_winter" => 4,
                ],
            ],
        ],
        "Other" => [
            "Other" => [
                "label" => "Other Appliance",
                "image" => "/image-path-here",
                "defaults" => [
                    "quantity" => 5,
                    "watts" => 500,
                    "hours_summer" => 55,
                    "hours_winter" => 3555,
                ],
            ],
        ],
    ];
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
function enqueue_custom_repeater_scripts() {
    wp_enqueue_script('gf-custom-repeater', plugin_dir_url(__FILE__) . 'gravity-forms-repeater/assets/repeater.js', array('jquery'), '1.0', true);

    // Pass appliances data to JavaScript
    wp_localize_script('gf-custom-repeater', 'gfRepeaterData', [
        'appliances' => gf_custom_repeater_get_appliances(),
    ]);
    
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

// Add a custom tooltip text field in the General section below the description box
function add_tooltip_js_to_gravity_form() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.gf-tooltip').each(function() {
            var tooltipText = $(this).find('.gfield_description').text();
            $(this).append('<span class="tooltip-text">' + tooltipText + '</span>');
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'add_tooltip_js_to_gravity_form');


?>
