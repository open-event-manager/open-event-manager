//import(/* webpackChunkName: "H2" */ '../css/app.scss');
import $ from 'jquery';

global.$ = global.jQuery = $;

import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import deLocale from '@fullcalendar/core/locales/de';
import bootstrapPlugin from '@fullcalendar/bootstrap';

function initCalendar() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl && typeof events !== 'undefined') {
        var calendar = new Calendar(calendarEl, {
            plugins: [bootstrapPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
            locale: deLocale,
            themeSystem: 'bootstrap',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            dayMaxEvents: true, // allow "more" link when too many events
            events: events,
            eventMouseEnter: function(event, jsEvent, view) {
                var props = event.event._def.extendedProps;
                $(event.el).closest('.fc-daygrid-event').append('<div id=\"'+event.id+'\" class=\"hover-end\">'+'<b>'+event.event._def.title+'</b><p>'+props.start+'<br>'+props.end+'<br>'+props.freeSpace+'</p></div>');
            },

            eventMouseLeave: function(event, jsEvent, view) {
                 $('#'+event.id).remove();
            }
        });

        calendar.render();

    }
}

export {initCalendar}