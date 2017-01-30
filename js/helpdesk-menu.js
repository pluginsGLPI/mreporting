$( document ).ready(function() {
  if (location.pathname.indexOf('plugins') > 0) {
      urlAjax = "../../mreporting/ajax/homepage_link.php";
   } else {
      urlAjax = "../plugins/mreporting/ajax/homepage_link.php";
   }
   
   $.post( urlAjax, function( data ) {
      $('#c_menu #menu').append( data );
   });
});
