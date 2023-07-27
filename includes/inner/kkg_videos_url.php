<div class='wrap'>
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome to KKG Video App!</h1>
        </div>
    </div>    
    <form method = 'POST'>
      <input type = 'hidden' name = 'action' value = 'kkg_videos_save'>
      <?php wp_nonce_field();?>
      <input type = 'hidden' name = 'redirectToUrl' value = ''>  
      <div class = 'row'>
        <div class = 'col-md-4'>          
          <p>Title</p>
        </div>
        <div class = 'col-md-8'>
          <?php echo $GLOBALS['formvideoContent'];?>
        </div>
      </div><!-- / row -->    
      <div class = 'row mt-3'>
        <div class = 'col-md-4'>          
          <p><?php echo $GLOBALS['formvideoTitle'];?></p>
        </div>
        <div class = 'col-md-8'>
          <?php echo $GLOBALS['formvideoUrl'];?>
        </div>
      </div><!-- / row -->
      <input type = 'submit' name = 'Submit' class = 'btn btn-primary urlsubmit'>
  </form>
</div><!-- / wrap -->

