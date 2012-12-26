</DIV>

<SCRIPT type="text/javascript">
VK.init(frameBodyHeightSet);
VK.callMethod("setLocation","<?php echo $_GET['my_page'].(isset($_GET['id']) ? '_'.$_GET['id'] : '' ); ?>");
VK.callMethod('scrollSubscribe');
VK.addCallback('onScroll',function(top){ vkScroll = top; });
</SCRIPT>

</BODY></HTML>
