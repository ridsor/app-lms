// Custom datepicker
(function ($) {
    "use strict";
    //Minimum and Maximum Date
    $(".datepicker-here-minmax").datepicker({
        language: "id",
        minDate: new Date(), // Now can select only dates, which goes after today
        autoClose: true,
        dateFormat: "dd/mm/yyyy",
    });

    $(".datepicker-here").datepicker({
        language: "id",
        autoClose: true,
        dateFormat: "dd/mm/yyyy",
    });

    // Disable Days of week example
    var disabledDays = [0, 6];

    $("#disabled-days").datepicker({
        language: "en",
        onRenderCell: function (date, cellType) {
            if (cellType == "day") {
                var day = date.getDay(),
                    isDisabled = disabledDays.indexOf(day) != -1;
                return {
                    disabled: isDisabled,
                };
            }
        },
    });
})(jQuery);
