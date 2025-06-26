$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// Custom add search option
var $selected;
var $optionsContainer;
var $searchBox;
var $optionsList;
$(document).on("click", ".selected-box", function () {
    $selectBox = $(this).parent();
    $selected = $(this);
    $optionsContainer = $selectBox.find(".options-container");
    $searchBox = $selectBox.find(".search-box input");
    $optionsList = $optionsContainer.find(".selection-option");

    $optionsContainer.toggleClass("active");

    $searchBox.val("");
    filterList("");

    if ($optionsContainer.hasClass("active")) {
        $searchBox.focus();
    }

    $(document)
        .off("click.selectBoxClose")
        .on("click.selectBoxClose", function (e) {
            if (!$(e.target).closest(".select-box").length) {
                $(".options-container.active").removeClass("active");
            }
        });

    $optionsList.off("click").on("click", function (e) {
        $selected.html($(this).find("label").html());
        $optionsContainer.removeClass("active");
    });

    $searchBox.off("keyup").on("keyup", function (e) {
        filterList(e.target.value);
    });

    function filterList(searchTerm) {
        searchTerm = searchTerm.toLowerCase();
        $optionsList.each(function () {
            var $label = $(this).find("label");
            var labelText = $label.text().toLowerCase();
            if (labelText.indexOf(searchTerm) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});
