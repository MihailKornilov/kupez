$("#links").infoLink({
  spisok:[
    {uid:0,title:'��� ����������'},
    {uid:1,title:'��������'},
    {uid:2,title:'�����'}],
   func:function (uid) { G.spisok.print({menu:uid}); }
});



G.spisok.create({
  view:$("#vk-ob"),
  url:"/vk/myOb/AjaxObSpisok.php",
  nofind:"���������� �� �������.",
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


