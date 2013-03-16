G.spisok.create({
  view:$("#vk-ob"),
  url:"/vk/myOb/AjaxObSpisok.php",
  alert:0,
  values:{
    menu:0,
    type:'all'
  },
  callback:function (data) {
    myob.spisok = data;
    $(".unit").unbind();
    $(".unit").bind({
      mouseenter:function () { $(this).find("H2").show(); },
      mouseleave:function(){ $(this).find("H2").hide(); }
    });
  }
});
