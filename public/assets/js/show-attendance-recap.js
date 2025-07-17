function handleDetailMeeting(id) {
    if (!id) return;

    const button = $(event.target);
    const originalHtml = button.html();
    button
        .prop("disabled", true)
        .html('<i class="fa-solid fa-arrows-rotate fa-spin"></i>');

    $.ajax({
        url: `/kehadiran/pertemuan/${id}`,
        method: "GET",
        success: function (res) {
            $("#detailMeetingModal").modal("show");
            if (res.success && res.data) {
                $("#detailMeetingModal #change_attendance").attr(
                    "href",
                    `/kehadiran/${res.data.schedule.id}/${res.data.id}`
                );

                $("#detailMeetingModal #subject").html(
                    res.data.schedule.subject.name
                );
                $("#detailMeetingModal #class").html(
                    (res.data.schedule.class.major
                        ? res.data.schedule.class.major.name + " - "
                        : "") +
                        res.data.schedule.class.name +
                        " - " +
                        res.data.schedule.class.level
                );
                $("#detailMeetingModal #meeting").html(res.data.meeting_at);
                let meeting_method;
                switch (res.data.meeting_method) {
                    case "Online":
                        meeting_method = "Daring";
                        break;
                    case "Offline":
                        meeting_method = "Luring";
                        break;
                    default:
                        meeting_method = "Campuran";
                }
                $("#detailMeetingModal #meeting_method").html(meeting_method);
                $("#detailMeetingModal #teacher").html(
                    res.data.schedule.teacher.name
                );
                $("#detailMeetingModal #date").html(res.data.formatted_date);
                $("#detailMeetingModal #start_time").html(
                    res.data.schedule.start_time
                );
                $("#detailMeetingModal #end_time").html(
                    res.data.schedule.end_time
                );

                $("#detailMeetingModal #status").html(res.data.status);

                // kehadiran pertemuan
                $("#detailMeetingModal #started_at").html(
                    res.data.formatted_started_at
                );
                $("#detailMeetingModal #total_attendance").html(
                    res.data.attendances_count
                );
                $("#detailMeetingModal #total_user").html(
                    res.data.schedule.class.students_count
                );
                $("#detailMeetingModal #teacher").html(
                    res.data.schedule.teacher.name
                );
                $("#detailMeetingModal #teacher-image").attr(
                    "src",
                    res.data.schedule.teacher.user.image
                        ? `/storage/${res.data.schedule.teacher.user.image}`
                        : "/assets/svg/user-placeholder.svg"
                );
            }
        },
        error: function (xhr) {
            const toast = new bootstrap.Toast($("#toast-error"));
            $("#toast-error #toast-text").text(
                xhr.responseJSON?.message || "Gagal mengambil detail siswa"
            );
            toast.show();
        },
        complete: function () {
            button.prop("disabled", false).html(originalHtml);
        },
    });
}
