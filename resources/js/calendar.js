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
            const isMobile = window.innerWidth < 1024;
            this.calendar = new Calendar(calendarEl, {
                plugins: [themePlugin, dayGridPlugin, timeGridPlugin, listPlugin],
                initialView: isMobile ? 'timeGridWeek' : initialView,
                locale: idLocale,
                headerToolbar: {
                    left: isMobile ? 'prev,next' : 'prev,next today',
                    center: isMobile ? '' : 'title',
                    right: isMobile ? 'title' : 'dayGridMonth,timeGridWeek,listWeek'
                },
                dayMaxEvents: true, // allow "more" link when too many events
                events: eventsUrl,
                height: '100%',
                eventClick: function(info) {
                    if (info.event.title === 'Available') {
                        window.dispatchEvent(new CustomEvent('open-delete-slot-modal', {
                            detail: {
                                id: info.event.id,
                                start: info.event.start,
                                end: info.event.end,
                            }
                        }));
                        // Store the event instance on the window so we can remove it later
                        window._currentCalendarEvent = info.event;
                    }
                },
                windowResize: function (arg) {
                    const mobile = window.innerWidth < 1024;
                    arg.view.calendar.setOption('headerToolbar', {
                        left: isMobile ? 'prev,next' : 'prev,next today',
                        center: isMobile ? '' : 'title',
                        right: isMobile ? 'title' : 'dayGridMonth,timeGridWeek,listWeek'
                    });
                    if (mobile && arg.view.type !== 'timeGridWeek') {
                        arg.view.calendar.changeView('timeGridWeek');
                    } else if (!mobile && arg.view.type === 'timeGridWeek') {
                        arg.view.calendar.changeView(initialView);
                    }
                }
            });
            this.calendar.render();
        }
    }));
});