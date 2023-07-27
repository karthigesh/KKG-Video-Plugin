var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('status')) {
    // setTimeout(function () {
    //     history.pushState('', '',
    //         location.href.split('&')[0]);
    //     location.reload();
    // }, 10000); // 1 min = 1000 ms * 60 = 60000
}
jQuery(function ($) {
    $('#choosevideoFile').bind('change', function () {
        var filename = $("#choosevideoFile").val();
        console.log(filename);
        var fileExtension = ['mp4','mov','avi'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only '.mp4','.mov','.avi' formats are allowed.");
            $(".file-upload").removeClass('active');
            $("#novFile").text("No file chosen...");
            return false;
        } else {
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#novFile").text("No file chosen...");
            }
            else {
                $(".file-upload").addClass('active');
                $("#novFile").text(filename.replace("C:\\fakepath\\", ""));
            }
        }

    });
    $('#videoUrl').change(function () {
        let text = this.value;
        let pattern = /^.*\.(mp4|mov|avi)$/i;
        if (pattern.test(text)) {
            $('.urlsubmit').attr('disabled', false);
        } else {
            alert("Only '.mp4','.mov','.avi' formats are allowed.");
            $('.urlsubmit').attr('disabled', true);
        }
    });
});