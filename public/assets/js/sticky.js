$(document).ready(function () {
    var $activeItem = $("#my-sticky .selected-item.active");
    if ($activeItem.length) {
        // Cari parent scrollable terdekat (bisa .meeting-sidebar atau parent .custom-scrollbar)
        var $parent = $activeItem.closest(
            ".custom-scrollbar, .meeting-sidebar"
        );
        if ($parent.length) {
            // Hitung posisi relatif item aktif terhadap parent
            var parentRect = $parent[0].getBoundingClientRect();
            var itemRect = $activeItem[0].getBoundingClientRect();
            // Scroll sehingga item aktif berada di tengah parent
            var offset =
                itemRect.top +
                itemRect.height / 2 -
                (parentRect.top + parentRect.height / 2);
            $parent.scrollTop($parent.scrollTop() + offset);
        }
    }

    var $stickyElement = $("#my-sticky");
    var $parentElement = $stickyElement.parent();
    var stickyTop = 0; // Jarak sticky dari atas, misal 0 atau 100

    function updateSticky() {
        var parentTop = $parentElement[0].offsetTop;
        var parentBottom =
            $parentElement[0].offsetTop + $parentElement[0].offsetHeight;
        var stickyHeight = $stickyElement.outerHeight();
        var scrollY = window.scrollY || window.pageYOffset;
        var stickyOffset = parentTop; // stickyOffset diambil dari parent, bukan stickyElement

        // Hitung posisi maksimal sticky agar tidak keluar dari parent
        var maxTop = parentBottom - stickyHeight - stickyOffset;

        if (scrollY > stickyOffset - stickyTop) {
            var newTop = scrollY - stickyOffset + stickyTop;
            if (newTop > maxTop) {
                $stickyElement.css("top", maxTop + "px");
            } else if (newTop < 0) {
                $stickyElement.css("top", "0px");
            } else {
                $stickyElement.css("top", newTop + "px");
            }
        } else {
            $stickyElement.css("top", "0px");
        }
    }

    $(window).on("scroll resize", updateSticky);
    // Inisialisasi posisi sticky saat load
    updateSticky();
});