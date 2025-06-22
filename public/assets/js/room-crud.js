$(document).ready(function () {
    let searchTimer;

    function loadRooms() {
        let search = $("#search").val();
        let sort = $("#sort").val();

        let params = new URLSearchParams();
        params.append("search", search);
        params.append("sort", sort);

        // Menambahkan loading indicator
        // $("#roomsList").html(
        //     '<div class="d-flex justify-content-center p-5"><span class="spinner-border text-primary" role="status"></span></div>'
        // );

        $.ajax({
            url: `/ruangan?${params.toString()}`,
            method: "GET",
            success: function (response) {
                const newContent = $(response).find("#roomsList").html();
                $("#roomsList").html(newContent);

                // Inisialisasi ulang ikon feather jika ada
                if (typeof feather !== "undefined") {
                    feather.replace();
                }
            },
            error: function (xhr) {
                console.error("Error loading rooms:", xhr);
                $("#roomsList").html(
                    '<p class="text-center text-danger">Gagal memuat data.</p>'
                );
            },
        });
    }

    $("#search, #sort").on("keyup change", function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadRooms, 500); // 500ms delay
    });

    // Form submission
    $("#roomForm").on("submit", function (e) {
        e.preventDefault();

        const roomId = $("#roomId").val();
        const roomName = $("#roomName").val().trim();

        // Clear previous errors
        $("#nameError").text("");
        $("#roomName").removeClass("is-invalid is-valid");

        // Basic validation
        if (!roomName) {
            $("#roomName").addClass("is-invalid");
            $("#nameError").text("Nama ruangan harus diisi.");
            return;
        }

        // Set method for form submission
        const method = roomId ? "PUT" : "POST";
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
            url: "/ruangan" + (roomId ? `/${roomId}` : ""),
            method: "POST", // Always use POST, method spoofing will handle PUT
            data: formData,
            processData: false,
            contentType: false,
            success: async function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    await loadRooms();
                    resetForm();
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $("#roomName").addClass("is-invalid");
                        $("#nameError").text(errors.name[0]);
                    }
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

    // Edit room
    $(document).on("click", ".edit-data", function (e) {
        e.preventDefault();
        const roomId = $(this).data("id");

        // Show loading state
        const editBtn = $(this);
        const originalHtml = editBtn.html();
        editBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span>'
            );

        $.ajax({
            url: `/ruangan/${roomId}`,
            method: "GET",
            success: function (response) {
                $("#roomId").val(response.data.id);
                $("#roomName").val(response.data.name);
                $("#formTitle").text("Edit Ruangan");
                $("#submitBtn").text("Edit");
                $("#cancelBtn").show();
                $("#methodField").val("PUT");
                $("#roomName").focus();

                // Set active state on list
                $(".dd-item").removeClass("active");
                $(`.dd-item[data-id="${response.data.id}"]`).addClass("active");

                // Scroll to form
                $("html, body").animate(
                    {
                        scrollTop: $("#roomForm").offset().top - 100,
                    },
                    500
                );
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

    // Delete room
    $(document).on("click", ".delete-data", function (e) {
        e.preventDefault();
        const roomId = $(this).data("id");
        const roomName = $(this).data("name");

        Swal.fire({
            title: "Apakah anda yakin menghapusnya?",
            text: `Ruangan: ${roomName}`,
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "Hapus",
            denyButtonText: `Batal`,
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#16C7F9",
            imageUrl: "../assets/images/gif/trash.gif",
            imageWidth: 120,
            imageHeight: 120,
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const deleteBtn = $(this);
                const originalHtml = deleteBtn.html();
                deleteBtn.prop("disabled", true).html(
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
                    url: `/ruangan/${roomId}`,
                    method: "POST", // Use POST with method spoofing
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: async function (response) {
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(response.message);
                        toast.show();

                        await loadRooms();

                        // If deleted room was being edited, reset form
                        if ($("#roomId").val() == roomId) {
                            resetForm();
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
    $("#roomName").on("input", function () {
        const value = $(this).val().trim();
        $(this).removeClass("is-invalid is-valid");
        $("#nameError").text("");

        if (value.length > 0) {
            $(this).addClass("is-valid");
        }
    });

    // Reset form
    function resetForm() {
        $("#roomForm")[0].reset();
        $("#roomId").val("");
        $("#formTitle").text("Tambah Ruangan");
        $("#submitBtn").text("Simpan");
        $("#cancelBtn").hide();
        $("#methodField").val("POST");
        $("#nameError").text("");
        $("#roomName").removeClass("is-invalid is-valid");

        // Remove active state from list
        $(".dd-item").removeClass("active");
    }
});
