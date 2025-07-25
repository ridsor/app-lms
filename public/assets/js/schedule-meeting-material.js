$(document).ready(function () {
    $("#addMaterialForm [name='file_type']").on("change", function () {
        const value = $(this).val();
        const $file = $("#addMaterialForm .materialFile");
        $file.show();
        $file.parent().find(".materialLink").hide();

        if (value === "eBook") {
            $file.find("#custom-file-upload").css({
                "pointer-events": "auto",
                opacity: 1,
            });
            $file.find(".info").show();
            $file
                .find("input")
                .attr("accept", ".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx");
            $file.find("input").attr("disabled", false);
            $file.next(".materialLink").find("input").attr("disabled", true);
        } else if (value === "Archive") {
            $file.find("#custom-file-upload").css({
                "pointer-events": "auto",
                opacity: 1,
            });
            $file.find(".info").show();
            $file.find("input").attr("accept", ".zip,.rar");
            $file.find("input").attr("disabled", false);
            $file.next(".materialLink").find("input").attr("disabled", true);
        } else if (value === "Link") {
            $file.hide();
            $file.parent().find(".materialLink").show();
            $file.find("input").attr("disabled", true);
        } else {
            $file.find("#custom-file-upload").css({
                "pointer-events": "none",
                opacity: 0.6,
            });
            $file.find(".info").hide();
            $file.find("input").removeAttr("accept");
            $file.find("input").attr("disabled", true);
            $file.next(".materialLink").find("input").attr("disabled", true);
        }
    });

    $("#addMaterialForm").on("submit", function (e) {
        e.preventDefault();
        const meeting_id = $(this).data("id");

        $("#addMaterialForm").find("input, select").removeClass("is-invalid");
        $("#addMaterialForm").find(".invalid-feedback").text("");
        const submitBtn = $(this).find("button[type='submit']");
        const originalHtml = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm spinner_loader" role="status" aria-hidden="true"></span> Loading...'
            );
        const formData = new FormData(this);
        $.ajax({
            url: `/jadwal/pertemuan/${meeting_id}/materi`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    const toast = new bootstrap.Toast($("#toast-success"));
                    $("#toast-success #toast-text").text(response.message);
                    toast.show();
                    $("#addMaterialForm")[0].reset();
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    console.log(errors);
                    for (const key in errors) {
                        console.log($("#addMaterialForm [name='" + key + "']"));
                        if (
                            $("#addMaterialForm [name='" + key + "']").hasClass(
                                "file_path"
                            ) ||
                            $("#addMaterialForm [name='" + key + "']").hasClass(
                                "quill"
                            )
                        ) {
                            $("#addMaterialForm [name='" + key + "']")
                                .parent()
                                .addClass("is-invalid")
                                .next(".invalid-feedback")
                                .text(errors[key][0]);
                        } else {
                            $("#addMaterialForm [name='" + key + "']")
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
            },
            complete: function () {
                submitBtn.prop("disabled", false).html(originalHtml);
            },
        });
    });
});
