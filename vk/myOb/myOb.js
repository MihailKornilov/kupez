$("#links").infoLink({
  spisok:[
    {uid:0,title:'Все объявления'},
    {uid:1,title:'Активные'},
    {uid:2,title:'Архив'}],
   func:function (uid) { G.spisok.print({menu:uid}); }
});



G.spisok.create({
  view:$("#vk-ob"),
  url:"/vk/myOb/AjaxObSpisok.php",
  nofind:"Объявлений не найдено.",
  values:{
    menu:0,
    type:'local'
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


