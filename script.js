const eventCards = document.querySelectorAll('.event-card');
const shareButtons = document.querySelectorAll('.share-btn');

// simple HTML escaper
function escapeHtml(unsafe) {
    return String(unsafe)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('myModal');
    const closeBtn = document.getElementById('closeModal');
    const btnsPresenter = document.querySelectorAll('.button-presenter');

    btnsPresenter.forEach(btn => {
        btn.addEventListener('click', function () {
            const biography = this.dataset.biography || '';
            const providedPhoto = this.dataset.photo || '';
            const providedLinkedin = this.dataset.linkedin || '';
            const modal = document.querySelector('.modal');
            const modalContent = document.getElementById('modalContent');
            const modalSections = document.getElementById('modalSections');
            const modalLinkedIn = document.getElementById('modalLinkedIn');
            const modalAvatar = document.getElementById('modalAvatar');

            // Basic cleanup
            modalContent.textContent = '';
            if (modalSections) modalSections.innerHTML = '';
            modalLinkedIn.hidden = true;
            modalLinkedIn.href = '#';
            modalAvatar.src = '';

            // Prefer explicit data-linkedin, otherwise try to extract from biography
            if (providedLinkedin) {
                modalLinkedIn.href = providedLinkedin;
                modalLinkedIn.hidden = false;
            } else {
                const lnMatch = biography.match(/https?:\/\/(www\.)?linkedin\.com\/[A-Za-z0-9\-\/_?=.#%]*/i);
                if (lnMatch) {
                    modalLinkedIn.href = lnMatch[0];
                    modalLinkedIn.hidden = false;
                }
            }

            // Split biography into paragraphs and render the About block
            const paragraphs = biography.split(/\n\s*\n|<br\s*\/?\>|\r\n\s*\r\n/).map(p => p.trim()).filter(Boolean);
            let aboutParas = paragraphs.slice();
            if (aboutParas.length === 0 && biography.trim() !== '') {
                aboutParas = [biography];
            }

            const aboutHtml = aboutParas.map(p => `<p class="modal-section-body">${escapeHtml(p).replace(/\n/g, '<br>')}</p>`).join('');
            modalContent.innerHTML = aboutHtml;
            modalContent.style.display = 'block';
            if (modalSections) modalSections.style.display = 'none';

            // Avatar: prefer provided photo, otherwise initials placeholder (larger)
            if (providedPhoto) {
                modalAvatar.src = providedPhoto;
            } else {
                const initials = (this.textContent || 'S').split(' ').map(s => s[0]).slice(0,2).join('').toUpperCase() || 'S';
                modalAvatar.src = `data:image/svg+xml;utf8,${encodeURIComponent(`<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><rect width='100%' height='100%' fill='%23344656'/><text x='50%' y='55%' font-size='56' font-family='Arial, Helvetica, sans-serif' fill='%23fff' text-anchor='middle' dominant-baseline='middle'>${initials}</text></svg>`)}`;
            }

            // Show modal
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        })
    })

    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
    });

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
    });

    // close modal with Escape key
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (modal && modal.style.display === 'flex') {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    });

    shareButtons.forEach(btn => {
        btn.addEventListener('click', handleShare);
        btn.setAttribute('aria-label', 'Share this event');
        btn.setAttribute('title', 'Share');
        btn.setAttribute('role', 'button');
        btn.setAttribute('tabindex', '0');
        btn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleShare.call(this, e);
            }
        });
    });
    const searchInput = document.getElementById('searchInput');
    const cancelBtn = document.getElementById('cancelBtn');
    const eventCards = document.querySelectorAll('.event-card');

    let lastActiveDay = getActiveDay();
    let searchWasEmpty = true;

    function getActiveDay() {
        const active = document.querySelector('.day-number.active-day');
        return active ? active.closest('.day-column').dataset.day : 'monday';
    }

    function applyDayFilter(day) {
        document.querySelectorAll('.day-number').forEach(d => d.classList.remove('active-day'));
        const target = document.querySelector(`.day-column[data-day="${day}"] .day-number`);
        if (target) target.classList.add('active-day');

        eventCards.forEach(card => {
            card.style.display = (day === 'all' || card.dataset.day === day) ? 'flex' : 'none';
        });
    }

    (function selectDefaultDayByDate() {
        const dayColumns = Array.from(document.querySelectorAll('.day-column'));
        if (dayColumns.length === 0) return;

        const monthMap = {
            'JAN': 0, 'FEB': 1, 'MAR': 2, 'APR': 3, 'MAY': 4, 'JUN': 5,
            'JUL': 6, 'AUG': 7, 'SEP': 8, 'OCT': 9, 'NOV': 10, 'DEC': 11
        };

        const titleText = (document && document.title) ? document.title : '';
        const headerText = document.querySelector('.header-content h1')?.textContent || '';
        const yearMatch = (titleText + ' ' + headerText).match(/\b(20\d{2})\b/);
        const inferredYear = yearMatch ? parseInt(yearMatch[1], 10) : (new Date()).getFullYear();
        console.log(inferredYear);

        const parsed = dayColumns.map(col => {
            const key = col.dataset.day;
            const dayNumberEl = col.querySelector('.day-number');
            const monthEl = col.querySelector('.month');
            if (!dayNumberEl || !monthEl) return null;
            const dayNum = parseInt(dayNumberEl.textContent.trim(), 10);
            const monthAbbr = monthEl.textContent.trim().toUpperCase();
            const monthIdx = monthMap[monthAbbr];
            if (Number.isNaN(dayNum) || monthIdx === undefined) return null;
            const date = new Date(inferredYear, monthIdx, dayNum);
            return { key, date };
        }).filter(Boolean);

        if (parsed.length === 0) return;

        parsed.sort((a, b) => a.date - b.date);

        var today = new Date();
        const start = parsed[0];
        const end = parsed[parsed.length - 1];

        const toYmd = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

        let targetKey = null;


        if (today < start.date) {
            targetKey = start.key;
        } else if (today > end.date) {
            targetKey = end.key;
        } else {
            const todayYmd = toYmd(today);
            const sameDay = parsed.find(p => toYmd(p.date) === todayYmd);
            targetKey = sameDay ? sameDay.key : start.key;
        }

        if (targetKey) {
            applyDayFilter(targetKey);
            lastActiveDay = targetKey;
        }
    })();

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            if (searchTerm.length > 0) {
                if (searchWasEmpty) {
                    lastActiveDay = getActiveDay();
                }
                searchWasEmpty = false;
                cancelBtn.style.display = 'block';
            } else {
                searchWasEmpty = true;
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

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            searchInput.value = '';

            cancelBtn.style.display = 'none';

            applyDayFilter(lastActiveDay || getActiveDay());

            const existingMessage = document.querySelector('.no-results');
            if (existingMessage) {
                existingMessage.remove();
            }

            searchInput.focus();
        });
    }

    const dayColumns = document.querySelectorAll('.day-column');
    dayColumns.forEach(column => {
        column.addEventListener('click', function () {
            const selectedDay = this.dataset.day;

            searchInput.value = '';
            cancelBtn.style.display = 'none';
            searchWasEmpty = true;
            lastActiveDay = selectedDay;

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

async function handleShare(e) {
    e.stopPropagation();
    const eventCard = e.target.closest('.event-card');
    const eventTitle = eventCard?.querySelector('.event-title')?.textContent || document.title;
    const pageUrl = window.location.href;

    const shareData = {
        title: eventTitle,
        text: 'Check out this TechWeek 2025 event!',
        url: pageUrl
    };

    try {
        if (navigator.share && (!navigator.canShare || navigator.canShare(shareData))) {
            await navigator.share(shareData);
            return;
        }
    } catch (err) {
    }
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