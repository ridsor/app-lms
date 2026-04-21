// =================== Initial Setup ===================
const params = new URLSearchParams(window.location.search);
let q = Number(params.get("q")) || 1;
let lightbox;
const answered = new Map();

if (old_answer) {
    old_answer.forEach((item) => {
        const typeSuffix = item.questionable_type.includes("MultipleQuestion") ? "multiple" : "essay";
        answered.set(item.questionable_id + typeSuffix, item.answer);
    });
}

// =================== Document Ready ===================
$(document).ready(function () {
    (async function () {
        await loadQuestion(q);
        $("#question-loading").remove();

        if (answered.size > 0) {
            answered.forEach((_, key) => {
                const navBtn = document.getElementById(`nav-q${key}`);
                if (navBtn) {
                    navBtn.classList.remove("btn-outline-secondary");
                    navBtn.classList.add("btn-success");
                }
            });
            updateProgress();
        }
    })();

    // Update query param on q change
    document.addEventListener("qValueChanged", () => {
        params.set("q", q);
        const newUrl = `${window.location.pathname}?${params.toString()}${window.location.hash}`;
        window.history.replaceState({}, "", newUrl);
    });

    // =================== Timer Countdown ===================
    if (hasDuration) {
        const display = document.getElementById("countdown");

        // Sisa waktu dalam detik
        const endTime = Date.now() + duration * 1000;

        const timer = setInterval(() => {
            const now = Date.now();
            const diff = Math.floor((endTime - now) / 1000); // sisa detik

            if (diff <= 0) {
                clearInterval(timer);
                display.textContent = "00:00";
                submitUKK();
                return;
            }

            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;

            display.textContent = `${String(minutes).padStart(2, "0")}:${String(
                seconds
            ).padStart(2, "0")}`;
        }, 1000);
    }

    // =================== Progress Bar ===================
    function updateProgress() {
        const percent = (answered.size / total) * 100;
        const bar = document.getElementById("progress-bar");
        if (bar) {
            bar.style.width = percent + "%";
            bar.setAttribute("aria-valuenow", answered.size);
        }
    }

    // =================== Jawaban Event ===================
    function handleEventChangeAnswer() {
        const question_id = $("#question-area").attr("data-id");
        const question_type = $("#question-area").attr("data-type");
        const answer = $(".answer:checked").val();

        if (answer) {
            answered.set(question_id + question_type, answer);
            submitAnswer({ answer, question_id, question_type });

            $(`.nav-q${q}`)
                .removeClass("btn-outline-secondary")
                .addClass("btn-success");
        } else {
            answered.delete(question_id + question_type);
            $(`.nav-q${q}`)
                .addClass("btn-outline-secondary")
                .removeClass("btn-success");
        }

        updateProgress();
    }

    function handleEventChangeAnswerSubmit() {
        const question_id = $("#question-area").attr("data-id");
        const question_type = $("#question-area").attr("data-type");
        const answer = $(".answer").val();

        if (answer) {
            answered.set(question_id + question_type, answer);
            submitAnswer({ answer, question_id, question_type });

            $(`.nav-q${q}`)
                .removeClass("btn-outline-secondary")
                .addClass("btn-success");
        } else {
            answered.delete(question_id + question_type);
            $(`.nav-q${q}`)
                .addClass("btn-outline-secondary")
                .removeClass("btn-success");
        }

        updateProgress();
    }

    $(document).on("change", ".answer", handleEventChangeAnswer);
    $(document).on("click", ".answer-submit", handleEventChangeAnswerSubmit);

    // =================== Navigasi Soal ===================
    document.querySelectorAll(".question-nav").forEach((btn) => {
        btn.addEventListener("click", async function () {
            if (q === Number(this.dataset.q)) return;

            const navBtn = $(this);
            const originalHtml = navBtn.html();

            navBtn
                .prop("disabled", true)
                .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

            try {
                await loadQuestion(this.dataset.q);
            } finally {
                navBtn.prop("disabled", false).html(originalHtml);
            }
        });
    });

    // =================== Load Question ===================
    async function loadQuestion(question) {
        return $.ajax({
            url: `/ukk/${ukk_id}/teori/pengerjaan/soal?q=${question}`,
            method: "GET",
            success: function (res) {
                if (res.success) {
                    const qData = res.data.question;
                    $("#question-area").attr("data-id", qData.id);
                    $("#question-area").attr("data-type", qData.question_type);
                    $("#question-text").html(qData.question_text);
                    $("#question-file").html(getElementFileQuestion(qData));

                    $("#question-answer").empty();
                    if (qData.question_type === "multiple") {
                        $("#question-answer").html('<div class="option-list d-flex flex-column gap-2" id="option-list"></div>');
                        const optionList = $("#option-list");
                        ["a", "b", "c", "d", "e"].forEach((type) => {
                            if (qData[`option_${type}`]) {
                                optionList.append(
                                    getElementOption(
                                        type,
                                        qData[`option_${type}`],
                                        qData[`option_${type}_image`],
                                        qData,
                                        question
                                    )
                                );
                            }
                        });
                        if (res.data.answer) {
                            $(`.answer[value='${res.data.answer.answer}']`).prop("checked", true);
                        }
                    } else if (qData.question_type === "essay") {
                        $("#question-answer").html(`
                            <div class="mb-2">
                                <textarea class="form-control answer w-100" rows="4" placeholder="Tulis jawaban Anda di sini...">${res.data.answer ? res.data.answer.answer : ""}</textarea>
                                <button type="button" class="btn btn-primary mt-2 answer-submit">Simpan Jawaban</button>
                            </div>
                        `);
                    }
                    q = Number(question);
                    document.dispatchEvent(new CustomEvent("qValueChanged"));
                    if (lightbox) lightbox.reload();
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Gagal memuat soal'
                });
            },
        });
    }

    // =================== Render Elemen Soal ===================
    function getElementFileQuestion(question) {
        if (!question.question_file) return "";
        const fileType = getFileType(question.question_file);
        let html = "";

        switch (fileType) {
            case "image":
                html = `<div class="image">
                    <a href="/soal/${question.id}/file?type=${question.question_type}" class="glightbox" data-gallery="question-${question.id}" data-type="image">
                        <img src="/soal/${question.id}/file?type=${question.question_type}" alt="soal gambar" style="max-width:150px; max-height:150px; object-fit:cover;" />
                    </a>
                </div>`;
                break;
            case "audio":
                html = `<div class="audio"><audio controls><source src="/soal/${question.id}/file?type=${question.question_type}" type="audio/mpeg">Browser Anda tidak mendukung elemen audio.</audio></div>`;
                break;
            case "video":
                html = `<div class="video"><video width="320" height="240" controls><source src="/soal/${question.id}/file?type=${question.question_type}" type="video/mp4">Browser Anda tidak mendukung elemen video.</video></div>`;
                break;
            case "archive":
            case "document":
                html = `<div class="document d-flex align-items-center gap-2">
                    <i class="fa fa-file text-primary fs-3"></i>
                    <span>File Lampiran</span>
                    <a href="/soal/${question.id}/file/download?type=${question.question_type}" class="btn btn-sm btn-info"><i class="fa fa-download"></i></a>
                </div>`;
                break;
        }
        return `<div class="mb-2">${html}</div>`;
    }

    function getElementOption(type, option, image, question, qNum) {
        const imageHtml = image ? `<div class="option-image mt-1">
            <a href="/soal/${question.id}/${type}/file" class="glightbox" data-type="image">
                <img src="/soal/${question.id}/${type}/file" alt="opsi gambar" style="max-width:150px; max-height:150px; object-fit:cover;" />
            </a>
        </div>` : "";

        return `<div class="option-item">
            <div class="d-flex align-items-start gap-2">
                <input type="radio" value="${type}" name="q${qNum}" class="form-check-input answer mt-1">
                <div class="option-label">
                    <span class="fw-bold text-uppercase">${type}.</span> ${option}
                    ${imageHtml}
                </div>
            </div>
        </div>`;
    }

    // =================== Submit ===================
    async function submitUKK() {
        const formData = new FormData();
        let index = 0;

        answered.forEach((value, key) => {
            // key is id+type (e.g., 10multiple)
            const question_id = key.replace("multiple", "").replace("essay", "");
            const question_type = key.includes("multiple") ? "multiple" : "essay";

            formData.append(`answered[${index}][question_id]`, question_id);
            formData.append(`answered[${index}][question_type]`, question_type);
            formData.append(`answered[${index}][answer]`, value);
            index++;
        });

        return $.ajax({
            url: `/ukk/${ukk_id}/teori/pengerjaan`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                    }).then(() => {
                        window.location.href = `/ukk/${ukk_id}/teori/pengerjaan/hasil`;
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            },
        });
    }

    function submitAnswer({ answer, question_id, question_type }) {
        const formData = new FormData();
        formData.append("answer", answer);
        formData.append("question_id", question_id);
        formData.append("question_type", question_type);

        $.ajax({
            url: `/ukk/${ukk_id}/teori/pengerjaan/answer`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
        });
    }

    $("#btn-submit").on("click", function () {
        Swal.fire({
            title: "Selesaikan UKK",
            text: "Apakah Anda yakin ingin menyelesaikan UKK?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: "Batal",
        }).then(async (result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop("disabled", true).html('Kirim <i class="fa-solid fa-arrows-rotate fa-spin"></i>');
                await submitUKK();
                btn.prop("disabled", false).html(originalHtml);
            }
        });
    });

    // Helper functions for file types
    function getFileType(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].includes(ext)) return 'image';
        if (['mp4', 'webm', 'ogg'].includes(ext)) return 'video';
        if (['mp3', 'wav', 'ogg'].includes(ext)) return 'audio';
        if (['zip', 'rar', '7z'].includes(ext)) return 'archive';
        return 'document';
    }

    // ===== Deteksi tab pindah =====
    let warningCount = 0;
    const maxWarnings = 3;

    document.addEventListener("visibilitychange", () => {
        if (!document.hidden) return;

        warningCount++;

        showToast(
            "error",
            `⚠️ Anda meninggalkan tab UKK! (${warningCount}/${maxWarnings}) - Batas pelanggaran ${maxWarnings} kali`
        );

        if (warningCount >= maxWarnings) {
            submitUKK();
        }
    });
});

$(function () {
    lightbox = GLightbox({
        selector: ".glightbox",
        touchNavigation: true,
        zoomable: true,
    });
});
