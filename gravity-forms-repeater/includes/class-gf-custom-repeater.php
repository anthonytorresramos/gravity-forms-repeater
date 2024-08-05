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
            $input_name = "input_" . $input_id;
            $input_value = esc_attr($value);
        
            // Use default attributes or define custom ones here
            $field_attributes = $this->get_input_attributes();
        
            // Repeater container
            $input = '<div class="gf-repeater">';
        
            // Add static header row
            $input .= '
                <div class="repeater-header">
                    <div class="repeater-header-row">
                        <div class="header-item">Appliance</div>
                        <div class="header-item">Other Appliance</div>
                        <div class="header-item">Qty</div>
                        <div class="header-item">Watts</div>
                        <div class="header-item">Hours Usage (SUMMER)</div>
                        <div class="header-item">Hours Usage (WINTER)</div>
                        <div class="header-item">kWh/day (SUMMER)</div>
                        <div class="header-item">kWh/day (WINTER)</div>
                        <div class="header-item">Remove</div>
                    </div>
                </div>';
        
            // Repeater rows container with prepopulated data
            $input .= '<div class="repeater-rows">';
        
            // Add prepopulated rows
            $input .= $this->get_prepopulated_repeater_rows($input_id);
        
            $input .= '</div>'; // End of repeater-rows
        
            // Add repeater button below the rows
            $input .= '<button type="button" class="add-repeater-row">Add More Appliances</button>';
        
            $input .= '</div>'; // End of gf-repeater
        
            // Totals container
            $input .= '<div class="total-container">';
            $input .= '<p>Total kWh/day (SUMMER): <span id="total-kwh-day-summer" class="total-kwh-day-summer">0.00</span></p>';
            $input .= '<p>Total kWh/day (WINTER): <span id="total-kwh-day-winter" class="total-kwh-day-winter">0.00</span></p>';
            $input .= '<p>Total Watts: <span id="total-watts" class="total-watts">0</span></p>';
            $input .= '</div>';
        
            return $input;
        }
        
        
        
        
        

        public function get_repeater_row_html($input_id)
        {
            ob_start();
        ?>
            <div class="repeater-row">
                <select name="input_<?php echo $input_id; ?>[appliance][]" class="appliance-select">
                    <optgroup label="HEATING">
                        <option value="Elec Hot Water (type?)|HEATING">Elec Hot Water (type?)</option>
                        <option value="Air Conditioning Elec Input|HEATING">Air Conditioning Elec Input</option>
                        <option value="Bar / Elec Heaters|HEATING">Bar / Elec Heaters</option>
                    </optgroup>
                    <optgroup label="KITCHEN">
                        <option value="Elec Oven|KITCHEN">Elec Oven</option>
                        <option value="Elect Cook Top|KITCHEN">Elect Cook Top</option>
                        <option value="Dishwasher|KITCHEN">Dishwasher</option>
                        <option value="Kettle|KITCHEN">Kettle</option>
                        <option value="Toaster|KITCHEN">Toaster</option>
                        <option value="Fridge|KITCHEN">Fridge</option>
                    </optgroup>
                    <optgroup label="PUMPS">
                        <option value="Pool Pump|PUMPS">Pool Pump</option>
                        <option value="Sewage System Pump etc|PUMPS">Sewage System Pump etc</option>
                        <option value="Water Pump|PUMPS">Water Pump</option>
                        <option value="Washing Machine (Cold W)|PUMPS">Washing Machine (Cold W)</option>
                        <option value="LED Lights EXTERNAL|PUMPS">LED Lights EXTERNAL</option>
                        <option value="LED Lights|PUMPS">LED Lights</option>
                    </optgroup>
                    <optgroup label="Other">
                        <option value="Other|Other">Other</option>
                    </optgroup>
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
            $prepopulated_data = [
                ["Elec Hot Water (type?)|HEATING", "", 0, 3500, 3, 3],
                ["Air Conditioning Elec Input|HEATING", "", 1, 2500, 2, 2],
                ["Bar / Elec Heaters|HEATING", "", 0, 1000, 0, 2],
                ["Elec Oven|KITCHEN", "", 1, 1500, 0.5, 0.5],
                ["Elect Cook Top|KITCHEN", "", 0, 1000, 0.5, 0.5],
                ["Dishwasher|KITCHEN", "", 1, 2000, 1, 1],
                ["Kettle|KITCHEN", "", 1, 2000, 0.2, 0.2],
                ["Toaster|KITCHEN", "", 0, 1500, 0.1, 0.1],
                ["Fridge|KITCHEN", "", 1, 200, 4, 4],
                ["Pool Pump|PUMPS", "", 0, 500, 5, 2],
                ["Sewage System Pump etc|PUMPS", "", 0, 400, 12, 12],
                ["Water Pump|PUMPS", "", 0, 1000, 1, 1],
                ["Washing Machine (Cold W)|PUMPS", "", 0, 500, 1, 1],

                ["Other|Other", "LED FLOOD LIGHT", 2, 50, 4, 4],
                ["Other|Other", "LED lights", 0, 20, 4, 4],
                ["Other|Other", "Ceiling Fans", 2, 30, 2, 0],
                ["Other|Other", "TV", 1, 50, 2, 2],
                ["Other|Other", "LED lights", 20, 8, 4, 4],



    
            ];
        
            ob_start();
        
            foreach ($prepopulated_data as $row) {
                list($appliance, $other_appliance, $qty, $watts, $hours_summer, $hours_winter) = $row;
                $kwh_day_summer = ($qty * $watts * $hours_summer) / 1000;
                $kwh_day_winter = ($qty * $watts * $hours_winter) / 1000;
        ?>
        
                <div class="repeater-row">
                    <select name="input_<?php echo $input_id; ?>[appliance][]" class="appliance-select">
                        <optgroup label="HEATING">
                            <option value="Elec Hot Water (type?)|HEATING" <?php selected($appliance, "Elec Hot Water (type?)|HEATING"); ?>>Elec Hot Water (type?)</option>
                            <option value="Air Conditioning Elec Input|HEATING" <?php selected($appliance, "Air Conditioning Elec Input|HEATING"); ?>>Air Conditioning Elec Input</option>
                            <option value="Bar / Elec Heaters|HEATING" <?php selected($appliance, "Bar / Elec Heaters|HEATING"); ?>>Bar / Elec Heaters</option>
                        </optgroup>
                        <optgroup label="KITCHEN">
                            <option value="Elec Oven|KITCHEN" <?php selected($appliance, "Elec Oven|KITCHEN"); ?>>Elec Oven</option>
                            <option value="Elect Cook Top|KITCHEN" <?php selected($appliance, "Elect Cook Top|KITCHEN"); ?>>Elect Cook Top</option>
                            <option value="Dishwasher|KITCHEN" <?php selected($appliance, "Dishwasher|KITCHEN"); ?>>Dishwasher</option>
                            <option value="Kettle|KITCHEN" <?php selected($appliance, "Kettle|KITCHEN"); ?>>Kettle</option>
                            <option value="Toaster|KITCHEN" <?php selected($appliance, "Toaster|KITCHEN"); ?>>Toaster</option>
                            <option value="Fridge|KITCHEN" <?php selected($appliance, "Fridge|KITCHEN"); ?>>Fridge</option>
                        </optgroup>
                        <optgroup label="PUMPS">
                            <option value="Pool Pump|PUMPS" <?php selected($appliance, "Pool Pump|PUMPS"); ?>>Pool Pump</option>
                            <option value="Sewage System Pump etc|PUMPS" <?php selected($appliance, "Sewage System Pump etc|PUMPS"); ?>>Sewage System Pump etc</option>
                            <option value="Water Pump|PUMPS" <?php selected($appliance, "Water Pump|PUMPS"); ?>>Water Pump</option>
                            <option value="Washing Machine (Cold W)|PUMPS" <?php selected($appliance, "Washing Machine (Cold W)|PUMPS"); ?>>Washing Machine (Cold W)</option>
                            <option value="LED Lights EXTERNAL|PUMPS" <?php selected($appliance, "LED Lights EXTERNAL|PUMPS"); ?>>LED Lights EXTERNAL</option>
                            <option value="LED Lights|PUMPS" <?php selected($appliance, "LED Lights|PUMPS"); ?>>LED Lights</option>
                        </optgroup>
                        <optgroup label="Other">
                            <option value="Other|Other" <?php selected($appliance, "Other|Other"); ?>>Other</option>
                        </optgroup>
                    </select>
                    <input type="text" name="input_<?php echo $input_id; ?>[other_appliance][]" class="other-appliance" value="<?php echo esc_attr($other_appliance); ?>" placeholder="Other Appliance" <?php echo ($appliance === "Other|Other") ? '' : 'disabled'; ?> />
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
        
            return ob_get_clean();
        }
        
    }
}
?>
