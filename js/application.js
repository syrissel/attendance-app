$(document).ready(function() {
    $('#btn_sidebar').click(function() {
        if ($('.sidebar-wrapper').width() === 0){
            document.getElementsByClassName("sidebar-wrapper")[0].style.width = "300px";
            $('#menu_icon').html('<i class="fa fa-times" aria-hidden="true"></i>');
            // document.getElementsByTagName('body')[0].style.marginLeft = "300px";
            // document.getElementsByClassName("container")[0].style.marginLeft = "300px";
          } else {
            document.getElementsByClassName("sidebar-wrapper")[0].style.width = 0;
            $('#menu_icon').html('<i class="fa fa-bars" aria-hidden="true"></i>');
            // document.getElementsByTagName('body')[0].style.marginLeft = 0;
            // document.getElementsByClassName("container")[0].style.marginLeft = 0;
          }
    })
})