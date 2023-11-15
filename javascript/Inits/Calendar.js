/* initialize the external events
     -----------------------------------------------------------------*/
function ini_events(ele) {
    ele.each(function () {

        // create an Event Object (https://fullcalendar.io/docs/event-object)
        // it doesn't need to have a start or end
        const eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject)

        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex        : 1070,
            revert        : true, // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        })

    })
}

let lastData = null;

window.libs['calendar'] = function () {

    ini_events($('#external-events div.external-event'))

    let date = new Date()
    let d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear();
    const Calendar = FullCalendar.Calendar;
    const checkbox = document.getElementById('drop-remove');

    const calendar = new Calendar(this.target, {
        //initialView: 'dayGridMonth',
        selectable: true,
        headerToolbar: {
            left  : 'prev,next today',
            center: 'title',
            right : 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        themeSystem: 'bootstrap',
        //Random default events
        events: {
            url: window.calendar_data,
            method: 'POST',
            extraParams: function() {
                return {
                    _token: exec('token')
                };
            }
        },
        select: async function (start, end, allDay) {
            const { value: formValues } = await Swal.fire({
                title: 'Add event',
                html: '<div class="form-group"><div class="input-group>"><input id="swalEvtTitle" class="form-control" placeholder="Enter title"/></div></div>' +
                    '<div class="form-group"><div class="input-group"><textarea id="swalEvtDesc" class="form-control" placeholder="Enter description"></textarea></div></div>' +
                    '<div class="form-group"><div class="input-group"><input id="swalEvtURL" class="form-control" placeholder="Enter url"/></div></div>',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        title: document.getElementById('swalEvtTitle').value,
                        desc: document.getElementById('swalEvtDesc').value,
                        url: document.getElementById('swalEvtURL').value,
                    };
                }
            });

            if (formValues) {
                NProgress.start();
                axios.post(window.calendar_event, {
                    title: formValues.title,
                    description: formValues.desc,
                    url: formValues.url,
                    start: start.startStr,
                    end: start.startStr,
                    _token: exec('token')
                }).then(d => {
                    if (d.data.id) {
                        Swal.fire('Event added successfully!', '', 'success');
                        calendar.refetchEvents();
                    } else {
                        Swal.fire('Error', '', 'error');
                    }
                }).finally(d => {
                    NProgress.done();
                })
            }
        },
        eventClick: function (info) {
            info.jsEvent.preventDefault();

            Swal.fire({
                title: info.event.title,
                icon: 'info',
                html: '<p>' + info.event.extendedProps.description + '</p>' +
                    (info.event.url && info.event.url !== 'null' ? '<a href="' + info.event.url + '">Visit event page</a>' : ''),
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'Close',
                confirmButtonText: 'Delete event',
            }).then(r => {
                if (r.isConfirmed) {
                    NProgress.start();
                    axios.delete(window.drop_event, {params: {
                        _token: exec('token'),
                        id: info.event.toJSON().id,
                    }}).then(d => {
                        calendar.refetchEvents();
                    }).finally(d => {
                        NProgress.done();
                    })
                }
            });
        },
        editable  : true,
        droppable : false,
        eventDrop: function(info) {
            NProgress.start();
            axios.post(window.calendar_event, {
                id: info.event.toJSON().id,
                start: info.event.toJSON().start,
                end: info.event.toJSON().end,
                _token: exec('token')
            }).then(d => {
                if (d.data.id) {
                    calendar.refetchEvents();
                } else {
                    Swal.fire('Error', '', 'error');
                }
            }).finally(d => {
                NProgress.done();
            })
        },
        eventResize: function(info) {
            NProgress.start();
            axios.post(window.calendar_event, {
                id: info.event.toJSON().id,
                start: info.event.toJSON().start,
                end: info.event.toJSON().end,
                _token: exec('token')
            }).then(d => {
                if (d.data.id) {
                    calendar.refetchEvents();
                } else {
                    Swal.fire('Error', '', 'error');
                }
            }).finally(d => {
                NProgress.done();
            })
        },
        drop: function(info) {
            if (checkbox.checked) {
                info.draggedEl.querySelector('[data-click="calendar::events_template_delete"]').click();
                $(checkbox).prop('checked', false);
            }
            NProgress.start();
            axios.post(window.calendar_event, {
                title: lastData.title,
                backgroundColor: lastData.backgroundColor,
                borderColor: lastData.borderColor,
                textColor: lastData.textColor,
                start: info.dateStr,
                _token: exec('token')
            }).finally(d => {
                NProgress.done();
            });
        }
    });

    calendar.render();
};
