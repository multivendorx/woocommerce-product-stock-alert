jQuery(document).ready(function($){
  var _custom_media = true,
  _orig_send_attachment = wp.media.editor.send.attachment;

  $('.dc-wp-fields-uploader .upload_button').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var mime = $(this).data('mime');
    var id = button.attr('id').replace('_button', '');
    _custom_media = true;
    wp.media.editor.send.attachment = function(props, attachment) {
      console.log(JSON.stringify(props) +":"+ JSON.stringify(attachment));
      if ( _custom_media ) {
        if(mime  == 'image') {
          $("#"+id+'_display').attr('src', attachment.url).removeClass('placeHolder').show();
          if($("#"+id+'_preview').length > 0)
            $("#"+id+'_preview').attr('src', attachment.url);
        } else {
          $("#"+id+'_display').attr('href', attachment.url);
          if(attachment.icon) $("#"+id+'_display span').css('background', 'url("'+attachment.icon+'")').css('width', '48px').css('height', '64px');
        }
        $("#"+id).val(attachment.url);
        $("#"+id).hide();
        button.hide();
        $("#"+id+'_remove_button').show();
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });
  
  $('.dc-wp-fields-uploader .remove_button').each(function() {
    var button = $(this);
    var mime = $(this).data('mime');
    var id = button.attr('id').replace('_remove_button', '');
    if(mime == 'image')
      var attachment_url = $("#"+id+'_display').attr('src');
    else
      var attachment_url = $("#"+id+'_display').attr('href');
    if(attachment_url.length == 0) button.hide();
    else $("#"+id+'_button').hide();
    button.click(function(e) {
      id = $(this).attr('id').replace('_remove_button', '');
      if(mime == 'image') {
        $("#"+id+'_display').attr('src', '').addClass('placeHolder').hide();
        $("#"+id+'_preview').attr('src', '');
      } else {
        $("#"+id+'_display').attr('href', '#');
      }
      $("#"+id).val('');
      $(this).hide();
      $("#"+id+'_button').show();
      return false;
    });
  });

  $('.add_media').on('click', function(){
    _custom_media = false;
  });
});