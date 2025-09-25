import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr'; // <-- import de la locale française

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ dayGridPlugin, interactionPlugin ], // plugins globaux importés via CDN
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        editable: true,
        selectable: true,
        locale: 'fr',
        events: [
            { title: 'Entraînement', start: '2025-09-24', color: '#0F9DE8' },
            { title: 'Compétition', start: '2025-09-26', end: '2025-09-27', color: '#8b5e3c' },
            { title: 'Fête du club', start: '2025-09-29', color: '#28a745' }
        ],
    
        dateClick: function(info) {
            var title = prompt("Nom de l'événement :");
            if (title) {
                calendar.addEvent({
                    title: title,
                    start: info.dateStr,
                    allDay: true,
                    color: '#0F9DE8'
                });
            }
        }
    });
    
    calendar.render();
});