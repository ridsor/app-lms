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

$(document).ready(function () {
    let alphabet = ["A", "B", "C", "D", "E"];

    // Tambah opsi
    $("#addOption").click(function (e) {
        e.preventDefault();
        let count = $("#optionsContainer .answer-option").length;
        if (count < alphabet.length) {
            let letter = alphabet[count];
            let newOption = `
            <div class="answer-option d-flex align-items-center checkbox-checked">
                <input type="radio" name="correct" value="${letter}" class="me-2 form-check-inputs">
                <span class="fw-bold">${letter}.</span>
                <input type="text" class="form-control"
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
            $("#optionsContainer").append(newOption);
        }
    });

    // Preview gambar
    $(document).on("change", ".option-image", function () {
        let file = this.files[0];
        let preview = $(this).closest(".answer-option").find(".img-preview");
        let file_icon = $(this).closest(".answer-option").find(".file-icon");
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                preview.attr("src", e.target.result).show();
                file_icon.hide();
            };
            reader.readAsDataURL(file);
        } else {
            preview.hide();
            file_icon.show();
        }
    });

    // Hapus opsi (kecuali A, B, C)
    $(document).on("click", ".remove-option", function () {
        let letter = $(this).siblings("span.fw-bold").text().replace(".", "");
        if (letter === "A" || letter === "B" || letter === "C") {
            return;
        }
        $(this).closest(".answer-option").remove();

        // Update ulang label
        $("#optionsContainer .answer-option").each(function (index) {
            $(this)
                .find("span.fw-bold")
                .text(alphabet[index] + ".");
            $(this).find("input[type=radio]").val(alphabet[index]);
        });
    });
});
