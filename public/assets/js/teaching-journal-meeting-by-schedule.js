const subSubjectMatterQuill = new Quill("#subSubjectMatterQuill", {
    theme: "snow",
    modules: { toolbar: "#toolbarsubSubjectMatter" },
    placeholder: "Masukan sub pokok pembahasan",
});
subSubjectMatterQuill.disable();

const additionalNoteQuill = new Quill("#additionalNoteQuill", {
    theme: "snow",
    modules: { toolbar: "#toolbaradditionalNote" },
    placeholder: "Masukan catatan tambahan",
});
additionalNoteQuill.disable();

$(function () {
    // Halaman index: daftar jadwal (schedule)
    if ($("#teaching-journal-schedule-table").length) {
        var t = $("#teaching-journal-schedule-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: $.fn.dataTable.pipeline({
                url: window.location.pathname,
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

function handleDetailMeeting(id) {
    if (!id) return;

    const button = $(event.target);
    const originalHtml = button.html();
    button
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/jurnal-mengajar/pertemuan/${id}`,
        method: "GET",
        success: function (res) {
            $("#journalModal").modal("show");
            if (res.success && res.data) {
                $("#journalModal #subjectMatter").val(
                    res.data.teaching_journal.subject_matter
                );
                subSubjectMatterQuill.setContents(
                    subSubjectMatterQuill.clipboard.convert(
                        res.data.teaching_journal.sub_subject_matter
                    )
                );
                additionalNoteQuill.setContents(
                    additionalNoteQuill.clipboard.convert(
                        res.data.teaching_journal.additional_note
                    )
                );
                $("#journalModal #start_time").html(
                    res.data.schedule_time.start_time
                );
                $("#journalModal #end_time").html(
                    res.data.schedule_time.end_time
                );
                console.log(res.data.schedule_time);
                $("#journalModal #date").html(
                    res.data.formatted_date
                        ? `${res.data.formatted_date} WIT`
                        : "-"
                );
                $("#journalModal #started_at").html(
                    res.data.formatted_started_at
                        ? `${res.data.formatted_started_at} WIT`
                        : "-"
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
