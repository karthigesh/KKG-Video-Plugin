var urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('status')) {
    setTimeout(function () {
        history.pushState('', '',
            location.href.split('&')[0]);
        location.reload();
    }, 10000); // 1 min = 1000 ms * 60 = 60000
}
jQuery(function ($) {
    $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();
        var fileExtension = ['mp3','aac','m4a','amr'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only '.mp3' formats are allowed.");
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen...");
            return false;
        } else {
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");
            }
            else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
            }
        }

    });
    $('#videoUrl').change(function () {
        let text = this.value;
        let pattern = /^.*\.(mp3|aac|m4a|amr)$/i;
        if (pattern.test(text)) {
            $('.urlsubmit').attr('disabled', false);
        } else {
            $('.urlsubmit').attr('disabled', true);
        }
    });
    $('#delete_video').click(function(){
        var id = $(this).data('id');
        var url = $(this).data('url');
        var nonce = $(this).data('nonce');
        var action = $(this).data('action');
        if (confirm("Are you sure to delete?")) {
            $.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {id: id, nonce : nonce, action: action},
                success: function(response) {
                if(response.status) {
                        window.location.href = response.url;
                }else {
                    alert("Your vote could not be added")
                }
                }
            });  
        } 
    });
    $('.show-hide').hide();
    $('.video_chooseType').click(function(){
        var nv = $(this).val();
        $('.show-hide').hide();
        $('.show-'+nv).show();
        if(nv == 'url'){
            $('#kkgvideo_url').attr('required',true);
        }else{
            $('#kkgvideo_url').removeAttr('required');                       
        }
    });

    if($('#choosen_url').is(':checked')){
        $('.show-url').show();
    }
    if($('#choosen_up').is(':checked')){
        $('.show-upload').show();
    }
    var custom_uploader;
    $('#kkgvideo_up_btn').click(function(){
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose video',
            button: {
                text: 'Choose video'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            if(attachment.type === "video"){
                $('#kkgvideo_file').val(attachment.url);
                $('#kkgvideo_filename').val(attachment.filename);
            }else{
                alert('wrong file');                
                $('#kkgvideo_file').val("");
                $('#kkgvideo_filename').val("");
            }
        });

        //Open the uploader dialog
        custom_uploader.open();
    });

});