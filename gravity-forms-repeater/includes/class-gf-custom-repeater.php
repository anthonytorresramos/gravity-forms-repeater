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

            // Repeater container with add button
            $input = '<div class="gf-repeater">';
            $input .= '<button type="button" class="add-repeater-row">+</button>';
            $input .= '<div class="repeater-rows">';

            // Default row structure
            $input .= $this->get_repeater_row_html($input_id);

            $input .= '</div>'; // End of repeater-rows
            $input .= '</div>'; // End of gf-repeater

            // Totals container
            $input .= '<div class="total-container">';
            $input .= '<p>Total kWh/day (SUMMER): <span class="total-kwh-day-summer">0.00</span></p>';
            $input .= '<p>Total kWh/day (WINTER): <span class="total-kwh-day-winter">0.00</span></p>';
            $input .= '<p>Total Watts: <span class="total-watts">0</span></p>';
            $input .= '</div>';

            return $input;
        }

        // Generate HTML for a repeater row
        public function get_repeater_row_html($input_id)
        {
            ob_start();
?>
            <div class="repeater-row">
                <select name="input_<?php echo $input_id; ?>[appliance][]" class="appliance-select">
                    <option value="fridge">Fridge</option>
                    <option value="tv">TV</option>
                    <option value="washing_machine">Washing Machine</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" name="input_<?php echo $input_id; ?>[other_appliance][]" class="other-appliance" placeholder="Other Appliance" style="display:none;" />
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
                // Encode the value as JSON
                return json_encode($value);
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
                    $output .= '<td colspan="7" style="border:1px solid #ddd; padding:8px; text-align:right;">Total Watts: ' . esc_html($values['totals']['total_watts'] ?? '0') . '</td>';
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
    }
}
?>
