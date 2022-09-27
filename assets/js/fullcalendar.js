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
            events: events
        });

        calendar.render();

    }
}

export {initCalendar}