$(document).ready(function () {
    // Form submission
    $("#periodForm").on("submit", function (e) {
        e.preventDefault();

        const periodId = $("#periodId").val();
        const semester = $("#semester").val();
        const academicYear = $("#academicYear").val().trim();
        const startDate = $("#startDate").val();
        const endDate = $("#endDate").val();

        // Clear previous errors
        $(
            "#semesterError, #academicYearError, #startDateError, #endDateError"
        ).text("");
        $("#semester, #academicYear, #startDate, #endDate").removeClass(
            "is-invalid is-valid"
        );

        // Basic validation
        let hasError = false;

        if (!semester) {
            $("#semester").addClass("is-invalid");
            $("#semesterError").text("Semester harus dipilih.");
            hasError = true;
        }

        if (!academicYear) {
            $("#academicYear").addClass("is-invalid");
            $("#academicYearError").text("Tahun akademik harus diisi.");
            hasError = true;
        }

        if (!startDate) {
            $("#startDate").addClass("is-invalid");
            $("#startDateError").text("Tanggal mulai harus diisi.");
            hasError = true;
        }

        if (!endDate) {
            $("#endDate").addClass("is-invalid");
            $("#endDateError").text("Tanggal selesai harus diisi.");
            hasError = true;
        }

        if (startDate && endDate && startDate >= endDate) {
            $("#endDate").addClass("is-invalid");
            $("#endDateError").text(
                "Tanggal selesai harus setelah tanggal mulai."
            );
            hasError = true;
        }

        if (hasError) {
            return;
        }

        // Set method for form submission
        const method = periodId ? "PUT" : "POST";
        $("#methodField").val(method);

        // Show loading state
        const submitBtn = $("#submitBtn");
        const originalText = submitBtn.text();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );

        // Use form data for submission
        const formData = new FormData(this);

        $.ajax({
            url: "/periode" + (periodId ? `/${periodId}` : ""),
            method: "POST", // Always use POST, method spoofing will handle PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    resetForm();
                    loadPeriods();
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.semester) {
                        $("#semester").addClass("is-invalid");
                        $("#semesterError").text(errors.semester[0]);
                    }
                    if (errors.academic_year) {
                        $("#academicYear").addClass("is-invalid");
                        $("#academicYearError").text(errors.academic_year[0]);
                    }
                    if (errors.start_date) {
                        $("#startDate").addClass("is-invalid");
                        $("#startDateError").text(errors.start_date[0]);
                    }
                    if (errors.end_date) {
                        $("#endDate").addClass("is-invalid");
                        $("#endDateError").text(errors.end_date[0]);
                    }
                    console.log(errors);
                } else {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                }
                submitBtn.html(originalText);
            },
            complete: function () {
                submitBtn.prop("disabled", false);
            },
        });
    });

    // Edit period
    $(document).on("click", ".edit-data", function (e) {
        e.preventDefault();
        const periodId = $(this).data("id");

        // Show loading state
        const editBtn = $(this);
        const originalHtml = editBtn.html();
        editBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
            );

        $.ajax({
            url: `/periode/${periodId}`,
            method: "GET",
            success: function (response) {
                if (response.success) {
                    $("#periodId").val(response.data.id);
                    $("#semester").val(response.data.semester);
                    $("#academicYear").val(response.data.academic_year);
                    $("#startDate").val(response.data.start_date);
                    $("#endDate").val(response.data.end_date);
                    $("#formTitle").text("Edit Periode");
                    $("#submitBtn").text("Edit");
                    $("#cancelBtn").show();
                    $("#methodField").val("PUT");
                    $("#semester").focus();

                    // Set active state on list
                    $(".dd-item").removeClass("active");
                    $(`.dd-item[data-id="${response.data.id}"]`).addClass(
                        "active"
                    );

                    // Scroll to form
                    $("html, body").animate(
                        {
                            scrollTop: $("#periodForm").offset().top - 100,
                        },
                        500
                    );
                }
            },
            error: function (xhr, status, error) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            },
            complete: function () {
                editBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    // Active period
    $(document).on("click", ".active-data", function (e) {
        e.preventDefault();
        const periodId = $(this).data("id");
        const periodName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menjadikan periode ini aktif?",
            text: `Periode: ${periodName}`,
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Batal`,
            confirmButtonText: "Aktifkan",
            confirmButtonColor: "#16C7F9",
            cancelButtonColor: "#FC4438",
            confirmButtonText: "Aktifkan",
            imageUrl: "../assets/images/gif/dashboard-8/successful.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                const activeBtn = $(this)
                    .closest(".dd-item")
                    .find(".period-status");
                activeBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );

                $.ajax({
                    url: `/periode/active/${periodId}`,
                    method: "POST",
                    success: async function (response) {
                        if (response.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(
                                response.message
                            );
                            toast.show();
                            await loadPeriods();
                        }
                    },
                    error: function (xhr, status, error) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        activeBtn.prop("disabled", false);
                    },
                });
            } else if (result.isDenied) {
            }
        });
    });

    // Delete period
    $(document).on("click", ".delete-data", function (e) {
        e.preventDefault();
        const periodId = $(this).data("id");
        const periodName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menghapusnya?",
            text: `Periode: ${periodName}`,
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Batal`,
            confirmButtonText: "Hapus",
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#16C7F9",
            confirmButtonText: "Hapus",
            imageUrl: "../assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const deleteBtn = $(this);
                const originalHtml = deleteBtn.html();
                deleteBtn
                    .prop("disabled", true)
                    .html(
                        '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
                    );

                // Create form data for DELETE
                const formData = new FormData();
                formData.append(
                    "_token",
                    $('meta[name="csrf-token"]').attr("content")
                );
                formData.append("_method", "DELETE");

                $.ajax({
                    url: `/periode/${periodId}`,
                    method: "POST", // Use POST with method spoofing
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: async function (response) {
                        if (response.success) {
                            const toast = new bootstrap.Toast(
                                $("#toast-success")
                            );
                            $("#toast-success #toast-text").text(
                                response.message
                            );
                            toast.show();

                            await loadPeriods();

                            // If deleted period was being edited, reset form
                            if ($("#periodId").val() == periodId) {
                                resetForm();
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        const toast = new bootstrap.Toast($("#toast-error"));
                        $("#toast-error #toast-text").text(
                            xhr.responseJSON.message
                        );
                        toast.show();
                    },
                    complete: function () {
                        deleteBtn.prop("disabled", false).html(originalHtml);
                    },
                });
            } else if (result.isDenied) {
            }
        });
    });

    // Cancel edit
    $("#cancelBtn").on("click", function () {
        resetForm();
    });

    // Real-time validation
    $("#semester").on("change", function () {
        const value = $(this).val();
        $(this).removeClass("is-invalid is-valid");
        $("#semesterError").text("");

        if (value.length > 0) {
            $(this).addClass("is-valid");
        }
    });

    $("#academicYear").on("input", function () {
        const value = $(this).val().trim();
        $(this).removeClass("is-invalid is-valid");
        $("#academicYearError").text("");

        if (value.length > 0) {
            $(this).addClass("is-valid");
        }
    });

    $("#startDate").on("change", function () {
        const value = $(this).val();
        $(this).removeClass("is-invalid is-valid");
        $("#startDateError").text("");

        if (value.length > 0) {
            $(this).addClass("is-valid");
        }

        // Check if end date is before start date
        const endDate = $("#endDate").val();
        if (endDate && value >= endDate) {
            $("#endDate").addClass("is-invalid");
            $("#endDateError").text(
                "Tanggal selesai harus setelah tanggal mulai."
            );
        } else {
            $("#endDate").removeClass("is-invalid");
            $("#endDateError").text("");
        }
    });

    $("#endDate").on("change", function () {
        const value = $(this).val();
        const startDate = $("#startDate").val();
        $(this).removeClass("is-invalid is-valid");
        $("#endDateError").text("");

        if (value.length > 0) {
            if (startDate && value <= startDate) {
                $(this).addClass("is-invalid");
                $("#endDateError").text(
                    "Tanggal selesai harus setelah tanggal mulai."
                );
            } else {
                $(this).addClass("is-valid");
            }
        }
    });

    // Reset form
    function resetForm() {
        $("#periodForm")[0].reset();
        $("#periodId").val("");
        $("#formTitle").text("Tambah Periode");
        $("#submitBtn").text("Simpan");
        $("#cancelBtn").hide();
        $("#methodField").val("POST");
        $(
            "#semesterError, #academicYearError, #startDateError, #endDateError"
        ).text("");
        $("#semester, #academicYear, #startDate, #endDate").removeClass(
            "is-invalid is-valid"
        );

        // Remove active state from list
        $(".dd-item").removeClass("active");
    }

    // Filter periods
    $("#filterBtn").on("click", async function () {
        originalText = $(this).text();
        const filterBtn = $(this);
        filterBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        await loadPeriods();
        filterBtn.prop("disabled", false).html(originalText);
    });

    // Load periods list
    async function loadPeriods() {
        let startDate = $("#filterStartDate").val();
        let endDate = $("#filterEndDate").val();
        let semester = $("#filterSemester").val();
        let sort = $("#sort").val();

        let params = new URLSearchParams(window.location.search);
        params.set("start_date", startDate);
        params.set("end_date", endDate);
        params.set("semester", semester);
        params.set("sort", sort);

        await $.ajax({
            url: `/periode?${params.toString()}`,
            method: "GET",
            success: function (response) {
                // Extract periods list from response
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, "text/html");
                const periodsList = doc.querySelector("#periodsList");
                if (periodsList) {
                    $("#periodsList").html(periodsList.innerHTML);

                    // Reinitialize feather icons
                    if (typeof feather !== "undefined") {
                        feather.replace();
                    }
                }
            },
            error: function (xhr, status, error) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            },
        });
    }
});
