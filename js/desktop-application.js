/**
 * JS only used on the desktop version of the app.
 */
$(document).ready(function () {
  let href = window.location.href

  if (!(href.indexOf("daily") > -1) && !(href.indexOf("weekly") > -1) && !(href.indexOf("attendance_report") > -1) && !(href.indexOf("guest_report") > -1) && !(href.indexOf("student_report") > -1) && !(href.indexOf("employee") > -1)) {
    $('#reports_group').hide()
  } else {
    let font = $('#reports_group').parent().prev().find('i')
    toggleArrow(font)
    href.indexOf("employee_biweekly") > -1 ? $('#employee_biweekly_list_item').addClass('active') : $('#employee_biweekly_list_item').removeClass('active')
  }
  
  if (!(href.indexOf("users") > -1) && !(href.indexOf("guests") > -1) && !(href.indexOf("students") > -1) && !(href.indexOf("register") > -1)) {
    $('#management_group').hide()
  } else {
    let font = $('#management_group').parent().prev().find('i')
    toggleArrow(font)
    href.indexOf("users") > -1 ? $('#users_list_item').addClass('active') : $('#users_list_item').removeClass('active')
    href.indexOf("guests") > -1 ? $('#guests_list_item').addClass('active') : $('#guests_list_item').removeClass('active')
    href.indexOf("students") > -1 ? $('#students_list_item').addClass('active') : $('#students_list_item').removeClass('active')
    href.indexOf("register") > -1 ? $('#register_list_item').addClass('active') : $('#register_list_item').removeClass('active')
  }
  
  $('#btn_management').click(function () {
      $('#management_group').slideToggle()
      let font = $(this).find('i')
      toggleArrow(font)
  })
  
  $('#btn_reports').click(function() {
    $('#reports_group').slideToggle()
    let font = $(this).find('i')
    toggleArrow(font)
  })

  function toggleArrow(arrow) {
    if (arrow.hasClass('fa-chevron-down')) {
      arrow.removeClass('fa-chevron-down')
      arrow.addClass('fa-chevron-up')
    } else {
      arrow.removeClass('fa-chevron-up')
      arrow.addClass('fa-chevron-down')
    }
  }
})
