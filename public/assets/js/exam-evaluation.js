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

const handleSendScore = (inputElement) => {
    const $input = $(inputElement);

    const score = $input.val();

    const nameAttribute = $input.attr('name');
    const examId = $input.attr('exam-id');
    const match = nameAttribute.match(/\[(\d+)\]/);

    if (!match) return;

    const answerId = match[1];

    // Proses AJAX jQuery
    $.ajax({
        url: `/ujian/${examId}/score/${answerId}`,
        method: 'POST',
        data: {
            score: score
        },
        success: function (response) {
        },
        error: function (xhr, status, error) {
            console.error('Error:', xhr.responseJSON.message);

            // Indikator error (warna merah)
            $input.addClass('is-invalid');
            $input.next('.invalid-tooltip').text(xhr.responseJSON.message);
        }
    });
};

$(document).on('change', '.answer-score', function () {
    const $input = $(this);

    $input.removeClass('is-valid is-invalid');

    handleSendScore(this);
});