function getFileIcon(fileName) {
    var ext = fileName.split(".").pop().toLowerCase();
    if (["doc", "docx"].includes(ext)) return "fa fa-file-word text-primary";
    if (["pdf"].includes(ext)) return "fa fa-file-pdf text-danger";
    if (["xls", "xlsx"].includes(ext)) return "fa fa-file-excel text-success";
    if (["ppt", "pptx"].includes(ext))
        return "fa fa-file-powerpoint text-warning";
    if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext))
        return "fa fa-file-image text-info";
    if (["zip", "rar", "7z"].includes(ext))
        return "fa fa-file-archive text-secondary";
    return "fa fa-file";
}

function getElementFileSimple(fileName, fileSize, fileIcon, idx = 0) {
    return `
    <div class="d-flex align-items-center gap-2 border p-2 rounded mt-2 file-preview-item" data-idx="${idx}">
        <div style="display:flex;align-items:center;justify-content:center;width:24px;height:24px;">
            <i class="${fileIcon}" style="font-size:16px;"></i>
        </div>
        <div style="flex: 1; min-width: 0;">
            <div class="text-truncate fw-500" title="${fileName}" style="font-size:13px;">${fileName}</div>
            <div style="font-size:11px;color:#888;">${fileSize}</div>
        </div>
        <button type="button" class="btn btn-sm text-danger p-0 btn-clear-file-simple" data-idx="${idx}">
            <i class="fa fa-times-circle fs-6"></i>
        </button>
    </div>
    `;
}

function getElementFile(fileName, fileSize, fileIcon, idx = 0) {
    return `
    <div class="d-flex align-items-center gap-1 justify-content-between file border w-100 rounded-2 p-3 mb-2 file-preview-item" data-idx="${idx}">
        <div class="d-flex align-items-center">
            <div  style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;margin-right:5px;">
                <i class="${fileIcon}" style="color:#1976d2;font-size:18px;"></i>
            </div>
            <div>
                <div class="fw-bold text-break">${fileName}</div>
                <div style="font-size:12px;color:#888;">${fileSize}</div>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-link text-danger p-0 btn-remove-file" style="width:32px;height:32px;" title="Hapus" data-idx="${idx}"><i class="fa fa-trash"></i></button>
        </div>
    </div>
    `;
}

$(document).ready(function () {
    // 1. LOGIK UNTUK IMPORT MODAL
    $(document).on("change", "#importQuestionModal input[type='file']", function () {
        const $fileInput = $(this);
        const $parent = $fileInput.closest('.taskFile');
        const $dropArea = $parent.find(".custom-file-upload");
        const $preview = $parent.find("#import-file-preview");

        if (this.files && this.files.length > 0) {
            let html = "";
            Array.from(this.files).forEach((file, idx) => {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2) + "mb";
                const fileIcon = getFileIcon(file.name);
                html += getElementFileSimple(file.name, fileSize, fileIcon, idx);
            });
            $preview.html(html);
            $dropArea.hide();
        } else {
            $preview.empty();
            $dropArea.show();
        }
    });

    // 2. LOGIK UNTUK TAMBAH/EDIT SOAL (SAMA DENGAN IMPORT TAPI TIDAK SEMBUNYIKAN AREA)
    $(document).on("change", "form:not(#importQuestionForm) input[type='file'].file_path", function () {
        const $fileInput = $(this);
        const $parent = $fileInput.closest('.taskFile, .col-12');
        const $previewContainer = $parent.find("#file-preview");

        if (this.files && this.files.length > 0) {
            let html = "";
            Array.from(this.files).forEach((file, idx) => {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2) + "mb";
                const fileIcon = getFileIcon(file.name);
                html += getElementFileSimple(file.name, fileSize, fileIcon, idx);
            });
            $previewContainer.html(html);
        } else {
            $previewContainer.empty();
        }
    });

    // 3. Tombol Hapus (Berlaku untuk semua)
    $(document).on("click", ".btn-clear-file-simple", function (e) {
        e.preventDefault();
        const idx = $(this).data("idx");
        const $parent = $(this).closest('.taskFile, .col-12');
        const $fileInput = $parent.find("input[type='file']");

        if (idx === 'server') {
            const name = $fileInput.attr('name');
            const $form = $(this).closest('form');
            $form.append(`<input type="hidden" name="deleteData[]" value="${name}">`);
            $(this).closest('.file-preview-item').remove();
            $fileInput.val("");
        } else {
            // Reset file input
            $fileInput.val("");
            // Trigger change untuk update UI
            $fileInput.trigger("change");
        }
    });

    // 4. Drag and Drop Handling
    $(document).on("dragover", ".custom-file-upload", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass("is-drag");
    });

    $(document).on("dragleave", ".custom-file-upload", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("is-drag");
    });

    $(document).on("drop", ".custom-file-upload", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass("is-drag");

        const files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            const $parent = $(this).closest('.taskFile, .col-12');
            const $fileInput = $parent.find("input[type='file']");
            if ($fileInput.length > 0) {
                $fileInput[0].files = files;
                $fileInput.trigger("change");
            }
        }
    });
});
