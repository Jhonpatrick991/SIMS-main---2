document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            const statTitle = this.querySelector('.stat-title').textContent;
            console.log(`Clicked on ${statTitle}`);
            
            if (statTitle.includes('Students')) {
                window.location.href = 'Menu/students.php';
            } else if (statTitle.includes('Sections')) {
                window.location.href = 'Menu/sections.php';
            } else if (statTitle.includes('Subjects')) {
                window.location.href = 'Menu/subjects.php';
            }
        });
        
        const moreInfoLink = card.querySelector('.more-info');
        if (moreInfoLink) {
            moreInfoLink.addEventListener('click', function(event) {
                event.stopPropagation();
                
                const statTitle = card.querySelector('.stat-title').textContent;
                
                if (statTitle.includes('Students')) {
                    window.location.href = 'Menu/students.php';
                } else if (statTitle.includes('Sections')) {
                    window.location.href = 'Menu/sections.php';
                } else if (statTitle.includes('Subjects')) {
                    window.location.href = 'Menu/subjects.php';
                }
            });
        }
    });
    
    const classCards = document.querySelectorAll('.class-card');
    classCards.forEach(card => {
        card.addEventListener('click', function() {
            const classCode = this.querySelector('.class-code').textContent;
            console.log(`Clicked on class ${classCode}`);
            
        });
    });
    
    const sidebarItems = document.querySelectorAll('nav li');
    sidebarItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.backgroundColor = '#444';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.backgroundColor = '';
            }
        });
    }); 

  function updateLiveClock() {
    const now = new Date();
    const options = {
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      hour12: true
    };
    const clockElement = document.getElementById('liveClock');
    if (clockElement) {
      clockElement.textContent = now.toLocaleTimeString('en-US', options);
    }
  }

  updateLiveClock();    
  setInterval(updateLiveClock, 1000);
  
  // Load upcoming events for dashboard
  const dashboardEvents = document.getElementById('dashboardEvents');
  if (dashboardEvents) {
    loadUpcomingEvents();
  }
  
  function loadUpcomingEvents() {
    // Get events from localStorage
    const events = JSON.parse(localStorage.getItem('calendarEvents')) || {};
    
    // Convert to array and sort by date
    const eventsList = Object.entries(events)
      .map(([dateStr, eventData]) => ({
        date: dateStr,
        ...eventData
      }))
      .sort((a, b) => new Date(a.date) - new Date(b.date));
    
    // Get upcoming events (filter out past events but no upper time limit)
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    const upcomingEvents = eventsList.filter(event => {
      const eventDate = new Date(event.date);
      return eventDate >= now;
    });
    
    // Display events
    if (upcomingEvents.length > 0) {
      dashboardEvents.innerHTML = '';
      
      upcomingEvents.forEach(event => {
        const eventDate = new Date(event.date);
        const formattedDate = eventDate.toLocaleDateString('en-US', { 
          weekday: 'long', 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        });
        
        const categoryColors = {
          exams: '#4a5af4',
          meetings: '#38b2ac',
          classes: '#ed8936',
          other: '#9f7aea'
        };
        
        const categoryColor = categoryColors[event.category] || categoryColors.other;
        
        const eventEl = document.createElement('div');
        eventEl.className = 'event-item';
        eventEl.innerHTML = `
          <div class="event-date">${formattedDate}</div>
          <div class="event-content">
            <div class="event-title" style="color: ${categoryColor}">
              <span class="category-dot" style="background-color: ${categoryColor}"></span>
              ${event.title}
            </div>
            ${event.time ? `<div class="event-time">${event.time}</div>` : ''}
            ${event.description ? `<div class="event-description">${event.description}</div>` : ''}
          </div>
        `;
        
        dashboardEvents.appendChild(eventEl);
      });
    } else {
      dashboardEvents.innerHTML = `
        <div class="no-events">
          <i class="fas fa-calendar-plus"></i>
          <p>No upcoming events found.</p>
          <a href="Menu/calendar.php" class="add-event-btn">Add Event</a>
        </div>
      `;
    }
  }
  
  // Global toast notification function
  window.showToast = function(message, isError = false) {
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
        setTimeout(() => {
            toast.classList.remove('show');
        }, 300);
    }, 3000);
  };
  
  // Check for URL parameters for success/error messages
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('updated')) {
    const updated = urlParams.get('updated');
    if (updated === '1') {
        window.showToast('Record updated successfully');
    } else if (updated === '0') {
        window.showToast('Update failed. Please try again.', true);
    }
    
    // Clean the URL without reloading the page
    const newUrl = window.location.pathname;
    window.history.replaceState({}, document.title, newUrl);
  }
  
  // Handle deletion success messages with toast
  if (urlParams.has('deleted')) {
    const deleted = urlParams.get('deleted');
    if (deleted === 'multiple') {
      window.showToast('Multiple items deleted successfully');
    } else if (deleted === '1') {
      window.showToast('Item deleted successfully');
    }
    
    // Clean the URL without reloading the page
    const newUrl = window.location.pathname;
    window.history.replaceState({}, document.title, newUrl);
  }
});


document.querySelector(".user-info").addEventListener("click", function() {
    document.querySelector(".dropdownContent").classList.toggle("show");
})
