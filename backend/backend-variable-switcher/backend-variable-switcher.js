jQuery(document).ready(function($){
        /*disabled color field on color taxonony*/
        $(" table tbody tr td #color_tax").prop('disabled', true);
      
        /*disabled color field on color taxonony*/

        /*variation image upload sesction */

        jQuery(document).ready( function($){
            function ct_media_upload(button_class) {
                var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;
                $('body').on('click', button_class, function(e) {
                  var button_id = '#'+$(this).attr('id');
                  var send_attachment_bkp = wp.media.editor.send.attachment;
                  var button = $(button_id);
                  _custom_media = true;
                  wp.media.editor.send.attachment = function(props, attachment){
                    if ( _custom_media ) {
                      $('#webcfwc_tax_image_id').val(attachment.id);
                      $('#webcfwc_image_wrapper').html('<span class="variation_img_icon">X</span> <img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                      $('#webcfwc_image_wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                    } else {
                      return _orig_send_attachment.apply( button_id, [props, attachment] );
                    }
                    }
                wp.media.editor.open(button);
                return false;
              });
            }
            ct_media_upload('.webcfwc_tax_media_button.button'); 
            $('body').on('click','.variation_img_icon',function(){
              $('#webcfwc_tax_image_id').val('');
              $('#webcfwc_image_wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
            });
  
        });
        /*variation image upload sesction end*/
});