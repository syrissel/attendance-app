$(document).ready(function () {
    $('.pagination li').addClass('page-item')
    $('.pagination li a').addClass('page-link').click(function () {
        $('#spinner').show()
    })
})