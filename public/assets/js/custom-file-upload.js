$(document).ready(function () {
    var $dropArea = $("#custom-file-upload");
    var $fileInput = $("#custom-file-upload").parent().find("input[type='file']");
    var $label = $dropArea.find("span:last");

    $dropArea.off("dragover dragleave drop");

    // Highlight area saat drag
    $dropArea.on("dragover", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass("is-drag");
    });

    $dropArea.on("dragleave", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("is-drag");
    });

    // Drop file
    $dropArea.on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            $fileInput[0].files = files;
            showFilePreview(files);
        }
    });

    // Jika pilih file manual, tampilkan nama file
    $fileInput.on("change", function () {
        if (this.files && this.files[0]) {
            showFilePreview(this.files);
        }
    });

    // Fungsi icon file
    function getFileIcon(fileName) {
        var ext = fileName.split(".").pop().toLowerCase();
        if (["doc", "docx"].includes(ext))
            return "fa fa-file-word text-primary";
        if (["pdf"].includes(ext)) return "fa fa-file-pdf text-danger";
        if (["xls", "xlsx"].includes(ext))
            return "fa fa-file-excel text-success";
        if (["ppt", "pptx"].includes(ext))
            return "fa fa-file-powerpoint text-warning";
        if (["jpg", "jpeg", "png", "gif"].includes(ext))
            return "fa fa-file-image text-info";
        return "fa fa-file";
    }
    function showFilePreview(files) {
        var html = "";

        Array.from(files).forEach(function (file, idx) {
            var fileSize = (file.size / 1024).toFixed(2) + "KB";
            var fileName = file.name;
            var fileIcon = getFileIcon(fileName);

            html += `
            <div class="d-flex align-items-center gap-1 justify-content-between bg-light rounded-2 p-3 mb-2 file-preview-item" data-idx="${idx}">
                <div class="d-flex align-items-center">
                    <div  style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;margin-right:5px;">
                        <i class="${fileIcon}" style="color:#1976d2;font-size:18px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${fileName}</div>
                        <div style="font-size:12px;color:#888;">${fileSize}</div>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-link text-danger p-0 btn-remove-file" style="width:32px;height:32px;" title="Hapus" data-idx="${idx}"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        `;
        });

        const $preview = $("#file-preview");
        if (files.length === 0) {
            $preview.html("");
            $dropArea.show();
        } else {
            $dropArea.hide();
            $preview.html(html);
        }

        // Hapus file (khusus multiple)
        $preview.find(".btn-remove-file").on("click", function () {
            var idx = $(this).data("idx");
            var dt = new DataTransfer();
            Array.from($fileInput[0].files).forEach(function (file, i) {
                if (i != idx) dt.items.add(file);
            });
            $fileInput[0].files = dt.files;
            showFilePreview($fileInput[0].files);
            if ($fileInput[0].files.length === 0) $label.text("Unggah File");
        });
    }
});
