// attendance-crud.js
$(function () {
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

    // Definisikan columns berdasarkan role
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
        {
            data: "Mata Pelajaran",
            name: "subjects.name",
            searchable: true, // Ubah menjadi true agar bisa dicari
        },
    ];

    // Tambahkan kolom berdasarkan role
    if (role == "teacher") {
        columns.push({
            data: "Kelas",
            name: "class_name",
            searchable: false,
            orderable: false,
        });
    }

    if (role == "student" || role == "parent") {
        columns.push({
            data: "Guru",
            name: "teachers.name",
            searchable: false,
            orderable: false,
        });
    }

    // Tambahkan kolom tetap
    columns.push(
        {
            data: "Rekap",
            name: "attendance_percentage", // Ganti dengan nama yang sesuai
            orderable: false,
            searchable: false,
        },
        {
            data: "Aksi", // Beri nama yang jelas
            name: "action",
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                // Render action buttons jika needed
                return data; // Biarkan server yang menghandle
            },
        }
    );

    // Halaman index: daftar jadwal (schedule)
    if ($("#attendance-schedule-table").length) {
        var t = $("#attendance-schedule-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: "/kehadiran",
                pages: 5,
                data: function (d) {
                    let filterParams = getQueryParams();
                    $.extend(d, filterParams);

                    // Tambahkan role ke request agar server tahu
                    d.role = role;
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
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            autoWidth: false,
            searchable: true,
            searching: true,
            searchDelay: 300,
            order: [],
        });

        // Event handler untuk filter
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
    }
});

// Helper function untuk debug
function debugDataTable(table) {
    table.on("xhr", function (e, settings, json, xhr) {
        console.log("Server response:", json);
        if (json && json.data && json.data.length > 0) {
            console.log("First row data:", json.data[0]);
        }
    });
}
