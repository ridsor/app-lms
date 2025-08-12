const group_members = new Tagify(document.querySelector("#group_members"), {
    delimiters: null,
    enforceWhitelist: true,
    whitelist: members,
    dropdown: {
        enabled: 1,
    },
});

$(document).ready(function () {
    group_members.on("change", function (e) {
        checkSubmitTask();
    });

    const preview = $("#task-submission-preview");
    if (taskSubmissioncontents) {
        showContentPreview(taskSubmissioncontents, preview);
    }

    $("#task-submission-content-link").on("submit", function (e) {
        e.preventDefault();
        const linkInput = $("#task-submission-content-link [name='link']");
        const value = linkInput.val().trim();

        if (!isValidUrlContent({ url: value })) {
            linkInput
                .addClass("is-invalid")
                .next(".invalid-feedback")
                .text("Link tidak valid.");
            return false;
        }

        taskSubmissioncontents.push({
            url: value,
        });

        showContentPreview(taskSubmissioncontents, preview, checkSubmitTask);

        $("#linkModal").modal("hide");

        $("#task-submission-content-link")[0].reset();
    });

    $("#task-submission-content-file").on("change", function (e) {
        taskSubmissioncontents = taskSubmissioncontents.concat(
            Array.from(e.target.files)
        );
        showContentPreview(taskSubmissioncontents, preview, checkSubmitTask);
    });

    // Hapus file (khusus multiple)
    const handleRemoveContent = function () {
        var idx = $(this).data("idx");
        taskSubmissioncontents = taskSubmissioncontents.filter((item, i) => {
            if (!(i != idx)) {
                if (item.id) {
                    deleteContent.push(item.id);
                }
            }
            return i != idx;
        });
        showContentPreview(taskSubmissioncontents, preview, checkSubmitTask);
        console.log(deleteContent);
    };
    preview.on("click", ".btn-remove-file", handleRemoveContent);

    function checkSubmitTask() {
        if (taskSubmissioncontents.length > 0) {
            $("#submit-task").attr("disabled", false);
        } else {
            $("#submit-task").attr("disabled", true);
        }
    }
    $("#submit-task").on("click", function () {
        const task_id = $(this).data("id");
        const btnSubmit = $(this);
        const originalHtml = btnSubmit.html();
        btnSubmit
            .prop("disabled", true)
            .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

        const payload = {
            links: [],
            files: [],
        };

        taskSubmissioncontents.forEach((item, i) => {
            if (isValidUrlContent(item)) {
                payload.links.push({
                    id: item.id ?? i,
                    url: item.url,
                });
            } else if (item instanceof File) {
                payload.files.push({
                    id: item.id ?? i,
                    file: item,
                });
            }
        });

        const formData = new FormData();
        if (document.querySelector("#group_members")) {
            group_members.value.forEach((item, index) => {
                formData.append(`group_members[${index}]`, item.value);
            });
        }
        deleteContent.forEach((item, index) => {
            formData.append(`deleteContent[${index}]`, item);
        });
        payload.links.forEach((link, index) => {
            formData.append(`links[${index}][id]`, link.id);
            formData.append(`links[${index}][url]`, link.url);
        });
        payload.files.forEach((item, index) => {
            formData.append(`files[${index}][id]`, item.id);
            formData.append(`files[${index}][file]`, item.file);
        });

        $.ajax({
            url: `/tugas/${task_id}`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(res.message);
                    toast.show();
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
                btnSubmit.prop("disabled", false).html(originalHtml);
            },
        });
    });
});
