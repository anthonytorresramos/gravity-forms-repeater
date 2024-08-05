jQuery(document).ready(function ($) {
    // Add new repeater row
    $(document).on('click', '.add-repeater-row', function () {
        var $repeater = $(this).closest('.gf-repeater');
        var $rows = $repeater.find('.repeater-rows');
        var $newRow = $rows.find('.repeater-row').first().clone();

        // Reset the fields for the new row
        $newRow.find('input').val(''); 
        $newRow.find('.other-appliance').hide(); // Hide the other appliance field by default

        $rows.append($newRow); // Append the new row
    });

    // Remove repeater row
    $(document).on('click', '.remove-repeater-row', function () {
        $(this).closest('.repeater-row').remove();
        calculateTotals(); // Recalculate totals after removing a row
    });

    // Show other field if "Other" is selected
    $(document).on('change', '.appliance-select', function () {
        var $otherField = $(this).closest('.repeater-row').find('.other-appliance');
        if ($(this).val() === 'other') {
            $otherField.show();
        } else {
            $otherField.hide().val('');
        }
    });

    // Calculate kWh/day for each row
    $(document).on('input', 'input[name="quantity[]"], input[name="watts[]"], input[name="hours_usage[]"]', function () {
        var $row = $(this).closest('.repeater-row');
        var qty = parseFloat($row.find('input[name="quantity[]"]').val()) || 0;
        var watts = parseFloat($row.find('input[name="watts[]"]').val()) || 0;
        var hours = parseFloat($row.find('input[name="hours_usage[]"]').val()) || 0;

        var kwhDay = (qty * watts * hours) / 1000;
        $row.find('.kwh-day').val(kwhDay.toFixed(2));

        calculateTotals(); // Recalculate totals whenever inputs change
    });

    // Calculate total kWh/day and total watts
    function calculateTotals() {
        var totalKwh = 0;
        var totalWatts = 0;

        // Iterate over each row and sum up kWh/day and watts
        $('.gf-repeater .repeater-row').each(function () {
            var kwh = parseFloat($(this).find('.kwh-day').val()) || 0;
            var watts = parseFloat($(this).find('input[name="watts[]"]').val()) || 0;

            totalKwh += kwh;
            totalWatts += watts;
        });

        // Update the total fields
        $('.total-kwh-day').text(totalKwh.toFixed(2));
        $('.total-watts').text(totalWatts);
    }
});
