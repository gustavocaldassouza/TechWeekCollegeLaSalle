function initSearchBar({ containerId, placeholder = 'Search events...', onChange }) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const bar = document.createElement('div');
    bar.id = 'search-bar';

    const icon = document.createElement('span');
    icon.className = 'icon';
    icon.innerHTML = '&#128269;';

    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = placeholder;

    const clear = document.createElement('span');
    clear.className = 'clear';
    clear.innerHTML = '&#10005;';
    clear.style.display = 'none';

    bar.append(icon, input, clear);
    container.appendChild(bar);

    input.addEventListener('input', () => {
        const text = input.value;
        clear.style.display = text ? 'inline' : 'none';
        onChange(text);
    });

    clear.addEventListener('click', () => {
        input.value = '';
        clear.style.display = 'none';
        onChange('');
        input.focus();
    });
}
