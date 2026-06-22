var addUkkInstructionQuill = new Quill("#addUkkInstructionQuill", {
    theme: "snow",
    modules: { toolbar: "#addUkkToolbar" },
    placeholder: "Tulis instruksi",
});
var editUkkInstructionQuill = new Quill("#editUkkInstructionQuill", {
    theme: "snow",
    modules: { toolbar: "#editUkkToolbar" },
    placeholder: "Tulis instruksi",
});

const formatTime = (date) => {
    const hours = date.getHours().toString().padStart(2, "0");
    const minutes = date.getMinutes().toString().padStart(2, "0");
    return `${hours}:${minutes}`;
};

const add_start_time = flatpickr("#addUkkStartTime", {
    defaultDate: new Date(),
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
    onChange: function (selectedDates) {
        if (selectedDates.length > 0) {
            const selectedDate = selectedDates[0];
            add_end_time.set({
                minDate: selectedDate,
                minTime: formatTime(selectedDate),
            });
            add_end_time.setDate(selectedDate);
        }
    },
});

const add_end_time = flatpickr("#addUkkEndTime", {
    defaultDate: new Date(),
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
});

const edit_start_time = flatpickr("#editUkkStartTime", {
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
    onChange: function (selectedDates) {
        if (selectedDates.length > 0) {
            const selectedDate = selectedDates[0];
            edit_end_time.set({
                minDate: selectedDate,
                minTime: formatTime(selectedDate),
            });
            edit_end_time.setDate(selectedDate);
        }
    },
});

const edit_end_time = flatpickr("#editUkkEndTime", {
    enableTime: true,
    static: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    locale: flatpickrLocationID,
});

$(document).ready(function () {
    $("[name='type']").on("change", function () {
        const type = $(this).val();
        const rubricSection = $(this).closest("form").find(".rubric-section");
        const extraFields = $(this).closest("form").find(".extra-fields-praktik");
        const shuffleSection = $(this).closest("form").find(".shuffle-questions-section");
        
        if (type === 'Praktik') {
            rubricSection.show();
            extraFields.show();
            shuffleSection.hide();
        } else if (type === 'Teori') {
            rubricSection.hide();
            extraFields.hide();
            shuffleSection.show();
        } else {
            rubricSection.hide();
            extraFields.hide();
            shuffleSection.hide();
        }
    }).trigger('change');

    $(".add-rubric-row").on("click", function () {
        const tbody = $(this).closest(".rubric-section").find(".rubric-table tbody");
        const row = `
            <tr>
                <td>
                    <select class="form-select" name="rubric[category][]">
                        <option value="Utama">Utama</option>
                        <option value="Pendukung">Pendukung</option>
                    </select>
                </td>
                <td>
                    <textarea class="form-control" name="rubric[element][]" rows="2" placeholder="Tulis elemen kompetensi"></textarea>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger remove-rubric-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    $(document).on("click", ".remove-rubric-row", function () {
        $(this).closest("tr").remove();
    });

    $("#addUkkForm").on("submit", function (e) {
        console.log("Submitting add UKK form");
        e.preventDefault();

        $("#addUkkForm").find("input, select").removeClass("is-invalid");
        $("#addUkkForm").find(".invalid-feedback").text("");

        // Sync Quill to input
        var value = addUkkInstructionQuill.root.innerHTML;
        var descriptionText = addUkkInstructionQuill.getText().trim();
        if (descriptionText === "") {
            value = "";
        }
        $("#addUkkForm [name='instructions']").val(value);

        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);

        $.ajax({
            url: `/ukk`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (
                            $("#addUkkForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addUkkForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#addUkkForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#addUkkForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#addUkkForm [name='" + key + "']").hasClass(
                                "radio"
                            )
                        ) {
                            $("#addUkkForm [name='" + key + "']")
                                .parent()
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addUkkForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }
                    }
                } else {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                }
                submitBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });

    $("#editUkkForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editUkkForm").find("input, select").removeClass("is-invalid");
        $("#editUkkForm").find(".invalid-feedback").text("");

        // Sync Quill to input
        var value = editUkkInstructionQuill.root.innerHTML;
        var descriptionText = editUkkInstructionQuill.getText().trim();
        if (descriptionText === "") {
            value = "";
        }
        $("#editUkkForm [name='instructions']").val(value);

        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);

        $.ajax({
            url: `/ukk/${id}`,
            method: "POST",
            headers: { "X-HTTP-Method-Override": "PUT" },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        if (
                            $("#editUkkForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#editUkkForm [name='" + key + "']").hasClass(
                                "quill"
                            ) ||
                            $("#editUkkForm [name='" + key + "']").hasClass(
                                "selectpicker"
                            )
                        ) {
                            $("#editUkkForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#editUkkForm [name='" + key + "']").hasClass(
                                "radio"
                            )
                        ) {
                            $("#editUkkForm [name='" + key + "']")
                                .parent()
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#editUkkForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }
                    }
                } else {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                }
                submitBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });
    $("#addUkkFile").on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            $("#add-file-preview").html(`
                <div class="d-flex align-items-center gap-2 border p-2 rounded">
                    <i class="fa fa-file text-primary"></i>
                    <span class="text-truncate flex-grow-1" style="max-width: 250px;">${file.name}</span>
                    <button type="button" class="btn btn-sm text-danger remove-add-file-ukk p-0">
                        <i class="fa fa-times-circle fs-5"></i>
                    </button>
                </div>
            `);
        }
    });

    $(document).on("click", ".remove-add-file-ukk", function () {
        $("#addUkkFile").val("");
        $("#add-file-preview").html("");
    });

    $("#editUkkFile").on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            $("#editUkkDeletedFile").val(0);
            $("#edit-file-preview").html(`
                <div class="d-flex align-items-center gap-2 border p-2 rounded">
                    <i class="fa fa-file text-primary"></i>
                    <span class="text-truncate flex-grow-1" style="max-width: 250px;">${file.name}</span>
                    <button type="button" class="btn btn-sm text-danger delete-file-ukk p-0">
                        <i class="fa fa-times-circle fs-5"></i>
                    </button>
                </div>
            `);
        }
    });
});

function handleEditUkk(e, id) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/ukk/${id}/edit`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                $("#editUkkForm").data("id", id);

                $("#editUkkForm [name='title']").val(res.data.title);
                $("#editUkkForm [name='operator_id']").val(res.data.operator_id);
                $("#editUkkForm [name='major']").val(res.data.major);
                $("#editUkkForm [name='code']").val(res.data.code);
                $("#editUkkForm [name='package_number']").val(res.data.package_number);
                $("#editUkkForm [name='exam_format']").val(res.data.exam_format);
                editUkkInstructionQuill.setContents(
                    editUkkInstructionQuill.clipboard.convert(
                        res.data.instructions
                    )
                );
                $("#editUkkForm [name='instructions']").val(res.data.instructions);
                $("#editUkkForm [name='type']").val(res.data.type).trigger('change');
                edit_start_time.setDate(new Date(res.data.start_time));
                edit_end_time.setDate(new Date(res.data.end_time));
                edit_end_time.set("minTime", new Date(res.data.start_time));
                edit_end_time.set("minDate", new Date(res.data.start_time));

                // Load Rubric
                const rubricTbody = $("#editUkkForm .rubric-table tbody");
                rubricTbody.empty();
                if (res.data.type === 'Praktik' && res.data.rubric) {
                    const rubric = res.data.rubric;
                    // Check if rubric is in old format (just elements) or new format (category and element)
                    if (Array.isArray(rubric.category)) {
                        for (let i = 0; i < rubric.category.length; i++) {
                            const row = `
                                <tr>
                                    <td>
                                        <select class="form-select" name="rubric[category][]">
                                            <option value="Utama" ${rubric.category[i] === 'Utama' ? 'selected' : ''}>Utama</option>
                                            <option value="Pendukung" ${rubric.category[i] === 'Pendukung' ? 'selected' : ''}>Pendukung</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="rubric[element][]" rows="2">${rubric.element[i]}</textarea>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-danger remove-rubric-row">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            rubricTbody.append(row);
                        }
                    }
                }

                if (res.data.is_shuffle_questions) {
                    $(`#editUkkForm [name='is_shuffle_questions'][value='1']`).prop(
                        "checked",
                        true
                    );
                } else {
                    $(`#editUkkForm [name='is_shuffle_questions'][value='0']`).prop(
                        "checked",
                        true
                    );
                }

                $("#editUkkDeletedFile").val(0);
                $("#editUkkFile").val("");
                if (res.data.file_path) {
                    $("#edit-file-preview").html(`
                        <div class="d-flex align-items-center gap-2 border p-2 rounded">
                            <i class="fa fa-file text-primary"></i>
                            <span class="text-truncate flex-grow-1" style="max-width: 250px;">${res.data.file_name}</span>
                            <button type="button" class="btn btn-sm text-danger delete-file-ukk p-0">
                                <i class="fa fa-times-circle fs-5"></i>
                            </button>
                        </div>
                    `);
                } else {
                    $("#edit-file-preview").html("");
                }

                $("#editUkkModal").modal("show");
                $("#editUkkModal .selectpicker").selectpicker("refresh");
            }
        },
        error: function (xhr) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(xhr.responseJSON.message);
            toast.show();
        },
        complete: function () {
            editBtn.prop("disabled", false).html(originalHtml);
        },
    });
}

$(document).on("click", ".delete-file-ukk", function () {
    $("#editUkkDeletedFile").val(1);
    $("#editUkkFile").val("");
    $("#edit-file-preview").html("");
});

function handleDeleteUkk(e, id) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const redirectWhenSuccess = deleteBtn.data("redirect");
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Ujian",
        text: "Apakah Anda yakin ingin menghapus ujian ini?",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: "Hapus",
        denyButtonText: `Batal`,
        confirmButtonColor: "#FC4438",
        imageUrl: "/assets/images/gif/trash.gif",
        imageWidth: 120,
        imageHeight: 120,
    }).then((result) => {
        if (result.isConfirmed) {
            deleteBtn
                .prop("disabled", true)
                .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

            $.ajax({
                url: `/ukk/${id}`,
                method: "DELETE",
                success: function (response) {
                    if (response.success) {
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(response.message);
                        toast.show();
                        if (redirectWhenSuccess) {
                            window.location.href = redirectWhenSuccess;
                        } else {
                            location.reload();
                        }
                    }
                },
                error: function (xhr) {
                    const toast = new bootstrap.Toast($("#toast-error"));
                    $("#toast-error #toast-text").text(
                        xhr.responseJSON.message
                    );
                    toast.show();
                    deleteBtn.prop("disabled", false).html(originalHtml);
                },
            });
        }
    });
}
