<?php
  require_once('classes/classes.php');
  require_once('vendor/autoload.php');

  // use JasonGrimes\Paginator;

  // $totalItems = count(User::getEmployeesAndStudents());
  // $itemsPerPage = 10;
  // $currentPage = 1;

  // if ($_GET && isset($_GET['page'])) {
  //   $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
  // }

  // $offSet = $currentPage * $itemsPerPage;

  // $urlPattern = '/test.php?page=(:num)';

  // $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

  // $users = User::getEmployeesAndStudents();
 $users = User::getAllStudents();
 $current_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
  
?>
<html>
<head>
 <link rel="stylesheet" href="css/bootstrap-clockpicker.css">
</head>
<body>
<?php include('nav.php') ?>
<!-- Input group, just add class 'clockpicker', and optional data-* -->
<div class="container">
  <div id="user_list">
    <?= substr($current_page, 0, strpos($current_page, '.')) ?>
  </div>
</div>
</body>
</html>
<script>
$(document).ready(function () {
  $('.pagination li').addClass('page-item')
  $('.pagination li a').addClass('page-link').click(function (event) {
    event.preventDefault()
    $('.pagination li a').parent().removeClass('active')
    $(this).parent().addClass('active')
    let url = $(this).attr('href')
    let get = $.get(url)
    get.done(function(data) {
      $('#user_list').html($(data).find('#user_list').html())
    })
  })
})
</script>
