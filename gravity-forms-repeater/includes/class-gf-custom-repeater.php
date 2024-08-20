<?php
if (class_exists('GFForms')) {

    class GF_Custom_Repeater_Field extends GF_Field
    {
        public $type = 'repeater_custom'; // Use a unique type name

        // Field title in the form editor
        public function get_form_editor_field_title()
        {
            return esc_attr__('Custom Repeater', 'gravityforms');
        }

        // Settings to display in the form editor
        public function get_form_editor_field_settings()
        {
            return array(
                'label_setting',
                'description_setting',
            );
        }

        // Button to add the field in the form editor
        public function get_form_editor_button()
        {
            return array(
                'group' => 'advanced_fields', // Add the field to the advanced fields group
                'text'  => $this->get_form_editor_field_title(),
            );
        }

        // Support conditional logic
        public function is_conditional_logic_supported()
        {
            return true;
        }

        // Display field input in form
public function get_field_input($form, $value = '', $entry = null)
{
    $input_id = $this->id;

    // Get the global appliances array
    $appliances = gf_custom_repeater_get_appliances();

    // Repeater container
    $input = '<div class="gf-repeater">';

    // Appliances category cards
    $input .= '<div class="repeater-categories">';
    foreach ($appliances as $category => $items) {
        $input .= '<div class="repeater-category">';
        $input .= '<h4>' . esc_html($category) . '</h4>';
        foreach ($items as $appliance => $details) {
            $input .= '<div class="appliance-card" data-appliance="' . esc_attr($appliance) . '">';
            $input .= '<p>' . esc_html($details['label']) . '</p>';
            $input .= '</div>';
        }
        $input .= '</div>';
    }
    $input .= '</div>';

    // Header row
    $input .= '<div class="repeater-header">';
    $input .= '<div class="header-cell" title="Appliance">Appliances</div>';
    $input .= '<div class="header-cell" title="Other Appliance">Others</div>';
    $input .= '<div class="header-cell" title="Quantity">Qty</div>';
    $input .= '<div class="header-cell" title="Watts">Watts</div>';
    $input .= '<div class="header-cell" title="Hours Usage (SUMMER)">Hours Use (S)</div>';
    $input .= '<div class="header-cell" title="Hours Usage (WINTER)">Hours Use (W)</div>';
    $input .= '<div class="header-cell" title="kWh/day (SUMMER)">kWh/day(S)</div>';
    $input .= '<div class="header-cell" title="kWh/day (WINTER)">kWh/day(W)</div>';
    $input .= '<div class="header-cell"></div>';
    $input .= '</div>';

    // Repeater rows (initial empty row)
    $input .= '<div class="repeater-rows">';
    $input .= $this->get_repeater_row_html($input_id);
    $input .= '</div>';

    // Add button
    $input .= '<button type="button" class="add-repeater-row">Add More Appliances</button>';
    $input .= '</div>'; // End of gf-repeater

    // Totals container
    $input .= '<div class="total-container">';
    $input .= '<p>Total kWh/day (SUMMER): <span id="g_total_summer" class="total-kwh-day-summer">0.00</span></p>';
    $input .= '<p>Total kWh/day (WINTER): <span id="g_total_winter" class="total-kwh-day-winter">0.00</span></p>';
    $input .= '<p>Total Watts: <span id="g_total_watts" class="total-watts">0</span></p>';
    $input .= '</div>';

    return $input;
}


        // Generate HTML for each repeater row
        public function get_repeater_row_html($input_id)
        {
            $appliances = gf_custom_repeater_get_appliances();
            ob_start();
            ?>
            <div class="repeater-row">
                <select name="input_<?php echo $input_id; ?>[appliance][]" class="appliance-select">
                    <?php foreach ($appliances as $category => $items) : ?>
                        <optgroup label="<?php echo esc_attr($category); ?>">
                            <?php foreach ($items as $appliance => $details) : ?>
                                <option value="<?php echo esc_attr($appliance . '|' . $category); ?>">
                                    <?php echo esc_html($appliance); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="input_<?php echo $input_id; ?>[other_appliance][]" class="other-appliance" placeholder="Other Appliance" disabled />
                <input type="number" name="input_<?php echo $input_id; ?>[quantity][]" placeholder="Qty" min="1" />
                <input type="number" name="input_<?php echo $input_id; ?>[watts][]" placeholder="Watts" min="0" />
                <input type="number" name="input_<?php echo $input_id; ?>[hours_usage_summer][]" placeholder="Hours Usage (SUMMER)" min="0" step="0.1" />
                <input type="number" name="input_<?php echo $input_id; ?>[hours_usage_winter][]" placeholder="Hours Usage (WINTER)" min="0" step="0.1" />
                <input type="number" name="input_<?php echo $input_id; ?>[kwh_day_summer][]" class="kwh-day-summer" placeholder="kWh/day (SUMMER)" readonly />
                <input type="number" name="input_<?php echo $input_id; ?>[kwh_day_winter][]" class="kwh-day-winter" placeholder="kWh/day (WINTER)" readonly />
                <button type="button" class="remove-repeater-row">−</button>
            </div>
            <?php
            return ob_get_clean();
        }

        // Save entry value as JSON
        public function get_value_save_entry($value, $form, $input_name, $entry_id, $entry)
        {
            if (is_array($value)) {
                $processed_values = [];
                foreach ($value['appliance'] as $key => $appliance) {
                    list($appliance_name, $category) = explode('|', $appliance);

                    // Capture other appliance name if selected
                    $other_appliance = sanitize_text_field($value['other_appliance'][$key] ?? '');
                    if ($appliance_name === 'Other' && !empty($other_appliance)) {
                        $appliance_name = 'Other'; // Keep the appliance as "Other"
                        $other_column = $other_appliance; // Store the custom appliance name in a separate column
                    } else {
                        $other_column = '-'; // Use a dash for non-custom entries
                    }

                    $processed_values[] = [
                        'appliance' => $appliance_name,
                        'other_appliance' => $other_column,
                        'quantity' => floatval($value['quantity'][$key] ?? 0),
                        'watts' => floatval($value['watts'][$key] ?? 0),
                        'hours_usage_summer' => floatval($value['hours_usage_summer'][$key] ?? 0),
                        'hours_usage_winter' => floatval($value['hours_usage_winter'][$key] ?? 0),
                        'kwh_day_summer' => floatval($value['kwh_day_summer'][$key] ?? 0),
                        'kwh_day_winter' => floatval($value['kwh_day_winter'][$key] ?? 0),
                    ];
                }

                // Include totals in the processed data
                $processed_values['totals'] = [
                    'total_kwh_day_summer' => array_sum(array_column($processed_values, 'kwh_day_summer')),
                    'total_kwh_day_winter' => array_sum(array_column($processed_values, 'kwh_day_winter')),
                    'total_watts' => array_sum(array_column($processed_values, 'watts')),
                ];

                return json_encode($processed_values);
            }
            return $value;
        }

        // Display entry value in table format
        public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen')
        {
            $values = json_decode($value, true);

            if (is_array($values)) {
                $output = '<table style="width:100%; border-collapse:collapse;">';
                $output .= '<thead><tr>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Appliance</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Other</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Qty</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Watts</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Hours Usage (SUMMER)</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">Hours Usage (WINTER)</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">kWh/day (SUMMER)</th>';
                $output .= '<th style="border:1px solid #ddd; padding:8px;">kWh/day (WINTER)</th>';
                $output .= '</tr></thead><tbody>';

                foreach ($values as $entry) {
                    if (isset($entry['appliance'])) { // Check to skip the totals part
                        $output .= '<tr>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['appliance'] ?? 'N/A') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['other_appliance'] ?? '-') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['quantity'] ?? '0') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['watts'] ?? '0') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['hours_usage_summer'] ?? '0') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['hours_usage_winter'] ?? '0') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['kwh_day_summer'] ?? '0') . '</td>';
                        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($entry['kwh_day_winter'] ?? '0') . '</td>';
                        $output .= '</tr>';
                    }
                }

                // Add totals row
                if (isset($values['totals'])) {
                    $output .= '<tr style="font-weight:bold;">';
                    $output .= '<td colspan="5" style="border:1px solid #ddd; padding:8px; text-align:right;">Total kWh/day (SUMMER):</td>';
                    $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($values['totals']['total_kwh_day_summer'] ?? '0.00') . '</td>';
                    $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($values['totals']['total_kwh_day_winter'] ?? '0.00') . '</td>';
                    $output .= '</tr>';
                    $output .= '<tr style="font-weight:bold;">';
                    $output .= '<td colspan="8" style="border:1px solid #ddd; padding:8px; text-align:right;">Total Watts: ' . esc_html($values['totals']['total_watts'] ?? '0') . '</td>';
                    $output .= '</tr>';
                }

                $output .= '</tbody></table>';

                return $output;
            }
            return esc_html($value);
        }

        // Optional: Define custom input attributes if needed
        private function get_input_attributes()
        {
            return 'class="ginput_container ginput_container_text"'; // You can customize this
        }

private function get_prepopulated_repeater_rows($input_id)
{
    // Fetch appliances array with defaults
    $appliances = gf_custom_repeater_get_appliances();
    
    ob_start();

    foreach ($appliances as $category => $items) {
        foreach ($items as $appliance_name => $details) {
            // Extract default values
            $qty = $details['defaults']['quantity'];
            $watts = $details['defaults']['watts'];
            $hours_summer = $details['defaults']['hours_summer'];
            $hours_winter = $details['defaults']['hours_winter'];
            $kwh_day_summer = ($qty * $watts * $hours_summer) / 1000;
            $kwh_day_winter = ($qty * $watts * $hours_winter) / 1000;

            ?>
            <div class="repeater-row">
                <select name="input_<?php echo $input_id; ?>[appliance][]" class="appliance-select">
                    <?php foreach ($appliances as $category_key => $category_items) : ?>
                        <optgroup label="<?php echo esc_attr($category_key); ?>">
                            <?php foreach ($category_items as $name => $item_details) : ?>
                                <option value="<?php echo esc_attr($name . '|' . $category_key); ?>" <?php selected($appliance_name, $name . '|' . $category_key); ?>>
                                    <?php echo esc_html($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="input_<?php echo $input_id; ?>[other_appliance][]" class="other-appliance" value="" placeholder="Other Appliance" <?php echo ($appliance_name === "Other") ? '' : 'disabled'; ?> />
                <input type="number" name="input_<?php echo $input_id; ?>[quantity][]" value="<?php echo esc_attr($qty); ?>" placeholder="Qty" min="0" />
                <input type="number" name="input_<?php echo $input_id; ?>[watts][]" value="<?php echo esc_attr($watts); ?>" placeholder="Watts" min="0" />
                <input type="number" name="input_<?php echo $input_id; ?>[hours_usage_summer][]" value="<?php echo esc_attr($hours_summer); ?>" placeholder="Hours Usage (SUMMER)" min="0" step="0.1" />
                <input type="number" name="input_<?php echo $input_id; ?>[hours_usage_winter][]" value="<?php echo esc_attr($hours_winter); ?>" placeholder="Hours Usage (WINTER)" min="0" step="0.1" />
                <input type="number" name="input_<?php echo $input_id; ?>[kwh_day_summer][]" class="kwh-day-summer" value="<?php echo esc_attr(number_format($kwh_day_summer, 2)); ?>" placeholder="kWh/day (SUMMER)" readonly />
                <input type="number" name="input_<?php echo $input_id; ?>[kwh_day_winter][]" class="kwh-day-winter" value="<?php echo esc_attr(number_format($kwh_day_winter, 2)); ?>" placeholder="kWh/day (WINTER)" readonly />
                <button type="button" class="remove-repeater-row">−</button>
            </div>
            <?php
        }
    }

    return ob_get_clean();
}

    }
}
?>
