<?php
require_once('../../classes/classes.php');
require('../authenticate.php');
session_start();
require_once('../../vendor/autoload.php');

use JasonGrimes\Paginator;

if (isset($_GET['search']) && isset($_GET['page']) && isset($_GET['type'])) {
    $user_search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
    $currentPage = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
    $type = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING));

    $totalItems = $type == 'active' ? count(User::search($user_search)) : count(User::search($user_search, '', '', 'inactive'));
    $itemsPerPage = 12;

    $offSet = ($currentPage - 1) * $itemsPerPage;

    $urlPattern = 'partials/user_search.php?page=(:num)&search=' . $user_search . "&type=$type";

    $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
    $paginator->setMaxPagesToShow(3);

    $users = $type == 'active' ? User::search($user_search, "limit $itemsPerPage", "offset $offSet") : User::search($user_search, "limit $itemsPerPage", "offset $offSet", 'inactive');
}

?>
<div id="search_user_list">
    <ul class="list-group">
        <?php foreach ($users as $user): ?>
            <li class="list-group-item list_item" data-id="<?= $user->getID() ?>"><?= $user->getFullName() ?></li>
        <?php endforeach ?>
        <li class="list-group-item"><?= $paginator ?></li>
    </ul>
</div>
<script>
$(document).ready(function() {
    $('.list_item').on('click', function() {
        $('.list_item').each(function(index) {
            $(this).removeClass('active');
        });
        $('.edit-list-item').each(function(index) {
            $(this).removeClass('active');
        });
        $(this).addClass("active");
        let userID = $(this).data('id');
        let get = $.get('partials/user_form.php', {
            id: userID
        });

        get.done(function(data) {
            $('#user-form-container').html(data);
        });

    });

    $('.pagination li').addClass('page-item')
    $('.pagination li a').addClass('page-link').click(function (event) {
      event.preventDefault()
      $('.pagination li a').parent().removeClass('active')
      $(this).parent().addClass('active')
      let url = $(this).attr('href')
      let get = $.get(url)
      get.done(function(data) {
        $('#user_list').html(data)
      })
    })
})
</script>