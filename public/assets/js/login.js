// Password show hide JS

(function ($) {
    "use strict";
    $(".show-hide").show();
    $(".show-hide span").addClass("show");

    $(".show-hide span").click(function () {
        if ($(this).hasClass("show")) {
            $('input[name="password"]').attr("type", "text");
            $(this).addClass("show");
        } else {
            $('input[name="password"]').attr("type", "password");
            $(this).removeClass("show");
        }
    });
    $('form button[type="submit"]').on("click", function () {
        $(".show-hide span").addClass("show");
        $(".show-hide")
            .parent()
            .find('input[name="password"]')
            .attr("type", "password");
    });
})(jQuery);
