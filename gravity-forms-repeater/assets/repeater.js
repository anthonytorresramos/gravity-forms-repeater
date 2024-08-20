jQuery(document).ready(function ($) {
  // Add new repeater row
  $(document).on("click", ".add-repeater-row", function () {
    addRepeaterRow();
  });

  // Click event for appliance cards to create a new row with pre-selected appliance
  $(document).on("click", ".appliance-card", function () {
    var appliance = $(this).data("appliance");
    addRepeaterRow(appliance);
  });

  // Function to add a repeater row
  function addRepeaterRow(preSelectedAppliance = "") {
    var $repeater = $(".gf-repeater");
    var $rows = $repeater.find(".repeater-rows");
    var $newRow = $rows.find(".repeater-row").first().clone();

    // Reset the fields for the new row
    $newRow.find("input").val("");
    $newRow.find(".other-appliance").prop("disabled", true); // Disable by default

    if (preSelectedAppliance) {
      var category = findCategory(preSelectedAppliance);
      var defaults = getApplianceDefaults(preSelectedAppliance, category);

      // Pre-select the appliance in the dropdown
      $newRow.find("select.appliance-select").val(preSelectedAppliance + "|" + category);

      // Set the default values for quantity, watts, hours usage if available
      if (defaults) {
        $newRow.find('input[name$="[quantity][]"]').val(defaults.quantity);
        $newRow.find('input[name$="[watts][]"]').val(defaults.watts);
        $newRow.find('input[name$="[hours_usage_summer][]"]').val(defaults.hours_summer);
        $newRow.find('input[name$="[hours_usage_winter][]"]').val(defaults.hours_winter);

        // Manually trigger calculation for kWh/day
        calculateRowKwh($newRow);
      }

      if (preSelectedAppliance === "Other") {
        $newRow.find(".other-appliance").prop("disabled", false);
      }
    }

    $rows.append($newRow); // Append the new row
  }

  // Function to calculate kWh/day (SUMMER) and kWh/day (WINTER) for a specific row
  function calculateRowKwh($row) {
    var qty = parseFloat($row.find('input[name$="[quantity][]"]').val()) || 0;
    var watts = parseFloat($row.find('input[name$="[watts][]"]').val()) || 0;
    var hoursSummer = parseFloat($row.find('input[name$="[hours_usage_summer][]"]').val()) || 0;
    var hoursWinter = parseFloat($row.find('input[name$="[hours_usage_winter][]"]').val()) || 0;

    var kwhDaySummer = (qty * watts * hoursSummer) / 1000;
    var kwhDayWinter = (qty * watts * hoursWinter) / 1000;

    $row.find('input[name$="[kwh_day_summer][]"]').val(kwhDaySummer.toFixed(2));
    $row.find('input[name$="[kwh_day_winter][]"]').val(kwhDayWinter.toFixed(2));

    calculateTotals(); // Recalculate totals whenever a row is updated
  }

  // Remove repeater row
  $(document).on("click", ".remove-repeater-row", function () {
    $(this).closest(".repeater-row").remove();
    calculateTotals(); // Recalculate totals after removing a row
  });

  // Enable or disable the "Other" field based on the dropdown selection
  $(document).on("change", ".appliance-select", function () {
    var $row = $(this).closest(".repeater-row");
    var selectedValue = $(this).val();
    if (selectedValue) {
      var applianceParts = selectedValue.split("|");
      var appliance = applianceParts[0];
      var category = applianceParts[1];
      var defaults = getApplianceDefaults(appliance, category);

      // Set the default values for quantity, watts, hours usage if available
      if (defaults) {
        $row.find('input[name$="[quantity][]"]').val(defaults.quantity);
        $row.find('input[name$="[watts][]"]').val(defaults.watts);
        $row.find('input[name$="[hours_usage_summer][]"]').val(defaults.hours_summer);
        $row.find('input[name$="[hours_usage_winter][]"]').val(defaults.hours_winter);

        // Manually trigger calculation for kWh/day
        calculateRowKwh($row);
      }

      if (appliance === "Other") {
        $row.find(".other-appliance").prop("disabled", false);
      } else {
        $row.find(".other-appliance").prop("disabled", true).val(""); // Disable and clear "Other" field
      }
    }
  });

  // Calculate kWh/day (SUMMER) and kWh/day (WINTER) for each row when input changes
  $(document).on("input", 'input[name$="[quantity][]"], input[name$="[watts][]"], input[name$="[hours_usage_summer][]"], input[name$="[hours_usage_winter][]"]', function () {
    var $row = $(this).closest(".repeater-row");
    calculateRowKwh($row);
  });

  // Function to find the category for a given appliance
  function findCategory(appliance) {
    var appliances = {
      "Elec Hot Water (type?)": "HEATING",
      "Air Conditioning Elec Input": "HEATING",
      "Bar / Elec Heaters": "HEATING",
      "Elec Oven": "KITCHEN",
      "Elect Cook Top": "KITCHEN",
      Dishwasher: "KITCHEN",
      Kettle: "KITCHEN",
      Toaster: "KITCHEN",
      Fridge: "KITCHEN",
      "Pool Pump": "PUMPS",
      "Sewage System Pump etc": "PUMPS",
      "Water Pump": "PUMPS",
      "Washing Machine (Cold W)": "PUMPS",
      "LED Lights EXTERNAL": "PUMPS",
      "LED Lights": "PUMPS",
      Other: "Other",
    };

    return appliances[appliance] || "";
  }

  // Function to retrieve the default values for a given appliance and category
  function getApplianceDefaults(appliance, category) {
    // Assuming this function mirrors the structure of your PHP appliances array
    var appliances = {
      HEATING: {
        "Elec Hot Water (type?)": {
          defaults: {
            quantity: 1,
            watts: 1100,
            hours_summer: 11,
            hours_winter: 111,
          },
        },
        "Air Conditioning Elec Input": {
          defaults: {
            quantity: 2,
            watts: 2222,
            hours_summer: 22,
            hours_winter: 222,
          },
        },
      },
      KITCHEN: {
        "Elec Oven": {
          defaults: {
            quantity: 3,
            watts: 3300,
            hours_summer: 33,
            hours_winter: 3333,
          },
        },
        "Elect Cook Top": {
          defaults: {
            quantity: 4,
            watts: 4,
            hours_summer: 4,
            hours_winter: 4,
          },
        },
      },
      Other: {
        Other: {
          defaults: {
            quantity: 5,
            watts: 500,
            hours_summer: 55,
            hours_winter: 3555,
          },
        },
      },
    };

    if (appliances[category] && appliances[category][appliance]) {
      return appliances[category][appliance].defaults;
    }
    return null;
  }

  // Calculate total kWh/day (SUMMER), total kWh/day (WINTER), and total watts
  function calculateTotals() {
    var totalKwhSummer = 0;
    var totalKwhWinter = 0;
    var totalWatts = 0;

    // Iterate over each row and sum up kWh/day and watts
    $(".gf-repeater .repeater-row").each(function () {
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
    $("#g_total_summer").text(totalKwhSummer.toFixed(2));
    $("#g_total_winter").text(totalKwhWinter.toFixed(2));
    $("#g_total_watts").text(totalWatts);

    // Update Gravity Forms fields with the calculated totals
    var totalSummerFieldId = "input_31"; // Replace with the actual field ID for Total kWh/day (SUMMER)
    var totalWinterFieldId = "input_32"; // Replace with the actual field ID for Total kWh/day (WINTER)
    var totalWattsFieldId = "input_33"; // Replace with the actual field ID for Total Watts

    // Set the values of the Gravity Forms fields
    $('input[name="' + totalSummerFieldId + '"]').val(totalKwhSummer.toFixed(2));
    $('input[name="' + totalWinterFieldId + '"]').val(totalKwhWinter.toFixed(2));
    $('input[name="' + totalWattsFieldId + '"]').val(totalWatts);
  }

  // Initial call to set values on page load
  calculateTotals();
});
