/*
 * Welcome to your app's main JavaScript file!
 *
 */
import '../css/app.scss';
//import(/* webpackChunkName: "H2" */ '../css/app.scss');
import $ from 'jquery';

global.$ = global.jQuery = $;
import ('popper.js');

import('bootstrap');
import('mdbootstrap');
import ('jquery-confirm');
import * as h2Button from 'h2-invent-apps';
import flatpickr from 'flatpickr'
import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import momentPlugin from '@fullcalendar/moment';
import listPlugin from '@fullcalendar/list';
import Chart from 'chart.js';
import autosize from 'autosize'
import ClipboardJS from 'clipboard'
import {initScheduling} from './scheduling'
import * as Toastr from 'toastr'
import {initFreeFields} from './freeField'
$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    }
    return decodeURI(results[1]) || 0;
}
addEventListener('load', function () {
    var url = (new URLSearchParams(window.location.search)).get('modalUrl');
    if (url !== null) {
        $('#loadContentModal').load(atob(url), function () {
            $('#loadContentModal ').modal('show');
        });
    }
});

$(document).ready(function () {

    setTimeout(function () {
        $('#snackbar').addClass('show').click(function (e) {
            $('#snackbar').removeClass('show');
        })
    }, 500);
    if (importBBB) {
        h2Button.init(bbbUrl);
    }

    $('.switchDarkmode').change(function (e) {
        var val = 0;
        if ($(this).prop('checked')) {
            val = 1
        }
        setCookie('DARK_MODE', val, 365);
        window.location.reload();
    })
    initCopytoClipboard();
    if (notificationUrl !== "") {
       $.getJSON(notificationUrl, function (data) {
            var notification = data
           for (var i = 0; i < notification.length; i++) {
                console.log(notification[i]);
                Toastr[notification[i].type](notification[i].text,notification[i].head )
                Toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
            }
        });

    }

    initDropDown();

    $('#dismiss, .overlay').on('click', function () {
        // hide sidebar
        $('#sidebar').removeClass('active');
        // hide overlay
        $('.overlay').removeClass('active');
    });

    $('#sidebarCollapse').on('click', function () {
        // open sidebar
        $('#sidebar').addClass('active');
        // fade in the overlay
        $('.overlay').addClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });

    $('.flatpickr').flatpickr({
        minDate: "today",
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

});
$(window).on('load', function () {
    $('[data-toggle="popover"]').popover({html: true});

});

$(document).on('click', '.confirmHref', function (e) {
    e.preventDefault();
    var url = $(this).prop('href');
    var text = $(this).data('text');
    if (typeof text === 'undefined') {

        text = 'Wollen Sie die Aktion durchf??hren?'
    }
    console.log(text);
    $.confirm({
        title: 'Best??tigung',
        content: text,
        theme: 'material',
        buttons: {
            confirm: {
                text: 'OK', // text for button
                btnClass: 'btn-outline-danger btn', // class for the button
                action: function () {
                    window.location.href = url;
                },


            },
            cancel: {
                text: 'Abbrechen', // text for button
                btnClass: 'btn-outline-primary btn', // class for the button
            },
        }
    });

})

$(document).on('click', '.loadContent', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    $('#loadContentModal').load(url, function (data,status) {
        if ( status === "error" ) {
            window.location.reload();
        }else {
            $('#loadContentModal ').modal('show');
        }

    });
});

function initServerFeatures() {

}

$('#loadContentModal').on('shown.bs.modal', function (e) {
    initScheduling();
    initFreeFields();
    $('[data-toggle="popover"]').popover({html: true});
    $('.flatpickr').flatpickr({
        minDate: "today",
        enableTime: true,
        time_24hr: true,
        defaultMinute: 0,
        minuteIncrement: 15,
        altFormat: 'd.m.Y H:i',
        dateFormat: 'Y-m-d H:i',
        altInput: true
    });
    $('.generateApiKey').click(function (e) {
        e.preventDefault();
        $('#enterprise_apiKey').val(Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15));
    })
    $('#jwtServer').change(function () {
        if ($('#jwtServer').prop('checked')) {
            $('#appId').collapse('show')
        } else {
            $('#appId').collapse('hide')
        }
    });

    if (typeof $('room_public') !== 'undefined') {
        if ($('#room_public').prop('checked')) {
            $('#maxParticipants').collapse('show')
        } else {
            $('#maxParticipants').collapse('hide')
        }
        if ($('#room_waitinglist').prop('checked')) {
            $('#maxWaitinglist').collapse('show')
        } else {
            $('#maxWaitinglist').collapse('hide')
        }
        if ($('#room_allowGroups').prop('checked')) {
            $('#maxGroupSize').collapse('show')
        } else {
            $('#maxGroupSize').collapse('hide')
        }
        if ($('#room_showRoomOnCalendar').prop('checked')) {
            $('#showInCalendarWhenNoSpace').collapse('show')
        } else {
            $('#showInCalendarWhenNoSpace').collapse('hide')
        }
        if ($('#room_showRoomOnJoinpage').prop('checked') || $('#room_showRoomOnCalendar').prop('checked')) {
            $('#showAfterDate').collapse('show')
        } else {
            $('#showAfterDate').collapse('hide')
        }
        $('#room_showRoomOnJoinpage, #room_showRoomOnCalendar').change(function () {
            if ($('#room_showRoomOnJoinpage').prop('checked') || $('#room_showRoomOnCalendar').prop('checked')) {
                $('#showAfterDate').collapse('show')
            } else {
                $('#showAfterDate').collapse('hide')
            }
        });

        $('#room_public').change(function () {
            if ($('#room_public').prop('checked')) {
                $('#maxParticipants').collapse('show')
            } else {
                $('#maxParticipants').collapse('hide')
            }
        })
        $('#room_waitinglist').change(function () {
            if ($('#room_waitinglist').prop('checked')) {
                $('#maxWaitinglist').collapse('show')
            } else {
                $('#maxWaitinglist').collapse('hide')
            }
        })
        $('#room_allowGroups').change(function () {
            if ($('#room_allowGroups').prop('checked')) {
                $('#maxGroupSize').collapse('show')
            } else {
                $('#maxGroupSize').collapse('hide')
            }
        })
        $('#room_showRoomOnCalendar').change(function () {
            if ($('#room_showRoomOnCalendar').prop('checked')) {
                $('#showInCalendarWhenNoSpace').collapse('show')
            } else {
                $('#showInCalendarWhenNoSpace').collapse('hide')
            }
        })
    }

    initCopytoClipboard();

    initSearchUser();
    initServerFeatures();
    var ctx = document.getElementById("lineChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });

});
$(document).on('click', '.directSend', function (e) {
    var $url = $(this).prop('href');
    var $targetUrl = $(this).data('url');
    var target = $(this).data('target');

    e.preventDefault();
    $.get($url, function () {
        $(target).closest('div').load($targetUrl + ' ' + target,function () {
            $('[data-toggle="popover"]').popover({html: true});
        });

    })
});
$(".clickable-row").click(function () {
    window.location = $(this).data("href");
});
$('#ex1-tab-3-tab').on('shown.bs.tab', function (e) {
    renderCalendar();
})

function renderCalendar() {

    var calendarEl = document.getElementById('calendar');
    var calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, bootstrapPlugin, momentPlugin, listPlugin],
        themeSystem: 'bootstrap',
        events: '/api/v1/getAllEntries',
        lang: 'de',
        timeFormat: 'H(:mm)',
        displayEventEnd: true,

    });
    calendar.render();

}

function initSearchUser() {
    autosize($('#new_member_member'));
    $('#searchUser').keyup(function (e) {
        var $ele = $(this);
        var $search = $ele.val();
        var $url = $ele.attr('href') + '?search=' + $search;
        if ($search.length > 0) {
            $.getJSON($url, function (data) {
                var $target = $('#participantUser');
                $target.empty();
                for (var i = 0; i < data.length; i++) {
                    $target.append('<a class="dropdown-item chooseParticipant addParticipants" data-val="' + data[i] + '" href="#"><i class=" text-success fas fa-plus"></i> ' + data[i] + '</a>');
                }

                $('.chooseParticipant').click(function (e) {
                    e.preventDefault();
                    var $textarea = $('#new_member_member');
                    var data = $textarea.val();
                    $textarea.val('').val($(this).data('val') + "\n" + data);
                    $('#searchUser').val('');
                    autosize.update($textarea);
                })
            })
        }

    })
}

function initDropDown() {
    $('.dropdownTabToggle').click(function (e) {
        e.preventDefault();
        var $ele = $(this);
        var $target = $ele.attr('href');
        $($target).tab('show');
        $ele.closest('.tabDropdown').find('button').text($ele.text());
        $ele.closest('.dropdown-menu').find('.active').removeClass('active');
        $ele.addClass('active');
    })


}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function initCopytoClipboard(){

    var clipboard = new ClipboardJS('.copyLink');
}
