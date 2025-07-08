// attendance-crud.js
$(function () {
    // Halaman index: daftar jadwal (schedule)
    if ($("#attendance-schedule-table").length) {
        var table = $("#attendance-schedule-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/kehadiran",
                data: function (d) {
                    d.major = $("#major-filter").val();
                    d.level = $("#level-filter").val();
                    d.class = $("#class-filter").val();
                },
            },
            columns: [
                typeof window.hasMajor !== "undefined" && window.hasMajor
                    ? {
                          data: "class.major.name",
                          name: "class.major.name",
                          defaultContent: "-",
                      }
                    : null,
                { data: "class.name", name: "class.name" },
                { data: "class.level", name: "class.level" },
                { data: "subject.name", name: "subject.name" },
                { data: "teacher.name", name: "teacher.name" },
                {
                    data: "Aksi",
                    orderable: false,
                    searchable: false,
                },
            ].filter(Boolean),
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
            searchDelay: 300,
            order: [],
        });

        $("#major-filter, #level-filter, #class-filter").on(
            "change",
            function () {
                table.ajax.reload();
            }
        );
        $("#filter-btn").on("click", function () {
            table.ajax.reload();
        });
    }

    // Halaman by-schedule: rekap kehadiran per meeting (grouped by subject+teacher)
    if ($("#attendance-meeting-table").length) {
        // Ambil parameter dari URL jika ada
        const params = new URLSearchParams(window.location.search);
        if (params.get("meeting")) {
            $("#meeting-filter").val(params.get("meeting"));
        }
        if (params.get("date")) {
            $("#date-filter").val(params.get("date"));
        }

        var t = $("#attendance-meeting-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: window.location.pathname,
                data: function (d) {
                    if ($("#meeting-filter").val())
                        d.meeting = $("#meeting-filter").val();
                    if ($("#date-filter").val())
                        d.date = $("#date-filter").val();
                },
            },
            columns: [
                { data: "subject_name", name: "subject_name" },
                { data: "teacher_name", name: "teacher_name" },
                {
                    data: "meetings_table",
                    name: "meetings_table",
                    orderable: false,
                    searchable: false,
                },
            ],
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
            searchable: false,
            searching: false,
            searchDelay: 300,
            order: [],
            initComplete: function (settings, json) {
                // Isi select meeting dari data datatable (ambil dari meetings di setiap group)
                let uniqueMeetings = [];
                if (json && json.data) {
                    json.data.forEach(function (group) {
                        if (group.meetings) {
                            group.meetings.forEach(function (m) {
                                if (!uniqueMeetings.includes(m.meeting_name))
                                    uniqueMeetings.push(m.meeting_name);
                            });
                        }
                    });
                    let $meetingFilter = $("#meeting-filter");
                    $meetingFilter
                        .empty()
                        .append('<option value="">Pilih Pertemuan</option>');
                    uniqueMeetings.forEach(function (m) {
                        $meetingFilter.append(
                            '<option value="' + m + '">' + m + "</option>"
                        );
                    });
                    // Set value jika ada di URL
                    if (params.get("meeting"))
                        $meetingFilter.val(params.get("meeting"));
                }
            },
        });

        $("#filter-btn").click(function (e) {
            e.preventDefault();
            // Buat query string
            const params = new URLSearchParams();
            if ($("#meeting-filter").val())
                params.append("meeting", $("#meeting-filter").val());
            if ($("#date-filter").val())
                params.append("date", $("#date-filter").val());
            // Update URL tanpa reload
            const newUrl =
                window.location.pathname +
                (params.toString() ? "?" + params.toString() : "");
            window.history.replaceState({}, "", newUrl);
            // Refresh datatable
            t.ajax.reload();
        });
    }

    // Halaman lain (by-class, by-schedule) bisa ditambahkan sesuai kebutuhan
});
