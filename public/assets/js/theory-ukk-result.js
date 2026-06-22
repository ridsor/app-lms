let t;

$(function () {
  t = $("#ukk-result-teori-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: $.fn.dataTable.pipeline({
      url: window.location.pathname,
      pages: 5,
    }),
    columns: [
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
        data: "Nama",
        name: "students.name",
        searchable: false,
      },
      {
        data: "NISN",
        name: "students.nisn",
        searchable: false,
      },
      {
        data: "Status",
        name: "status",
        searchable: false
      },
      {
        data: "Pengerjaan",
        name: "end_time",
        searchable: false
      },
      {
        data: null,
        name: "",
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
          const html = `
                    <div class="common-align gap-2 justify-content-start" style="cursor: pointer;">
                        <a class="reset-result btn btn-danger btn-sm p-1 px-2 rounded-2" data-id="${data.id}" data-ukk-id="${data.ukk_id}">
                                <i class="fa-solid fa-rotate-right"></i>
                        </a>
                        <a class="square-white view rounded-2" href=${"/ukk/" +
            data.ukk_id +
            "/teori/evaluasi/" +
            (meta.row + meta.settings._iDisplayStart + 1)
            }>
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>
                    `;
          return html;
        },
        className: "text-center",
        width: "40px",
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
    scrollCollapse: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    responsive: true,
    autoWidth: false,
    searchDelay: 300,
    order: [],
  });

  $("#globalSearch").on("keyup", function () {
    t.search(this.value).clearPipeline().draw();
  });

  $("#export-excel").on("submit", function (e) {
    e.preventDefault();

    const btnSubmit = $(this).find("button[type='submit']");
    const originalHtml = btnSubmit.html();

    btnSubmit
      .prop("disabled", true)
      .html(
        '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Loading...'
      );

    this.submit();

    setTimeout(function () {
      btnSubmit.prop("disabled", false).html(originalHtml);
    }, 3000);
  });
});

$(document).ready(function () {
  $("#reset-all").on("click", function () {
    let ukkId = $(this).data("id");
    const originalHtml = $(this).html();
    const btnSubmit = $(this);

    Swal.fire({
      title: "Reset Semua Hasil Teori UKK",
      text: "Apakah Anda yakin ingin mereset semua hasil teori UKK?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Reset Semua!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        btnSubmit
          .prop("disabled", true)
          .html(
            '<i class="fa-solid fa-arrows-rotate fa-spin"></i> Loading...'
          );
        $.ajax({
          url: `/ukk/${ukkId}/hasil/teori/reset`,
          method: "PATCH",
          success: function (response) {
            showToast("success", response.message);
            t.clearPipeline().draw();
          },
          error: function (xhr) {
            showToast(
              "error",
              xhr.responseJSON?.message || "Terjadi kesalahan."
            );
          },
          complete: function () {
            btnSubmit.prop("disabled", false).html(originalHtml);
          },
        });
      }
    });
  });

  $("#ukk-result-teori-table").on("click", ".reset-result", function () {
    let id = $(this).data("id");
    let ukk_id = $(this).data("ukk-id");
    const originalHtml = $(this).html();
    const btnSubmit = $(this);

    Swal.fire({
      title: "Reset Hasil Teori UKK",
      text: "Apakah Anda yakin ingin mereset hasil teori UKK?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, Reset!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        btnSubmit
          .prop("disabled", true)
          .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');
        $.ajax({
          url: `/ukk/${ukk_id}/hasil/teori/${id}/reset`,
          method: "PATCH",
          success: function (response) {
            showToast("success", response.message);
            t.clearPipeline().draw();
          },
          error: function (xhr) {
            showToast(
              "error",
              xhr.responseJSON?.message || "Terjadi kesalahan."
            );
          },
          complete: function () {
            btnSubmit.prop("disabled", false).html(originalHtml);
          },
        });
      }
    });
  });
});