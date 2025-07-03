$(function () {
    let columns = [];
    if (hasMajor) {
        columns.push({ data: "Jurusan", name: "majors.name" });
    }
    columns = columns.concat([
        { data: "Kelas", name: "classes.name" },
        { data: "Tingkat", name: "classes.level" },
        { data: "Aksi", name: "Aksi", orderable: false, searchable: false },
    ]);

    // filter
    const params = getQueryParams();
    if (params.major) {
        $("#major-filter").val(params.major);
    }
    if (params.class) {
        $("#class-filter").val(params.class);
    }
    if (params.level) {
        $("#level-filter").val(params.level);
    }

    var t = $("#schedule-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: $.fn.dataTable.pipeline({
            url: "/jadwal",
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
            sInfoPostFix: "",
            sSearch: "Cari:",
            sUrl: "",
            select: {
                rows: {
                    _: "%d baris terpilih",
                    0: "",
                },
            },
        },
        fixedColumns: {
            leftColumns: 2,
        },
        scrollCollapse: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        responsive: true,
        autoWidth: false,
        searchable: false,
        searching: false,
        order: [],
    });

    $("#filter-btn").click(function (e) {
        e.preventDefault();

        // Buat query string
        const params = new URLSearchParams();
        if ($("#major-filter").val())
            params.append("major", $("#major-filter").val());
        if ($("#class-filter").val())
            params.append("class", $("#class-filter").val());
        if ($("#level-filter").val())
            params.append("level", $("#level-filter").val());

        // Update URL tanpa reload
        const newUrl =
            window.location.pathname +
            (params.toString() ? "?" + params.toString() : "");
        window.history.replaceState({}, "", newUrl);

        // Refresh datatable
        t.clearPipeline().draw();
    });
});
