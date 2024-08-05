<?php
if ( class_exists( 'GFForms' ) ) {

    class GF_Custom_Repeater_Field extends GF_Field {

        public $type = 'repeater_custom'; // Use a unique type name

        // Field title in the form editor
        public function get_form_editor_field_title() {
            return esc_attr__( 'Custom Repeater', 'gravityforms' );
        }

        // Settings to display in the form editor
        public function get_form_editor_field_settings() {
            return array(
                'label_setting',
                'description_setting',
            );
        }

        // Button to add the field in the form editor
        public function get_form_editor_button() {
            return array(
                'group' => 'advanced_fields', // Add the field to the advanced fields group
                'text'  => $this->get_form_editor_field_title(),
            );
        }

        // Support conditional logic
        public function is_conditional_logic_supported() {
            return true;
        }

        // Display field input in form
        public function get_field_input( $form, $value = '', $entry = null ) {
            $input_id = $this->id;
            $input_name = "input_" . $input_id;
            $input_value = esc_attr( $value );

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
            $input .= '<p>Total kWh/day: <span class="total-kwh-day">0.00</span></p>';
            $input .= '<p>Total Watts: <span class="total-watts">0</span></p>';
            $input .= '</div>';

            return $input;
        }

        // Generate HTML for a repeater row
        public function get_repeater_row_html($input_id) {
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
                <input type="number" name="input_<?php echo $input_id; ?>[hours_usage][]" placeholder="Hours Usage" min="0" step="0.1" />
                <input type="number" name="input_<?php echo $input_id; ?>[kwh_day][]" class="kwh-day" placeholder="kWh/day" readonly />
                <button type="button" class="remove-repeater-row">−</button>
            </div>
            <?php
            return ob_get_clean();
        }

        // Save entry value as JSON
        public function get_value_save_entry( $value, $form, $input_name, $entry_id, $entry ) {
            if ( is_array( $value ) ) {
                // Encode the value as JSON
                return json_encode( $value );
            }
            return $value;
        }

        // Display entry value
        public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
            $values = json_decode( $value, true );

            if ( is_array( $values ) ) {
                $output = [];
                foreach ($values as $entry) {
                    $output[] = sprintf(
                        'Appliance: %s, Qty: %s, Watts: %s, Hours Usage: %s, kWh/day: %s',
                        esc_html($entry['appliance'] ?? 'N/A'),
                        esc_html($entry['quantity'] ?? '0'),
                        esc_html($entry['watts'] ?? '0'),
                        esc_html($entry['hours_usage'] ?? '0'),
                        esc_html($entry['kwh_day'] ?? '0')
                    );
                }
                return implode('<br>', $output);
            }
            return esc_html( $value );
        }

        // Optional: Define custom input attributes if needed
        private function get_input_attributes() {
            return 'class="ginput_container ginput_container_text"'; // You can customize this
        }
    }
}
?>
