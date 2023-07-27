<?php
//require_once plugin_dir_path( 'kkgvp-functions.php' );
?>
<div class = 'wrap'>
<h1>Welcome to KKG Video App!</h1>
<?php
if ( isset( $_GET[ 'success' ] ) ) {
    ?>
    <div class = 'alert alert-success' role = 'alert'>
    The Video File has been saved successfully!
    </div>
    <?php
}
?>
<form method = 'POST' enctype = 'multipart/form-data'>
<input type = 'hidden' name = 'action' value = 'kkg_videos_upload'>
<?php wp_nonce_field();
?>
<input type = 'hidden' name = 'redirectToUrl' value = ''>
<div class = 'row'>
<div class = 'col-md-4'>
<p>Title</p>
</div>
<div class = 'col-md-8'>
<?php echo $GLOBALS[ 'formvideoContent' ];
?>
</div>
</div><!-- / row -->
<div class = 'row mt-2'>
<div class = 'col-md-4'>
<p>Upload The Video File</p>
</div>
<?php
if(isset($GLOBALS[ 'formvideoUrl' ]) && $GLOBALS[ 'formvideoUrl' ] != ""){
  $path = parse_url($GLOBALS[ 'formvideoUrl' ], PHP_URL_PATH);
  $pathFragments = explode('/', $path);
  $novfile = end($pathFragments);
  $required = '';
}else{
  $novfile = 'No file chosen...';
  $required = 'required';
}

?>
<div class = 'col-md-8'>
<div class = 'file-upload'>
<div class = 'file-select'>
<div class = 'file-select-button' id = 'vfileName'>Choose File</div>
<div class = 'file-select-name' id = 'novFile'><?php echo $novfile;?></div>
<input type = 'file' name = 'choosevideoFile' id = 'choosevideoFile' accept = 'video/*' <?php echo $required;?>>
</div>
</div>
</div>
</div><!-- / row -->
<input type = 'submit' name = 'Submit' class = 'btn btn-primary'>
</form>