<?php
if(SA == $_GET['viewer_id']) {
  echo "<DIV id=admin><A href=''>Admin</A> :: <A id=script_style>Стили и скрипты (".$G->script_style.")</A> :: ".getTime($T)."</DIV>";
  echo "<SCRIPT type='text/javascript'>$('#script_style').click(function () { $.getJSON('/superadmin/AjaxScriptStyleUp.php?' + G.values, function () { location.reload(); }); });</SCRIPT>";
}
?>

</DIV>

<SCRIPT type="text/javascript">
VK.init(frameBodyHeightSet);
VK.callMethod("setLocation","<?php echo $_GET['my_page'].(isset($_GET['id']) ? '_'.$_GET['id'] : '' ); ?>");
VK.callMethod('scrollSubscribe');
VK.addCallback('onScroll',function(top){ vkScroll = top; });
</SCRIPT>

</BODY></HTML>
