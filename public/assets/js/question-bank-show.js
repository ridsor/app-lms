const toolbarOptions = [
    ["bold", "italic", "underline", "strike"], // toggled buttons
    ["blockquote"],
    ["link"],
    [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
    [{ header: [1, 2, 3, 4, 5, 6, false] }],

    [{ color: [] }, { background: [] }],
    [{ align: [] }],

    ["clean"], // remove formatting button
];

var addQuestionTextQuill = new Quill("#addQuestionTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis soal",
});
var editQuestionTextQuill = new Quill("#editQuestionTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis soal",
});
var addEssayQuestionTextQuill = new Quill("#addEssayQuestionTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis soal",
});
var editEssayQuestionTextQuill = new Quill("#editEssayQuestionTextQuill", {
    theme: "snow",
    modules: { toolbar: toolbarOptions },
    placeholder: "Tulis soal",
});

let alphabet = ["a", "b", "c", "d", "e"];

function elementOption(letter, value = "") {
    let newOption = `
            <div class="answer-option d-flex align-items-center checkbox-checked">
                <input type="radio" name="correct_answer" value="${letter}" class="me-2 form-check-input">
                <span class="fw-bold text-uppercase">${letter}.</span>
                <input type="text" class="form-control"
                    name="option_${letter}"
                    value="${value}"
                    placeholder="Tulis pilihan jawaban">
                <label class="p-2 h-100 mb-0 me-2" style="aspect-ratio: 1/1">
                    <div class="file-icon">
                        <i class="fa fa-image"></i>
                    </div>
                    <input type="file" hidden name="option_${letter}_image"
                        class="form-control form-control-sm option-image" accept="image/*">
                    <img class="img-preview"
                        style="height: 40px; width:40px; object-fit:cover; object-position: center; display: none;">
                </label>
                <span class="remove-option">&times;</span>
            </div>`;

    return newOption;
}

$(document).ready(function () {
    // Tambah opsi
    $("#addQuestionForm .addOption, #editQuestionForm .addOption").click(
        function (e) {
            e.preventDefault();
            let $container = $(this)
                .closest("#optionsForm")
                .find("#optionsContainer");

            let count = $container.find(".answer-option").length;

            if (count < alphabet.length) {
                let letter = alphabet[count];
                const newOption = elementOption(letter);
                $container.append(newOption);
            }
        }
    );

    $(document).on("change", ".option-image", function () {
        let $option = $(this).closest(".answer-option");
        let $preview = $option.find(".img-preview");
        let $fileIcon = $option.find(".file-icon");
        let file = this.files[0];

        if (file) {
            let reader = new FileReader();
            reader.onload = (e) => {
                $preview.attr("src", e.target.result).show();
                $fileIcon.hide();
            };
            reader.readAsDataURL(file);
        } else {
            $preview.hide().attr("src", "");
            $fileIcon.show();
        }
    });

    $(document).on("click", ".remove-option", function () {
        let form = $(this).closest("form");
        // let deleteData = form.find("input[name='deleteData[]']");
        let letter = $(this).siblings("span.fw-bold").text().replace(".", "");
        if (letter === "a" || letter === "b" || letter === "c") {
            return;
        }

        let $option = $(this).closest(".answer-option");
        let $container = $(this)
            .closest("#optionsForm")
            .find("#optionsContainer");

        // Hapus option
        $option.remove();

        // Update ulang label, value radio, dan name input
        $container.find(".answer-option").each(function (i) {
            let letter = alphabet[i].toLowerCase();

            $(this)
                .find("span.fw-bold")
                .text(letter + ".");
            $(this).find("input[type=radio]").val(letter);

            // update name input text dan file agar tetap urut
            $(this)
                .find("input[type=text]")
                .attr("name", "option_" + letter);
            $(this)
                .find("input[type=file]")
                .attr("name", "option_" + letter + "_image");
        });
    });

    addQuestionTextQuill.on("text-change", function () {
        var value = addQuestionTextQuill.root.innerHTML;
        var descriptionText = addQuestionTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addQuestionText").val(value);
    });
    editQuestionTextQuill.on("text-change", function () {
        var value = editQuestionTextQuill.root.innerHTML;
        var descriptionText = editQuestionTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editQuestionText").val(value);
    });
    addEssayQuestionTextQuill.on("text-change", function () {
        var value = addEssayQuestionTextQuill.root.innerHTML;
        var descriptionText = addEssayQuestionTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#addEssayQuestionText").val(value);
    });
    editEssayQuestionTextQuill.on("text-change", function () {
        var value = editEssayQuestionTextQuill.root.innerHTML;
        var descriptionText = editEssayQuestionTextQuill.getText().trim();
        if (descriptionText === "" || descriptionText === "\n") {
            value = "";
        }
        $("#editEssayQuestionText").val(value);
    });

    $("#addQuestionForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#addQuestionForm").find("input, select").removeClass("is-invalid");
        $("#addQuestionForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        formData.append('question_type', 'multiple_choice');
        formData.append('model', 'question_bank');

        $.ajax({
            url: `/soal/${id}`,
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
                    const options_errors = [];
                    for (const key in errors) {
                        if (
                            $("#addQuestionForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addQuestionForm [name='" + key + "']").hasClass(
                                "quill"
                            )
                        ) {
                            $("#addQuestionForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#addQuestionForm [name='" + key + "']")
                                .parent()
                                .hasClass("answer-option")
                        ) {
                            options_errors.push(errors[key]);
                        } else {
                            $("#addQuestionForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }

                        if (options_errors.length > 0) {
                            const toast = new bootstrap.Toast(
                                $("#toast-error")
                            );
                            $("#toast-error #toast-text").text(
                                options_errors[0]
                            );
                            toast.show();
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

    $("#addEssayQuestionForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#addEssayQuestionForm").find("input, select").removeClass("is-invalid");
        $("#addEssayQuestionForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        formData.append('question_type', 'essay');
        formData.append('model', 'question_bank');

        $.ajax({
            url: `/soal/${id}`,
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
                            $("#addEssayQuestionForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addEssayQuestionForm [name='" + key + "']").hasClass(
                                "quill"
                            )
                        ) {
                            $("#addEssayQuestionForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addEssayQuestionForm [name='" + key + "']")
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

    $("#editQuestionForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editQuestionForm").find("input, select").removeClass("is-invalid");
        $("#editQuestionForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        formData.append('question_type', 'multiple_choice');
        formData.append('model', 'question_bank');

        $.ajax({
            url: `/soal/${id}`,
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
                    const options_errors = [];
                    for (const key in errors) {
                        if (
                            $(
                                "#editQuestionForm [name='" + key + "']"
                            ).hasClass("file_path") ||
                            $(
                                "#editQuestionForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#editQuestionForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#editQuestionForm [name='" + key + "']")
                                .parent()
                                .hasClass("answer-option")
                        ) {
                            options_errors.push(errors[key]);
                        } else {
                            $("#editQuestionForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }

                        if (options_errors.length > 0) {
                            const toast = new bootstrap.Toast(
                                $("#toast-error")
                            );
                            $("#toast-error #toast-text").text(
                                options_errors[0]
                            );
                            toast.show();
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

    $("#editEssayQuestionForm").on("submit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $("#editEssayQuestionForm").find("input, select").removeClass("is-invalid");
        $("#editEssayQuestionForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        formData.append('question_type', 'essay');
        formData.append('model', 'question_bank');

        $.ajax({
            url: `/soal/${id}`,
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
                    const options_errors = [];
                    for (const key in errors) {
                        if (
                            $(
                                "#editEssayQuestionForm [name='" + key + "']"
                            ).hasClass("file_path") ||
                            $(
                                "#editEssayQuestionForm [name='" + key + "']"
                            ).hasClass("quill")
                        ) {
                            $("#editEssayQuestionForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else if (
                            $("#editEssayQuestionForm [name='" + key + "']")
                                .parent()
                                .hasClass("answer-option")
                        ) {
                            options_errors.push(errors[key]);
                        } else {
                            $("#editEssayQuestionForm [name='" + key + "']")
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        }

                        if (options_errors.length > 0) {
                            const toast = new bootstrap.Toast(
                                $("#toast-error")
                            );
                            $("#toast-error #toast-text").text(
                                options_errors[0]
                            );
                            toast.show();
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
});

$(function () {
    // Inisialisasi awal
    const lightbox = GLightbox({
        selector: ".glightbox",
        touchNavigation: true,
        zoomable: true,
        loop: false,
    });

    // Contoh: tombol custom untuk membuka lightbox secara programatik
    $(".btn-open-gallery").on("click", function (e) {
        e.preventDefault();
        lightbox.open(); // buka slide pertama dari selector '.glightbox'
    });

    // Jika kamu menambahkan elemen .glightbox secara dinamis (AJAX/append)
    // panggil reload setelah DOM berubah:
    $(document).on("content:loaded", function () {
        lightbox.reload(); // re-scan DOM untuk elemen baru
    });

    lightbox.on("open", () => {
        document
            .querySelectorAll(".glightbox-button-hidden")
            .forEach((btn) => (btn.style.display = "none"));
    });
});

function handleEditQuestion(e, id, type) {
    e.preventDefault();

    const editBtn = $(e.currentTarget);
    const originalHtml = editBtn.html();
    editBtn
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/soal/${id}/edit?question_type=${type}`,
        method: "GET",
        success: function (res) {
            if (res.success && res.data) {
                if (res.data.question_type === 'multiple') {
                    $("#editQuestionForm").data("id", id);
                    editQuestionTextQuill.setContents(
                        editQuestionTextQuill.clipboard.convert(
                            res.data.question_text
                        )
                    );
                    $("#editQuestionForm [name='question_points']").val(
                        res.data.question_points
                    );
                    $(
                        `#editQuestionForm [name='correct_answer'][value='${res.data.correct_answer}']`
                    ).prop("checked", true);
                    $("#editQuestionForm [name='option_a']").val(res.data.option_a);
                    $("#editQuestionForm [name='option_b']").val(res.data.option_b);
                    $("#editQuestionForm [name='option_c']").val(res.data.option_c);

                    let $container =
                        $("#editQuestionForm").find("#optionsContainer");

                    if (res.data.option_d) {
                        $container.append(elementOption("d", res.data.option_d));
                    } else {
                        if ($("#editQuestionForm [name='option_d']").length > 0)
                            $("#editQuestionForm [name='option_d']")
                                .closest(".answer-option")
                                .remove();
                    }
                    if (res.data.option_e) {
                        $container.append(elementOption("e", res.data.option_e));
                    } else {
                        if ($("#editQuestionForm [name='option_e']").length > 0)
                            $("#editQuestionForm [name='option_e']")
                                .closest(".answer-option")
                                .remove();
                    }
                    $("#editQuestionModal").modal("show");
                } else if (res.data.question_type === 'essay') {
                    $("#editEssayQuestionForm").data("id", id);
                    editEssayQuestionTextQuill.setContents(
                        editEssayQuestionTextQuill.clipboard.convert(
                            res.data.question_text
                        )
                    );
                    $("#editEssayQuestionForm [name='question_points']").val(
                        res.data.question_points
                    );

                    $("#editEssayQuestionModal").modal("show");
                }
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

function handleDeleteQuestion(e, id, type) {
    e.preventDefault();
    const deleteBtn = $(e.currentTarget);
    const originalHtml = deleteBtn.html();

    Swal.fire({
        title: "Hapus Soal",
        text: "Apakah Anda yakin ingin menghapus soal ini?",
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
                url: `/soal/${id}?question_type=${type}`,
                method: "DELETE",
                success: function (response) {
                    if (response.success) {
                        const toast = new bootstrap.Toast($("#toast-success"));
                        $("#toast-success #toast-text").text(response.message);
                        toast.show();
                        location.reload();
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
