{% if flash['flashMsg'] %}
<div class="flash-message flash-message-fixed with-transition">
    {% for msg in flash['flashMsg'] %}
    <div class="alert alert-{{ msg[1] }}" role="alert">{{ msg[0] }}</div>
    {% endfor %}
</div>
<script type="text/javascript">
$(function(){
  $('button.close').click(function(){
    $(this).parent().fadeOut();
  });
  $('.flash-message .alert').each(function(){
    var obj = $(this);
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
  });
});
</script>
{% endif %}