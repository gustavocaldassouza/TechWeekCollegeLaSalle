# TechWeek 2025 - LaSalle College Montreal

A modern, responsive web application for displaying TechWeek 2025 event schedule and information for LaSalle College Montreal.

## Features

- **Event Schedule Display**: View all TechWeek 2025 events organized by day
- **Interactive Calendar**: Click on different days to filter events
- **Search Functionality**: Search through events by title, description, location, or presenter
- **Speaker Biographies**: Click on presenter names to view detailed biographies
- **LinkedIn Integration**: Automatic detection and linking of LinkedIn profiles in speaker bios
- **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **Dark Mode Support**: Automatically adapts to user's system theme preference
- **Share Functionality**: Native mobile sharing with clipboard fallback

## Technology Stack

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Data Source**: External JSON API
- **Styling**: Custom CSS with CSS Variables for theming

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/gustavocaldassouza/TechWeekCollegeLaSalle
   cd zeroug
   ```

2. **Start PHP development server**
   ```bash
   php -S localhost:8000
   ```

3. **Open in browser**
   Navigate to `http://localhost:8000`

## Project Structure

```
zeroug/
├── index.php          # Main application file
├── script.js          # JavaScript functionality
├── styles.css         # CSS styles and theming
├── logo.png           # TechWeek logo
├── favicon.ico        # Website icon
└── README.md          # This file
```

## Features Overview

### Event Display
- Events are fetched from an external JSON API
- Each event shows time, title, location, presenter, and description
- Events are organized by day with visual indicators

### Search & Filter
- Real-time search across all event fields
- Day-based filtering with visual calendar navigation
- Clear search functionality with cancel option

### Speaker Information
- Clickable presenter names reveal detailed biographies
- Automatic LinkedIn link detection and conversion
- Modal popup with responsive design

### Responsive Design
- Mobile-first approach
- Adaptive header layout for different screen sizes
- Touch-friendly interface elements
- Optimized modal and button sizing

### Accessibility
- Keyboard navigation support
- ARIA labels and roles
- Focus management
- Screen reader friendly

## Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Development

### Running Locally
```bash
# Start PHP server
php -S localhost:8000

# Or use any other local server
# The project works with any web server that supports PHP
```

### File Structure
- `index.php`: Main application logic and HTML structure
- `script.js`: Client-side functionality and interactions
- `styles.css`: All styling with CSS custom properties for theming

## Customization

### Theming
The application uses CSS custom properties for easy theming. Modify the `:root` variables in `styles.css` to change colors, spacing, and typography.

### Data Source
The application fetches data from: `https://zermoh.github.io/restapi/techweek_schedule.json`

To use a different data source, modify the `$url` variable in the `fetchTechWeekData()` function in `index.php`.

## License

© 2025 Elite Team. All rights reserved.

## Contributing

This project is maintained by the Elite Team for LaSalle College Montreal's TechWeek 2025 event.

---

**TechWeek 2025** - September 29 to October 5, 2025  
LaSalle College Montreal
