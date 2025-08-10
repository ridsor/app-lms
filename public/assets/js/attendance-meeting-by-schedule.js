$(function () {
    // Halaman index: daftar jadwal (schedule)
    if ($("#attendance-schedule-table").length) {
        var t = $("#attendance-schedule-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: `/kehadiran/jadwal/${schedule_id}/pertemuan`,
                pages: 5,
                data: function (d) {},
            }),
            columns: [
                {
                    data: null,
                    name: "Pertemuan",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return `Pertemuan ke-${
                            meta.row + meta.settings._iDisplayStart + 1
                        }`;
                    },
                },
                { data: "Waktu", name: "date", searchable: false },
                {
                    data: "",
                    orderable: false,
                    searchable: false,
                },
            ].filter(Boolean),
            fixedColumns: {
                leftColumns: 2,
            },
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
    }
});

function handleDetailMeeting(id, schedule_time_id) {
    if (!id) return;

    const button = $(event.target);
    const originalHtml = button.html();
    button
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/kehadiran/pertemuan/${id}/${schedule_time_id}`,
        method: "GET",
        success: function (res) {
            $("#detailMeetingModal").modal("show");
            if (res.success && res.data) {
                $("#detailMeetingModal #change_attendance").attr(
                    "href",
                    `/kehadiran/${res.data.schedule.id}/pertemuan/${res.data.id}`
                );

                $("#detailMeetingModal #subject").html(
                    res.data.schedule.subject.name
                );
                $("#detailMeetingModal #class").html(
                    (res.data.schedule.class.major
                        ? res.data.schedule.class.major.name + " - "
                        : "") +
                        res.data.schedule.class.name +
                        " - " +
                        res.data.schedule.class.level
                );
                $("#detailMeetingModal #meeting").html(res.data.meeting_at);
                let meeting_method;
                switch (res.data.meeting_method) {
                    case "Online":
                        meeting_method = "Daring";
                        break;
                    case "Offline":
                        meeting_method = "Luring";
                        break;
                    default:
                        meeting_method = "Campuran";
                }
                $("#detailMeetingModal #meeting_method").html(meeting_method);
                $("#detailMeetingModal #teacher").html(
                    res.data.schedule.teacher.name
                );
                $("#detailMeetingModal #date").html(
                    getDayName(res.data.formatted_date)
                );
                $("#detailMeetingModal #start_time").html(
                    res.data.schedule_time.start_time
                );
                $("#detailMeetingModal #end_time").html(
                    res.data.schedule_time.end_time
                );

                $("#detailMeetingModal #status").html(res.data.status);

                // kehadiran pertemuan
                $("#detailMeetingModal #started_at").html(
                    res.data.started_at
                        ? `${res.data.formatted_date} ${res.data.formatted_started_at} WIT`
                        : "-"
                );
                $("#detailMeetingModal #total_attendance").html(
                    res.data.attendances_count
                );
                $("#detailMeetingModal #total_user").html(
                    res.data.schedule.class.students_count
                );
                $("#detailMeetingModal #teacher").html(
                    res.data.schedule.teacher.name
                );
                $("#detailMeetingModal #teacher-image").attr(
                    "src",
                    res.data.schedule.teacher.user.image
                        ? `/storage/${res.data.schedule.teacher.user.image}`
                        : "/assets/svg/user-placeholder.svg"
                );
            }
        },
        error: function (xhr) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(
                xhr.responseJSON?.message || "Gagal mengambil detail siswa"
            );
            toast.show();
        },
        complete: function () {
            button.prop("disabled", false).html(originalHtml);
        },
    });
}
