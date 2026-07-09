import { Calendar } from 'fullcalendar';
import themePlugin from 'fullcalendar/themes/monarch'; // YOUR THEME
import dayGridPlugin from 'fullcalendar/daygrid';
import timeGridPlugin from 'fullcalendar/timegrid';
import listPlugin from 'fullcalendar/list';
import idLocale from 'fullcalendar/locales/id';

// stylesheets
import 'fullcalendar/skeleton.css'; // ALWAYS NEED SKELETON
import 'fullcalendar/themes/monarch/theme.css'; // YOUR THEME
import '../css/calendar-palette.css'

document.addEventListener('alpine:init', () => {
    window.Alpine.data('calendarWidget', (eventsUrl, initialView) => ({
        calendar: null,
        initCalendar() {
            let calendarEl = this.$refs.calendarEl;
            this.calendar = new Calendar(calendarEl, {
                plugins: [themePlugin, dayGridPlugin, timeGridPlugin, listPlugin],
                initialView: initialView,
                locale: idLocale,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                dayMaxEvents: true, // allow "more" link when too many events
                events: eventsUrl
            });
            this.calendar.render();
        }
    }));
});