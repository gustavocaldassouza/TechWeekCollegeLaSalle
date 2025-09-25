const eventCards = document.querySelectorAll('.event-card');
const shareButtons = document.querySelectorAll('.share-btn');

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('myModal');
    const closeBtn = document.getElementById('closeModal');
    const btnsPresenter = document.querySelectorAll('.button-presenter');

    btnsPresenter.forEach(btn => {
        btn.addEventListener('click', function () {
            const biography = this.dataset.biography;
            const modalContent = document.getElementById('modalContent');
            modalContent.textContent = biography;
            const modal = document.querySelector('.modal');
            modal.style.display = 'flex';
        })
    })

    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    shareButtons.forEach(btn => {
        btn.addEventListener('click', handleShare);
    });
    const searchInput = document.getElementById('searchInput');
    const cancelBtn = document.getElementById('cancelBtn');
    const eventCards = document.querySelectorAll('.event-card');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            // Show/hide cancel button based on search input
            if (searchTerm.length > 0) {
                cancelBtn.style.display = 'block';
            } else {
                cancelBtn.style.display = 'none';
            }

            eventCards.forEach(card => {
                const title = card.querySelector('.event-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.event-description')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.location')?.textContent.toLowerCase() || '';
                const presenter = card.querySelector('.presenter')?.textContent.toLowerCase() || '';

                const matches = title.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    location.includes(searchTerm) ||
                    presenter.includes(searchTerm);

                card.style.display = matches ? 'flex' : 'none';
            });

            updateNoResultsMessage(searchTerm);
        });
    }

    // Cancel button functionality
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            // Clear the search input
            searchInput.value = '';

            // Hide the cancel button
            cancelBtn.style.display = 'none';

            // Show all event cards
            eventCards.forEach(card => {
                card.style.display = 'flex';
            });

            // Remove any no results message
            const existingMessage = document.querySelector('.no-results');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Reset to Monday view (first day)
            document.querySelectorAll('.day-number').forEach(day => {
                day.classList.remove('active-day');
            });
            document.querySelector('.day-column[data-day="monday"] .day-number').classList.add('active-day');

            // Focus back to search input
            searchInput.focus();
        });
    }

    const dayColumns = document.querySelectorAll('.day-column');
    dayColumns.forEach(column => {
        column.addEventListener('click', function () {
            const selectedDay = this.dataset.day;

            // Clear search and hide cancel button when switching days
            searchInput.value = '';
            cancelBtn.style.display = 'none';

            document.querySelectorAll('.day-number').forEach(day => {
                day.classList.remove('active-day');
            });
            this.querySelector('.day-number').classList.add('active-day');

            eventCards.forEach(card => {
                if (selectedDay === 'all' || card.dataset.day === selectedDay) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            // Remove any no results message
            const existingMessage = document.querySelector('.no-results');
            if (existingMessage) {
                existingMessage.remove();
            }
        });
    });
});

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
                <h3 style="color: #6b7280;">No events found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        `;
        document.querySelector('.events-list').appendChild(noResultsDiv);
    }
}