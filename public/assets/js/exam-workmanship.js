// initial setup
const params = new URLSearchParams(window.location.search);
let q = Number(params.get("q")) || 1;
let lightbox;
let answered = new Map();
old_answer.forEach((item) => {
    answered.set(item.question_id, item.answer);
});

$(document).ready(function () {
    $(async function () {
        await loadQuestion(q);
        $("#question-loading").remove();
    });

    document.addEventListener("qValueChanged", (e) => {
        params.set("q", q);
        const newUrl = `${window.location.pathname}?${params.toString()}${
            window.location.hash
        }`;
        window.history.replaceState({}, "", newUrl);
    });

    // ===== Timer Countdown =====
    if (hasDuration) {
        let display = document.getElementById("countdown");

        let timer = setInterval(function () {
            let minutes = Math.floor(duration / 60);
            let seconds = duration % 60;

            display.textContent =
                String(minutes).padStart(2, "0") +
                ":" +
                String(seconds).padStart(2, "0");

            if (--duration < 0) {
                clearInterval(timer);
                submitExam();
            }
        }, 1000);
    }

    // ===== Deteksi tab pindah =====
    // let warningCount = 0;
    // document.addEventListener("visibilitychange", () => {
    //     if (document.hidden) {
    //         warningCount++;

    //         showToast(
    //             "error",
    //             `⚠️ Anda meninggalkan tab ujian! (${warningCount}x)`
    //         );

    //         if (warningCount >= 3) {
    //             submitExam();
    //         }
    //     }
    // });

    // ===== Progress Bar =====
    function updateProgress() {
        let percent = (answered.size / total) * 100;
        let bar = document.getElementById("progress-bar");
        bar.style.width = percent + "%";
        bar.setAttribute("aria-valuenow", answered.size);

        answered.forEach((value, key) => {
            let navBtn = document.getElementById("nav-q" + key);
            if (navBtn) navBtn.classList.remove("btn-outline-secondary");
            if (navBtn) navBtn.classList.add("btn-success");
        });
    }

    // ===== Jawaban event =====
    const handleEventChangeAnswer = function () {
        const question_id = $("#question-area").data("id");
        const answer = $(".answer:checked").val();

        answered.set(question_id, answer);
        submitAnswer({ answer, question_id });

        updateProgress();
    };

    function setupEventChangeAnswer() {
        document.querySelectorAll(".answer").forEach((el) => {
            el.addEventListener("change", handleEventChangeAnswer);
        });
    }
    function resetEventChangeAnswer() {
        document.querySelectorAll(".answer").forEach((el) => {
            el.removeEventListener("change", handleEventChangeAnswer);
        });
    }

    // ===== Navigasi Soal (demo) =====
    document.querySelectorAll(".question-nav").forEach((btn) => {
        btn.addEventListener("click", async function (e) {
            if (q === Number(this.dataset.q)) return;

            const submitBtn = $(this);
            const originalHtml = submitBtn.html();
            submitBtn
                .prop("disabled", true)
                .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

            try {
                await loadQuestion(this.dataset.q);
            } catch (e) {
            } finally {
                submitBtn.prop("disabled", false).html(originalHtml);
            }
        });
    });

    async function loadQuestion(question) {
        resetEventChangeAnswer();

        return $.ajax({
            url: `/ujian/${exam_id}/pengerjaan/soal?q=${question}`,
            method: "GET",
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $("#question-area").data("id", res.data.question.id);
                    $("#question-text").html(res.data.question.question_text);
                    $("#question-file").html(
                        getElementFileQuestion(res.data.question)
                    );
                    $("#option-list").html("");
                    $("#option-list").append(
                        getElementOption(
                            "a",
                            res.data.question.option_a,
                            res.data.question.option_a_image,
                            res.data.question,
                            question
                        )
                    );
                    $("#option-list").append(
                        getElementOption(
                            "b",
                            res.data.question.option_b,
                            res.data.question.option_b_image,
                            res.data.question,
                            question
                        )
                    );
                    $("#option-list").append(
                        getElementOption(
                            "c",
                            res.data.question.option_c,
                            res.data.question.option_c_image,
                            res.data.question,
                            question
                        )
                    );
                    if (res.data.question.option_d) {
                        $("#option-list").append(
                            getElementOption(
                                "d",
                                res.data.question.option_d,
                                res.data.question.option_d_image,
                                res.data.question,
                                question
                            )
                        );
                    }
                    if (res.data.question.option_e) {
                        $("#option-list").append(
                            getElementOption(
                                "e",
                                res.data.question.option_e,
                                res.data.question.option_e_image,
                                res.data.question,
                                question
                            )
                        );
                    }

                    q = Number(question);
                    const event = new CustomEvent("qValueChanged");
                    lightbox.reload();
                    document.dispatchEvent(event);

                    setupEventChangeAnswer();
                }
            },
            error: function (xhr) {
                const toast = new bootstrap.Toast($("#toast-error"));
                $("#toast-error #toast-text").text(xhr.responseJSON.message);
                toast.show();
            },
        });
    }

    function getElementFileQuestion(question) {
        if (!question.question_file) {
            return "";
        }

        const file_type = getFileType(question.question_file);
        let html = "";

        switch (file_type) {
            case "image":
                html = `
                    <div class="image">
                        <a href="/soal/${question.id}/file"
                            class="glightbox" data-gallery="question-${question.id}"
                            data-type="image">
                            <img src="/soal/${question.id}/file"
                                alt="soal gambar"
                                style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                        </a>
                    </div>
                `;
                break;

            case "audio":
                const audioExt =
                    getFileExtension(questionFileName).toLowerCase();
                let audioSource = "";

                if (audioExt === "mp3") {
                    audioSource = `<source src="/soal/${question.id}/file" type="audio/mpeg">`;
                } else if (audioExt === "wav") {
                    audioSource = `<source src="/soal/${question.id}/file" type="audio/wav">`;
                } else if (audioExt === "ogg") {
                    audioSource = `<source src="/soal/${question.id}/file" type="audio/ogg">`;
                }

                html = `
                    <div class="audio">
                        <audio controls>
                            ${audioSource}
                            Browser Anda tidak mendukung elemen audio.
                        </audio>
                    </div>
                `;
                break;

            case "video":
                html = `
                    <div class="video">
                        <video width="320" height="240" controls>
                            <source src="/soal/${question.id}/file" type="video/mp4">
                            Browser Anda tidak mendukung elemen video.
                        </video>
                    </div>
                `;
                break;

            case "archive":
                html = `
                    <div class="archive">
                        <div class="rounded-2 d-flex align-items-center gap-3">
                            <div style="display:flex;align-items:center;justify-content:center;min-width:24px;min-height:24px;">
                                <i class="fa fa-file text-primary fs-3"></i>
                            </div>
                            <div class="fw-medium text-break">
                                File Arsip
                            </div>
                            <a href="/soal/${question.id}/file/download"
                                style="width: 32px; height: 32px;"
                                class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                `;
                break;

            case "document":
                html = `
                    <div class="document">
                        <div class="rounded-2 d-flex align-items-center gap-3">
                            <div style="display:flex;align-items:center;justify-content:center;min-width:24px;min-height:24px;">
                                <i class="fa fa-file text-primary fs-3"></i>
                            </div>
                            <div class="fw-medium text-break">
                                Dokumen
                            </div>
                            <a href="/soal/${question.id}/file/download"
                                style="width: 32px; height: 32px;"
                                class="btn d-flex align-items-center bg-20-info border justify-content-center text-info p-2">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                `;
                break;

            default:
                html = "";
        }

        html = `
        <div class="mb-2">
            ${html}
        <div>
        `;

        return html;
    }

    function getElementOption(type, option, image, question, q) {
        let html;

        let imagehtml = null;

        if (image) {
            imagehtml = `
            <div class="option-image mt-1">
                <a href="/soal/${question.id}/${type}/file"
                    class="glightbox" data-type="image"
                    data-gallery="option-${type}-b">
                    <img src="/soal/${question.id}/${type}/file"
                        alt="opsi gambar"
                        style="max-width:150px; max-height:150px; object-fit:cover; object-position:center;" />
                </a>
            </div>
            `;
        }

        html = `
        <div class="option-item checkbox-checked">
            <div class="d-flex align-items-center gap-2">
                <label class="d-flex align-items-center mb-0"
                    style="align-self: flex-start">
                    <input type="radio" value="${type}"
                        name="q${q}"
                         class="me-2 form-check-input answer"
                        style="transform: translateY(-2px)">
                    <span class="fw-bold text-uppercase">${type}.</span>
                </label>
                <div class="option-label">
                    <p class="mb-0">
                        ${option}
                    </p>
                    ${imagehtml ? imagehtml : ""}
                </div>
            </div>
        </div>
        `;

        return html;
    }

    async function submitExam() {
        const formData = new FormData();
        let index = 0;
        answered.forEach((value, key) => {
            formData.append(`answered[${index}][question_id]`, key);
            formData.append(`answered[${index}][answer]`, value);
            index++;
        });

        await $.ajax({
            url: `/ujian/${exam_id}/pengerjaan`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    showToast("success", response.message);
                    window.location.href = routeResult;
                }
            },
            error: function (xhr) {
                showToast(
                    "error",
                    xhr.responseJSON?.message || "Terjadi kesalahan."
                );
            },
        });
    }

    function submitAnswer({ answer, question_id }) {
        const formData = new FormData();
        formData.append(`answer`, answer);
        formData.append(`question_id`, question_id);

        $.ajax({
            url: `/ujian/${exam_id}/pengerjaan/answer`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
        });
    }

    $("#btn-submit").on("click", function (e) {
        const submitBtn = $(this);
        const originalHtml = submitBtn.html();
        Swal.fire({
            title: "Selesaikan Ujian",
            text: "Apakah Anda yakin ingin menyelesaikan ujian?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: "Batal",
        }).then(async (result) => {
            if (result.isConfirmed) {
                submitBtn
                    .prop("disabled", true)
                    .html(
                        'Kirim <i class="fa-solid fa-arrows-rotate fa-spin"></i>'
                    );
                await submitExam();
                submitBtn.prop("disabled", false).html(originalHtml);
            }
        });
    });

    updateProgress();
});

$(function () {
    // Inisialisasi awal
    lightbox = GLightbox({
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
