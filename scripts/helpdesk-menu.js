Ext.onReady(function() {
   if (location.pathname.indexOf('plugins') > 0) {
      urlAjax = "../../mreporting/ajax/homepage_link.php";
   } else {
      urlAjax = "../plugins/mreporting/ajax/homepage_link.php";
   }
   Ext.Ajax.request({
      url: urlAjax,
      success: function(data) {
         Ext.select('#c_menu #menu1')
               .insertHtml('afterEnd', data.responseText);
      }
   });
});
