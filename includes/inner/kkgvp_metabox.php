<div class="row mt-3 mb-3">
    <div class="col-md-12">
        <label for="choosen_url" class="show_if_video" style="">
        <?php echo esc_html( 'Url:');?>
            <input type="radio" name="kkgvideo_chooseType" id="choosen_url" class="video_chooseType" value="url" <?php echo esc_attr( $choosen_url );?>>
        </label>
        <label for="choosen_up" class="show_if_video" style="">
        <?php echo esc_html( 'Upload:');?>
            <input type="radio" name="kkgvideo_chooseType" id="choosen_up" class="video_chooseType" value="upload" <?php echo esc_attr( $choosen_up );?>>
        </label>
    </div>
</div>
<div class="mt-3 mb-3">
    <div class="row show-hide show-url" <?php echo esc_html( $choosen_url_display);?>> 
        <div class="col-md-3">     
            <label for="choosen_url" class="show_if_video" style="">
                <?php echo esc_html( 'Url:');?>            
            </label>        
        </div>
        <div class="col-md-9">
        <input type="url" style="width:100%" id="kkgvideo_url" name="kkgvideo_url" value="<?php echo esc_attr( $videourl );?>">
        </div>
    </div>
    <div class="row show-hide show-upload" <?php echo esc_html( $choosen_up_display);?>> 
        <div class="col-md-3">     
            <label for="choosen_upload" class="show_if_video" style="">
                <?php echo esc_html( 'Upload:');?>           
            </label>        
        </div>
        <div class="col-md-9">
            <input id="kkgvideo_up_btn" type="button" class="button button-primary button-large" value="Upload Video" />
            <input id="kkgvideo_file" type="hidden" name="kkgvideo_file" value="<?php echo esc_attr( $videourl );?>" />
            <input id="kkgvideo_filename" type="hidden" name="kkgvideo_filename" value="<?php echo esc_attr( $videofilename );?>" />
        </div>
    </div>
    <?php if(esc_attr( $videourl ) != ""){?>  
    <div class="row show-video mt-3">         
        <div class="col-md-9">
        <video width="320" height="240" controls controlsList="nodownload noremoteplayback ">
        <source src="<?php echo esc_attr( $videourl );?>" type="video/mp4">
        Your browser does not support the video tag.
        </video>
        </div>
    </div>
    <?php }?> 
</div>