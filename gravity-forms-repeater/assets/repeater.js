jQuery(document).ready(function ($) {
    // Add new repeater row
    $(document).on('click', '.add-repeater-row', function () {
        var $repeater = $(this).closest('.gf-repeater');
        var $rows = $repeater.find('.repeater-rows');
        var $newRow = $rows.find('.repeater-row').first().clone();

        // Reset the fields for the new row
        $newRow.find('input').val('');
        $newRow.find('.other-appliance').prop('disabled', true); // Disable by default

        $rows.append($newRow); // Append the new row
    });

    // Remove repeater row
    $(document).on('click', '.remove-repeater-row', function () {
        $(this).closest('.repeater-row').remove();
        calculateTotals(); // Recalculate totals after removing a row
    });

    // Enable or disable the "Other" field based on the dropdown selection
    $(document).on('change', '.appliance-select', function () {
        var $otherField = $(this).closest('.repeater-row').find('.other-appliance');
        if ($(this).val().includes('Other|Other')) {
            $otherField.prop('disabled', false); // Enable the field when "Other" is selected
        } else {
            $otherField.prop('disabled', true).val(''); // Disable the field and clear the value when not selected
        }
    });

    // Calculate kWh/day (SUMMER) and kWh/day (WINTER) for each row
    $(document).on('input', 'input[name$="[quantity][]"], input[name$="[watts][]"], input[name$="[hours_usage_summer][]"], input[name$="[hours_usage_winter][]"]', function () {
        var $row = $(this).closest('.repeater-row');
        var qty = parseFloat($row.find('input[name$="[quantity][]"]').val()) || 0;
        var watts = parseFloat($row.find('input[name$="[watts][]"]').val()) || 0;
        var hoursSummer = parseFloat($row.find('input[name$="[hours_usage_summer][]"]').val()) || 0;
        var hoursWinter = parseFloat($row.find('input[name$="[hours_usage_winter][]"]').val()) || 0;

        var kwhDaySummer = (qty * watts * hoursSummer) / 1000;
        var kwhDayWinter = (qty * watts * hoursWinter) / 1000;

        $row.find('input[name$="[kwh_day_summer][]"]').val(kwhDaySummer.toFixed(2));
        $row.find('input[name$="[kwh_day_winter][]"]').val(kwhDayWinter.toFixed(2));

        calculateTotals(); // Recalculate totals whenever inputs change
    });

    // Calculate total kWh/day (SUMMER), total kWh/day (WINTER), and total watts
    function calculateTotals() {
        var totalKwhSummer = 0;
        var totalKwhWinter = 0;
        var totalWatts = 0;

        // Iterate over each row and sum up kWh/day and watts
        $('.gf-repeater .repeater-row').each(function () {
            var qty = parseFloat($(this).find('input[name$="[quantity][]"]').val()) || 0;
            var watts = parseFloat($(this).find('input[name$="[watts][]"]').val()) || 0;
            var kwhSummer = parseFloat($(this).find('input[name$="[kwh_day_summer][]"]').val()) || 0;
            var kwhWinter = parseFloat($(this).find('input[name$="[kwh_day_winter][]"]').val()) || 0;

            totalKwhSummer += kwhSummer;
            totalKwhWinter += kwhWinter;

            // Multiply quantity by watts and add to total watts
            totalWatts += qty * watts;
        });

        // Update the total fields with static IDs
        $('#g_total_summer').text(totalKwhSummer.toFixed(2));
        $('#g_total_winter').text(totalKwhWinter.toFixed(2));
        $('#g_total_watts').text(totalWatts);

        // Update Gravity Forms fields with the calculated totals
        // Make sure to use the correct naming convention: 'input_' followed by your field ID
        var totalSummerFieldId = 'input_32'; // Replace with the actual field ID for Total kWh/day (SUMMER)
        var totalWinterFieldId = 'input_30'; // Replace with the actual field ID for Total kWh/day (WINTER)
        var totalWattsFieldId = 'input_33';   // Replace with the actual field ID for Total Watts

        // Set the values of the Gravity Forms fields
        $('input[name="' + totalSummerFieldId + '"]').val(totalKwhSummer.toFixed(2));
        $('input[name="' + totalWinterFieldId + '"]').val(totalKwhWinter.toFixed(2));
        $('input[name="' + totalWattsFieldId + '"]').val(totalWatts);
    }

    // Initial call to set values on page load
    calculateTotals();
});
