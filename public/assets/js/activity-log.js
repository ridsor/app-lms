let t;

$(function () {
    const params = getQueryParams();
    if (params.pengguna) {
        $("#user-filter").val(params.pengguna);
    }
    if (params.rentang_waktu_dari) {
        $("#start-date-filter").val(params.rentang_waktu_dari);
    }
    if (params.rentang_waktu_sampai) {
        $("#end-date-filter").val(params.rentang_waktu_sampai);
    }

    let columns = [
        {
            data: null,
            name: "No",
            orderable: false,
            searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
            className: "text-center",
            width: "40px",
        },
        { data: "Nama Aktifitas", name: "log_name" },
        { data: "Informasi", name: "description", searchable: false },
        { data: "Pengguna", name: "user", orderable: false, searchable: false },
        {
            data: "Subjek",
            name: "subject",
            orderable: false,
            searchable: false,
        },
        {
            data: "Waktu",
            name: "created_at",
            orderable: false,
            searchable: false,
        },
    ];
    t = $("#activity-log-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: window.location.pathname,
            pages: 5,
            data: function (d) {
                let filterParams = getQueryParams();
                $.extend(d, filterParams);
            },
        }),
        columns: columns,
        language: {
            sProcessing: "Sedang memproses...",
            sZeroRecords: "Tidak ditemukan data yang sesuai",
            sInfo: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            sInfoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
            sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
            sEmptyTable: "Tidak ada data di tabel",
            sSearch: "Cari:",
        },
        fixedColumns: {
            leftColumns: 2,
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchDelay: 300,
        order: [],
    });
});

$(document).ready(function () {
    const filter_end_date = flatpickr("#end-date-filter", {
        dateFormat: "d/m/Y",
        locale: flatpickrLocationID,
    });
    const filter_start_date = flatpickr("#start-date-filter", {
        dateFormat: "d/m/Y",
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const selectedDate = selectedDates[0];
                filter_end_date.set("minDate", selectedDate);
            }
        },
        locale: flatpickrLocationID,
    });

    $("div.dt-search input").attr("placeholder", "nama aktifitas...");

    $("#filter-btn").click(function (e) {
        e.preventDefault();

        // Buat query string
        const params = new URLSearchParams();
        if ($("#user-filter").val())
            params.append("pengguna", $("#user-filter").val());
        if ($("#start-date-filter").val())
            params.append("rentang_waktu_dari", $("#start-date-filter").val());
        if ($("#end-date-filter").val())
            params.append("rentang_waktu_sampai", $("#end-date-filter").val());

        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);

        // Refresh datatable
        t.clearPipeline().draw();
    });
});
