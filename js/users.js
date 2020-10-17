/**
 * Provides JS functionality for users.php
 * Author: Steph Mireault
 */

$(document).ready(function() {
    $('.list_item').on('click', function() {
        $('#spinner').show()

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
            $('#spinner').hide()
            $('#user-form-container').html(data);
        });

    });

    $('#edit-staff-positions').click(function() {
        $('#spinner').show()

        let url = 'partials/staff_positions.php';
        let get = $.get(url);

        get.done(function(data) {
            $('#spinner').hide()
            $('#user-form-container').html(data);
        });
    });


    $('#edit-absent-reasons').click(function() {
        $('#spinner').show()

        let url = 'partials/absent_reasons.php';
        let get = $.get(url);

        get.done(function(data) {
            $('#spinner').hide()
            $('#user-form-container').html(data);
        });
    });

    $('#user_search').submit(function(event) {
        $('#spinner').show()
        event.preventDefault()

        let get = $.get('partials/user_search.php', {
            search: $('#search').val(),
            page: 1,
            type: $('#type').val()
        })

        get.done(function(data) {
            $('#spinner').hide()
            $('#user_list').html(data);
        })
    })

    $('#link_users_help').click(function() {
        $('#modal_user_help').modal('show')
    })

    // Anchor links for help modal.
    $('.section_links').click(function () {
        let section = $(this).data('section')
        window.location.hash = section;
    })

    $('.pagination li').addClass('page-item')
    $('.pagination li a').addClass('page-link').click(function (event) {
      event.preventDefault()
      $('#spinner').show()
      $('.pagination li a').parent().removeClass('active')
      $(this).parent().addClass('active')
      let url = $(this).attr('href')
      let get = $.get(url)
      get.done(function(data) {
        $('#spinner').hide()
        $('#user_list').html($(data).find('#user_list').html())
      })
    })
});
