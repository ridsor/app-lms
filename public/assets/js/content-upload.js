function getElementContent(value, fileSize, idx = 0) {
    return `
    <div class="d-flex align-items-center gap-1 justify-content-between file border w-100 rounded-2 p-3 file-preview-item"
        data-idx="${idx}">
        <div class="d-flex align-items-center">
            <div
                style="display:flex;align-items:center;justify-content:center;min-width:32px;min-height:32px;margin-right:5px;">
                <i class="${getContentIcon(
                    value
                )}" style="color:#1976d2;font-size:18px;"></i>
            </div>
            <div>
                <div class="text-break" style="font-size:0.8rem">${value}${
        fileSize ? ` (${(fileSize / (1024 * 1024)).toFixed(2)}mb)` : ""
    }</div>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-link text-danger p-0 btn-remove-file"
                style="width:32px;height:32px;" title="Hapus" data-idx="${idx}"><i
                    class="fa fa-trash"></i></button>
        </div>
    </div>
    `;
}

function isValidUrlContent(value) {
    try {
        new URL(value.url);
        return true;
    } catch (err) {
        return false;
    }
}

function getContentIcon(value) {
    if (isValidUrlContent(value)) return "fa fa-link text-info";
    var ext = value.split(".").pop().toLowerCase();
    if (["doc", "docx"].includes(ext)) return "fa fa-file-word text-primary";
    if (["pdf"].includes(ext)) return "fa fa-file-pdf text-danger";
    if (["xls", "xlsx"].includes(ext)) return "fa fa-file-excel text-success";
    if (["ppt", "pptx"].includes(ext))
        return "fa fa-file-powerpoint text-warning";
    if (["jpg", "jpeg", "png", "gif"].includes(ext))
        return "fa fa-file-image text-info";
    if (["kml", "gpx", "geojson"].includes(ext))
        return "fa fa-map-marker text-success";
    return "fa fa-file";
}

function showContentPreview(contents, preview, callback = undefined) {
    var html = "";
    Array.from(contents).forEach(function (content, idx) {
        if (isValidUrlContent(content)) {
            html += getElementContent(content.url, fileSize, idx);
        } else {
            var fileSize = content.size;
            var fileName = content.name;

            html += getElementContent(fileName, fileSize, idx);
        }
    });
    if (contents.length === 0) {
        preview.html("");
    } else {
        preview.html(html);
    }

    if (callback) {
        callback();
    }
}
