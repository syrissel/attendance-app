import 'jquery';
import _ from 'lodash';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import interactionPlugin from '@fullcalendar/interaction';
import 'popper.js';
import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import '../js/clock-widget';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin, bootstrapPlugin, interactionPlugin ],
        themeSystem: 'bootstrap',
        events: '../event_feed.php',
        dateClick: function(info) {
            
        },
        eventClick: function(info) {
            let url = '../admin/edit_attendance.php';
            let id = info.event.extendedProps.attendance_id
            let get = $.get(url, {
                id: id
            })
            get.done(function() {
                let completeUrl = url + '?id=' + id + '&calendar=true';
                window.open(completeUrl, "_blank");
            })
        },
        eventDidMount: function(info) {
            $(info.el).tooltip({
                title: info.event.extendedProps.description,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            })
        }
    });

    calendar.render();

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

    $('#reports_group').hide()
    $('#management_group').hide()

    $('#btn_reports').click(function() {
        $('#reports_group').slideToggle()
        let font = $(this).find('i')
        toggleArrow(font)
    })

    $('#btn_management').click(function () {
        $('#management_group').slideToggle()
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
});