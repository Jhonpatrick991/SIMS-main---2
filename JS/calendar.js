let now = new Date();
let today = {
    day: now.getDate(),
    month: now.getMonth(),
    year: now.getFullYear()
};

let selectedDate = {...today};

// Maximum future year (3 years from now)
const maxYear = today.year + 3;

// Event storage structure
let events = JSON.parse(localStorage.getItem('calendarEvents')) || {};

// Event categories count
let categoryStats = {
    exams: 0,
    meetings: 0,
    classes: 0,
    other: 0
};

function formatDateForDisplay(dateStr) {
    const date = new Date(dateStr);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Auto-expire past events and update statistics
function cleanupExpiredEvents() {
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    let totalSchedules = 0;
    let thisMonthSchedules = 0;
    let upcomingSchedules = 0;
    
    // Reset category counts
    categoryStats = {
        exams: 0,
        meetings: 0,
        classes: 0,
        other: 0
    };
    
    // Go through all events
    for (const dateStr in events) {
        const eventDate = new Date(dateStr);
        
        // Remove events from past dates
        if (eventDate < now) {
            delete events[dateStr];
            continue;
        }
        
        // Count this event
        totalSchedules++;
        
        // Check if event is in current month
        if (eventDate.getMonth() === now.getMonth() && 
            eventDate.getFullYear() === now.getFullYear()) {
            thisMonthSchedules++;
        }
        
        // Count upcoming events (next 7 days)
        const sevenDaysLater = new Date(now);
        sevenDaysLater.setDate(sevenDaysLater.getDate() + 7);
        if (eventDate <= sevenDaysLater) {
            upcomingSchedules++;
        }
        
        // Count by category
        const category = events[dateStr].category || 'other';
        categoryStats[category]++;
    }
    
    // Update statistics in UI
    updateStatistics(totalSchedules, thisMonthSchedules, upcomingSchedules);
    
    // Save cleaned events
    localStorage.setItem('calendarEvents', JSON.stringify(events));
}

// Update statistics UI
function updateStatistics(total, monthly, upcoming) {
    const totalElement = document.getElementById('totalSchedules');
    const monthlyElement = document.getElementById('monthSchedules');
    const upcomingElement = document.getElementById('upcomingSchedules');
    
    if (totalElement) totalElement.textContent = total;
    if (monthlyElement) monthlyElement.textContent = monthly;
    if (upcomingElement) upcomingElement.textContent = upcoming;
    
    // Update category counts
    document.getElementById('examsCount').textContent = categoryStats.exams;
    document.getElementById('meetingsCount').textContent = categoryStats.meetings;
    document.getElementById('classesCount').textContent = categoryStats.classes;
    document.getElementById('otherCount').textContent = categoryStats.other;
}

// Initialize modal elements after DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const eventModal = document.getElementById('eventModal');
    const dateSelectorModal = document.getElementById('dateSelectorModal');
    const eventTitle = document.getElementById('eventTitle');
    const eventCategory = document.getElementById('eventCategory');
    const eventTime = document.getElementById('eventTime');
    const eventDescription = document.getElementById('eventDescription');
    const saveEventBtn = document.getElementById('saveEventBtn');
    const deleteEventBtn = document.getElementById('deleteEventBtn');
    const cancelEventBtn = document.getElementById('cancelEventBtn');
    const closeEventBtn = eventModal.querySelector('.close');
    const closeDateBtn = dateSelectorModal.querySelector('.close');
    let currentDateStr = '';

    // Year select elements
    const yearSelect = document.getElementById('yearSelect');
    const modalYearSelect = document.getElementById('modalYearSelect');
    
    // Cleanup expired events and update statistics
    cleanupExpiredEvents();
    
    // Populate year select dropdowns (current year to max year)
    for (let year = today.year; year <= maxYear; year++) {
        let option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
        
        let modalOption = document.createElement('option');
        modalOption.value = year;
        modalOption.textContent = year;
        modalYearSelect.appendChild(modalOption);
    }
    
    // Set default month and year in selects
    document.getElementById('monthSelect').value = today.month;
    yearSelect.value = today.year;
    document.getElementById('modalMonthSelect').value = today.month;
    modalYearSelect.value = today.year;
    
    // Add event listeners to month and year selects
    document.getElementById('monthSelect').addEventListener('change', function() {
        selectedDate.month = parseInt(this.value);
        renderMonthCalendar();
    });
    
    yearSelect.addEventListener('change', function() {
        selectedDate.year = parseInt(this.value);
        renderMonthCalendar();
    });
    
    document.getElementById('modalMonthSelect').addEventListener('change', function() {
        renderModalCalendar(parseInt(this.value), parseInt(modalYearSelect.value));
    });
    
    modalYearSelect.addEventListener('change', function() {
        renderModalCalendar(parseInt(document.getElementById('modalMonthSelect').value), parseInt(this.value));
    });

    // Function to show date selector modal - changed to directly open event modal
    window.showDateSelector = function(dateStr) {
        const selectedDate = new Date(dateStr);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Check if date is in the past
        if (selectedDate < today) {
            showToast("Cannot create schedule for past dates", true);
            return;
        }
        
        // Check if the date is beyond the max year
        const maxDate = new Date(maxYear, 11, 31);
        if (selectedDate > maxDate) {
            showToast(`Cannot create schedule beyond ${maxYear}`, true);
            return;
        }
        
        // Open event modal directly
        openModal(dateStr);
    };
    
    // Function to render calendar in date selector modal
    function renderModalCalendar(month, year) {
        const modalCalendarBody = document.getElementById('modalCalendarBody');
        modalCalendarBody.innerHTML = '';
        
        const date = new Date(year, month);
        const firstDay = (new Date(year, month, 1).getDay() + 6) % 7; // Monday = 0
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        let day = 1, nextMonthDay = 1;
        
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');
                const cellIndex = i * 7 + j;
                
                if (cellIndex < firstDay) {
                    cell.textContent = daysInPrevMonth - firstDay + j + 1;
                    cell.className = 'other-month';
                } else if (day > daysInMonth) {
                    cell.textContent = nextMonthDay++;
                    cell.className = 'other-month';
                } else {
                    cell.textContent = day;
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const date = new Date(year, month, day);
                    
                    // Add past class if date is in the past
                    if (date < new Date(today.year, today.month, today.day)) {
                        cell.classList.add('past');
                    }

                    // Add event marker if there's an event on this date
                    if (events[dateStr]) {
                        cell.classList.add('has-event');
                        const marker = document.createElement('div');
                        marker.className = 'event-marker';
                        cell.appendChild(marker);
                    }
                    
                    // Add data attribute and click event for selecting date
                    cell.setAttribute('data-date', dateStr);
                    cell.addEventListener('click', function() {
                        // Check if date is in the past or beyond max year
                        if (date < new Date(today.year, today.month, today.day)) {
                            showToast("Cannot create schedule for past dates", true);
                            return;
                        }
                        
                        if (year > maxYear) {
                            showToast(`Cannot create schedule beyond ${maxYear}`, true);
                            return;
                        }
                        
                        // Close the date selector modal
                        dateSelectorModal.style.display = 'none';
                        
                        // Open the event modal for the selected date
                        openModal(dateStr);
                    });
                    
                    day++;
                }
                
                row.appendChild(cell);
            }
            modalCalendarBody.appendChild(row);
            if (day > daysInMonth) break;
        }
    }

    // Open modal with date
    window.openModal = function(dateStr) {
        currentDateStr = dateStr;
        
        // Reset form
        eventTitle.value = '';
        eventCategory.value = 'other';
        eventTime.value = '';
        eventDescription.value = '';
        
        // If event exists for this date, populate the form
        if (events[dateStr]) {
            eventTitle.value = events[dateStr].title || '';
            eventCategory.value = events[dateStr].category || 'other';
            eventTime.value = events[dateStr].time || '';
            eventDescription.value = events[dateStr].description || '';
            deleteEventBtn.style.display = 'block';
        } else {
            deleteEventBtn.style.display = 'none';
        }
        
        // Show the modal
        eventModal.style.display = 'flex';
    };

    // Close modals
    function closeEventModal() {
        eventModal.style.display = 'none';
    }
    
    function closeDateSelectorModal() {
        dateSelectorModal.style.display = 'none';
    }

    // Save event
    function saveEvent() {
        const title = eventTitle.value.trim();
        
        if (title) {
            // Check if the date is in the past
            const selectedDate = new Date(currentDateStr);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showToast("Cannot create schedule for past dates", true);
                return;
            }
            
            // Check if the date is beyond the max year
            const maxDate = new Date(maxYear, 11, 31);
            if (selectedDate > maxDate) {
                showToast(`Cannot create schedule beyond ${maxYear}`, true);
                return;
            }
            
            // Store event with more detailed information
            events[currentDateStr] = {
                title: title,
                category: eventCategory.value,
                time: eventTime.value,
                description: eventDescription.value.trim(),
                dateCreated: new Date().toISOString()
            };
        } else {
            delete events[currentDateStr];
        }
        
        // Save events and update UI
        localStorage.setItem('calendarEvents', JSON.stringify(events));
        cleanupExpiredEvents();
        renderWeeklyCalendar(today);
        renderMonthCalendar();
        renderScheduleList();
        closeEventModal();
        
        // Show toast notification
        showToast('Schedule saved successfully');
    }

    // Delete event
    function deleteEvent() {
        delete events[currentDateStr];
        
        // Save events and update UI
        localStorage.setItem('calendarEvents', JSON.stringify(events));
        cleanupExpiredEvents();
        renderWeeklyCalendar(today);
        renderMonthCalendar();
        renderScheduleList();
        closeEventModal();
        
        // Show toast notification
        showToast('Schedule deleted successfully');
    }

    // Event listeners for modals
    closeEventBtn.addEventListener('click', closeEventModal);
    closeDateBtn.addEventListener('click', closeDateSelectorModal);
    saveEventBtn.addEventListener('click', saveEvent);
    deleteEventBtn.addEventListener('click', deleteEvent);
    cancelEventBtn.addEventListener('click', closeEventModal);
    
    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === eventModal) closeEventModal();
        if (e.target === dateSelectorModal) closeDateSelectorModal();
    });
    
    // Initialize calendars
    renderWeeklyCalendar(today);
    renderMonthCalendar();
    renderScheduleList();
    
    // Set up interval to update clock and check date
    setInterval(updateLiveClock, 1000);
    setInterval(updateCalendars, 60000); // Check every minute
    updateLiveClock();
});

// Toast notification function
function showToast(message, isError = false) {
    // Create toast container if it doesn't exist
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    
    // Set the message and class
    toast.textContent = message;
    if (isError) {
        toast.classList.add('error');
    } else {
        toast.classList.remove('error');
    }
    
    // Show the toast
    toast.classList.add('show');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function formatDate(date) {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}

function renderWeeklyCalendar(refDate) {
    const weekContainer = document.getElementById('weeklyCalendar');
    if (!weekContainer) {
        console.error('Weekly calendar container not found');
        return;
    }

    const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const base = new Date(refDate.year, refDate.month, refDate.day);
    
    // Center the current day in the 4th block (middle) of the weekly view
    // 4th block is index 3 (0-based), so we subtract 3 days from the current date
    const dayOffset = -3;
    base.setDate(base.getDate() + dayOffset);
    weekContainer.innerHTML = '';

    for (let i = 0; i < 7; i++) {
        const dayDate = new Date(base);
        dayDate.setDate(base.getDate() + i);
        
        // Get day of week (0 = Sunday, 1 = Monday, etc.)
        const dayOfWeek = dayDate.getDay();
        // Convert to our format (0 = Monday, 6 = Sunday)
        const adjustedDayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
        
        const isToday = dayDate.toDateString() === new Date().toDateString();
        const isPast = dayDate < new Date(today.year, today.month, today.day);
        const dateStr = formatDate(dayDate);
        const eventData = events[dateStr];

        const dayEl = document.createElement('div');
        dayEl.className = 'day';
        if (isToday) dayEl.classList.add('today');
        if (isPast) dayEl.classList.add('past');
        if (eventData) dayEl.classList.add('has-event');
        
        dayEl.innerHTML = `
            <div class="day-name">${dayNames[adjustedDayOfWeek]}</div>
            <div class="day-number">${dayDate.getDate()}</div>
            ${isToday ? '<div class="event">Today</div>' : ''}
            ${eventData ? `<div class="event">${eventData.title}</div>` : ''}
        `;
        
        // Add click event to open event modal directly 
        if (!isPast) {
            dayEl.addEventListener('click', () => showDateSelector(dateStr));
        }
        
        weekContainer.appendChild(dayEl);
    }
}

function renderMonthCalendar() {
    const tbody = document.getElementById('monthBody');
    const title = document.getElementById('currentMonthTitle');
    
    if (!tbody || !title) {
        console.error('Calendar elements not found');
        return;
    }

    const date = new Date(selectedDate.year, selectedDate.month);
    const firstDay = (new Date(selectedDate.year, selectedDate.month, 1).getDay() + 6) % 7; // Monday = 0
    const daysInMonth = new Date(selectedDate.year, selectedDate.month + 1, 0).getDate();
    const daysInPrevMonth = new Date(selectedDate.year, selectedDate.month, 0).getDate();

    title.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });
    tbody.innerHTML = '';
    
    // Calculate which day of the week the 15th of the month falls on
    // This helps us center the month view to show current date in 4th row
    const midMonthDay = Math.min(15, daysInMonth);
    const midMonthDate = new Date(selectedDate.year, selectedDate.month, midMonthDay);
    const midMonthDayOfWeek = (midMonthDate.getDay() + 6) % 7; // Monday = 0
    
    // Calculate how many days to show from previous month to align the 15th in 4th row
    // We want the 15th to be in the middle of the calendar
    let startOffset = midMonthDay - 15; // Default: mid-month is the 15th
    
    // Adjust for day of week to position current date in the 4th row (approximately)
    startOffset = startOffset - midMonthDayOfWeek + 3; // +3 adjusts for middle of week
    
    // Handle edge cases
    startOffset = Math.max(startOffset, 1 - firstDay);
    startOffset = Math.min(startOffset, daysInMonth - 35); // Don't leave too many days off at the end
    
    let day = startOffset <= 0 ? 1 - startOffset : 1;
    let inCurrentMonth = startOffset <= 0;
    let nextMonthDay = 1;

    for (let i = 0; i < 6; i++) {
        const row = document.createElement('tr');
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            
            if (!inCurrentMonth && day <= 0) {
                // Previous month
                cell.textContent = daysInPrevMonth + day;
                cell.className = 'other-month';
                day++;
                if (day > 0) {
                    day = 1;
                    inCurrentMonth = true;
                }
            } else if (inCurrentMonth && day <= daysInMonth) {
                // Current month
                cell.textContent = day;
                const dateStr = `${selectedDate.year}-${String(selectedDate.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const cellDate = new Date(selectedDate.year, selectedDate.month, day);
                
                if (day === today.day && selectedDate.month === today.month && selectedDate.year === today.year) {
                    cell.classList.add('active');
                }
                
                // Mark past dates
                const isPast = cellDate < new Date(today.year, today.month, today.day);
                if (isPast) {
                    cell.classList.add('past');
                }

                // Check if date has an event and add appropriate class
                if (events[dateStr]) {
                    cell.classList.add('has-event');
                    
                    // Add event marker for visual indication
                    const marker = document.createElement('div');
                    marker.className = 'event-marker';
                    cell.appendChild(marker);
                }

                cell.setAttribute('data-date', dateStr);
                
                // Add click event to open event modal directly
                if (!isPast) {
                    cell.addEventListener('click', function() {
                        showDateSelector(dateStr);
                    });
                }
                
                day++;
                if (day > daysInMonth) {
                    inCurrentMonth = false;
                    day = 1; // Reset for next month
                }
            } else {
                // Next month
                cell.textContent = nextMonthDay++;
                cell.className = 'other-month';
            }

            row.appendChild(cell);
        }
        tbody.appendChild(row);
        if (!inCurrentMonth && day > daysInMonth) break;
    }
}

// Render the schedule list
function renderScheduleList() {
    const scheduleList = document.getElementById('scheduleList');
    if (!scheduleList) return;
    
    scheduleList.innerHTML = '';
    
    // Get all events and sort them chronologically
    const eventsList = Object.entries(events)
        .map(([dateStr, eventData]) => ({
            date: dateStr,
            ...eventData
        }))
        .sort((a, b) => new Date(a.date) - new Date(b.date));
    
    if (eventsList.length === 0) {
        scheduleList.innerHTML = '<p>No schedules found. Click on a date to add a schedule.</p>';
        return;
    }
    
    // Create schedule items
    eventsList.forEach(event => {
        const scheduleItem = document.createElement('div');
        scheduleItem.className = 'schedule-item';
        
        const formattedDate = formatDateForDisplay(event.date);
        
        scheduleItem.innerHTML = `
            <div class="schedule-date">${formattedDate}</div>
            ${event.time ? `<div class="schedule-time">${event.time}</div>` : ''}
            <div class="schedule-title">${event.title}</div>
            ${event.description ? `<div class="schedule-description">${event.description}</div>` : ''}
        `;
        
        // Add click event to edit this schedule
        scheduleItem.addEventListener('click', () => openModal(event.date));
        
        scheduleList.appendChild(scheduleItem);
    });
}

function updateCalendars() {
    const now = new Date();
    const currentDate = {
        day: now.getDate(),
        month: now.getMonth(),
        year: now.getFullYear()
    };

    if (
        currentDate.day !== today.day ||
        currentDate.month !== today.month ||
        currentDate.year !== today.year
    ) {
        today = currentDate;
        selectedDate = {...today};
        document.getElementById('monthSelect').value = today.month;
        document.getElementById('yearSelect').value = today.year;
        
        // Clean up expired events
        cleanupExpiredEvents();
        
        // Update UI
        renderWeeklyCalendar(today);
        renderMonthCalendar();
    }
}

function updateLiveClock() {
    const now = new Date();
    const options = { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
    const clockElement = document.getElementById('liveClock');
    if (clockElement) {
        clockElement.textContent = now.toLocaleTimeString('en-US', options);
    }
}