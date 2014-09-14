<div class="flash-message flash-message-fixed with-transition">
{% if flash['flashMsg'] %}
  {% for msg in flash['flashMsg'] %}
  <div class="alert alert-{{ msg[1] }}" role="alert">{{ msg[0] }}</div>
  {% endfor %}
{% endif %}
</div>
<script type="text/javascript">
$(function(){
  if (typeof $G.flash == 'undefined') {
    $G.flash = {};
    $G.flash.flash = function(msg, type){
      if (typeof type == 'undefined') {
        type = 'info';
      }
      var div = $('<div role="alert"></div>');
      div.hide();
      div.addClass('alert');
      div.addClass('alert-' + type);
      div.append(msg);
      $(".flash-message").append(div);
      div.fadeIn();
      $G.flash.autoFadeOut(div);
    }
    $G.flash.autoFadeOut = function (obj) {
      obj.click(function() {
          $(this).fadeOut();
      })
      var timer = setTimeout(function(){
          obj.fadeOut();
      }, 3000);
      obj.mouseover(function(){
          obj.addClass('flash-message-fixed-mouseovered');
          clearTimeout(timer);
      });
    }
  }
  $('button.close').click(function(){
    $(this).parent().fadeOut();
  });
  $('.flash-message .alert').each(function(){
    var obj = $(this);
    $G.flash.autoFadeOut(obj);
  });
});
</script>
