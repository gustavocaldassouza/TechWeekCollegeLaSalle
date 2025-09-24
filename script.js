const searchInput = document.getElementById('searchInput');
const filterTabs = document.querySelectorAll('.tab');
const dayColumns = document.querySelectorAll('.day-column');
const eventCards = document.querySelectorAll('.event-card');
const shareButtons = document.querySelectorAll('.share-btn');

let currentFilter = 'all';
let currentCategory = 'all';
let selectedDay = 8;

document.addEventListener('DOMContentLoaded', function () {
    initializeEventListeners();
    updateActiveStates();
});

function initializeEventListeners() {
    searchInput.addEventListener('input', handleSearch);
    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => handleFilterChange(e.target.dataset.filter));
    });

    dayColumns.forEach(column => {
        column.addEventListener('click', (e) => handleDaySelection(e.currentTarget));
    });

    shareButtons.forEach(btn => {
        btn.addEventListener('click', handleShare);
    });

    document.addEventListener('keydown', handleKeyboardShortcuts);
}

function handleSearch(e) {
    const searchTerm = e.target.value.toLowerCase().trim();

    eventCards.forEach(card => {
        const title = card.querySelector('.event-title').textContent.toLowerCase();
        const description = card.querySelector('.event-description').textContent.toLowerCase();
        const location = card.querySelector('.location').textContent.toLowerCase();

        const matches = title.includes(searchTerm) ||
            description.includes(searchTerm) ||
            location.includes(searchTerm);

        if (matches || searchTerm === '') {
            card.style.display = 'flex';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });

    updateNoResultsMessage(searchTerm);
}

function handleFilterChange(filter) {
    currentFilter = filter;

    filterTabs.forEach(tab => tab.classList.remove('active'));
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

    applyFilters();
}

function applyFilters() {
    eventCards.forEach(card => {
        let shouldShow = true;

        if (currentFilter !== 'all') {
            const isPast = card.querySelector('.past-tag');

            switch (currentFilter) {
                case 'past':
                    shouldShow = isPast !== null;
                    break;
                case 'upcoming':
                case 'live':
                    shouldShow = isPast === null;
                    break;
            }
        }

        if (currentCategory !== 'all categories' && shouldShow) {
            const eventTags = card.querySelectorAll('.tag:not(.past-tag)');
            const categoryMatch = Array.from(eventTags).some(tag =>
                tag.textContent.toLowerCase().includes(currentCategory)
            );

            if (currentCategory === 'networking' && !categoryMatch) {
                shouldShow = card.querySelector('.event-title').textContent.toLowerCase().includes('network');
            } else if (!categoryMatch && currentCategory !== 'all categories') {
                shouldShow = false;
            }
        }

        if (shouldShow) {
            card.style.display = 'flex';
            card.style.animation = 'slideIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

function handleDaySelection(columnElement) {
    document.querySelectorAll('.day-number').forEach(day => {
        day.classList.remove('active-day');
    });

    const dayNumber = columnElement.querySelector('.day-number');
    dayNumber.classList.add('active-day');
    selectedDay = parseInt(dayNumber.textContent);

    showLoadingState();
    setTimeout(() => {
        loadEventsForDay(selectedDay);
        hideLoadingState();
    }, 500);
}

function handleShare(e) {
    e.stopPropagation();
    const eventCard = e.target.closest('.event-card');
    const eventTitle = eventCard.querySelector('.event-title').textContent;

    if (navigator.share) {
        navigator.share({
            title: eventTitle,
            text: 'Check out this TechWeek 2025 event!',
            url: window.location.href
        });
    } else {
        const shareText = `${eventTitle} - TechWeek 2025`;
        navigator.clipboard.writeText(shareText).then(() => {
            showToast('Event link copied to clipboard!');
        });
    }
}

function showToast(message) {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 20px;
        background: #1f2937;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 1001;
        animation: slideUp 0.3s ease;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function showLoadingState() {
    const eventsContainer = document.querySelector('.events-list');
    eventsContainer.style.opacity = '0.5';
    eventsContainer.style.pointerEvents = 'none';
}

function hideLoadingState() {
    const eventsContainer = document.querySelector('.events-list');
    eventsContainer.style.opacity = '1';
    eventsContainer.style.pointerEvents = 'all';
}

function loadEventsForDay(day) {
    console.log(`Loading events for day ${day}`);
    updateEventCounts();
}

function updateEventCounts() {
    const eventCounts = document.querySelectorAll('.event-count');
    eventCounts.forEach((count) => {
        const randomCount = Math.floor(Math.random() * 8) + 1;
        count.textContent = `${randomCount} events`;
    });
}

function updateNoResultsMessage(searchTerm) {
    const existingMessage = document.querySelector('.no-results');
    if (existingMessage) {
        existingMessage.remove();
    }

    const visibleCards = Array.from(eventCards).filter(card =>
        card.style.display !== 'none'
    );

    if (visibleCards.length === 0 && searchTerm !== '') {
        const noResultsDiv = document.createElement('div');
        noResultsDiv.className = 'no-results';
        noResultsDiv.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: #6b7280;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                <h3>No events found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        `;
        document.querySelector('.events-list').appendChild(noResultsDiv);
    }
}

function updateActiveStates() {
    document.querySelector('[data-filter="all"]').classList.add('active');
}

function handleKeyboardShortcuts(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
    }

    if (e.key === 'Escape' && document.activeElement === searchInput) {
        searchInput.value = '';
        handleSearch({ target: searchInput });
        searchInput.blur();
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideIn {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    @keyframes slideUp {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    @keyframes slideDown {
        from { 
            opacity: 1; 
            transform: translateY(0); 
        }
        to { 
            opacity: 0; 
            transform: translateY(20px); 
        }
    }
    
    .event-card {
        transition: all 0.3s ease;
    }
    
    .event-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
`;

document.head.appendChild(style);

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const debouncedSearch = debounce(handleSearch, 300);
searchInput.removeEventListener('input', handleSearch);
searchInput.addEventListener('input', debouncedSearch);