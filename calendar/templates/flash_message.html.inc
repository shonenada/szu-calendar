{% if flash['flashMsg'] %}
<div class="flash-message">
    {% for msg in flash['flashMsg'] %}
    <div class="alert alert-{{ msg[1] }} alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <strong>{{ msg[0] }}</strong>
    </div>
    {% endfor %}
</div>
<script type="text/javascript">
$(function(){
  $('button.close').click(function(){
    $(this).parent().fadeOut();
  });
});
</script>
{% endif %}