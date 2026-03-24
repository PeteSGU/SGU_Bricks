# Residency Programs Map Component

A vanilla JavaScript component for displaying medical residency programs on an interactive Mapbox map. WordPress-ready implementation.

## Features

- 🗺️ Interactive Mapbox map with custom markers
- 📍 71 residency program locations across the USA
- 🔍 Detailed popups with program information
- 📱 Responsive design
- 🎨 Custom styled markers and popups
- ♿ Accessible and SEO-friendly
- 🔒 XSS protection with HTML escaping
- 🎯 SOLID principles and DRY code

## Setup

### 1. Get Mapbox Access Token

1. Sign up for a free account at [Mapbox](https://www.mapbox.com/)
2. Go to your [Account Dashboard](https://account.mapbox.com/)
3. Copy your default public access token
4. Replace `YOUR_MAPBOX_ACCESS_TOKEN_HERE` in `mapbox-component.js`

### 2. Run Local Server

```bash
npm install
npm start
```

The server will start on `http://localhost:3000`

## File Structure

```
.
├── index.html              # Main HTML file
├── mapbox-component.js     # Map component (WordPress-ready)
├── input/
│   └── input.json         # Residency programs data
├── server.js              # Node.js development server
├── package.json           # Node dependencies
└── README.md             # This file
```

## WordPress Integration

### Method 1: Enqueue Scripts (Recommended)

Add to your theme's `functions.php`:

```php
function enqueue_residency_map() {
    // Enqueue Mapbox CSS
    wp_enqueue_style(
        'mapbox-gl-css',
        'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css',
        array(),
        '3.0.1'
    );
    
    // Enqueue Mapbox JS
    wp_enqueue_script(
        'mapbox-gl-js',
        'https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js',
        array(),
        '3.0.1',
        true
    );
    
    // Enqueue your component
    wp_enqueue_script(
        'residency-map',
        get_template_directory_uri() . '/js/mapbox-component.js',
        array('mapbox-gl-js'),
        '1.0.0',
        true
    );
    
    // Pass the Mapbox token to JavaScript
    wp_localize_script('residency-map', 'mapboxConfig', array(
        'accessToken' => 'YOUR_MAPBOX_ACCESS_TOKEN',
        'dataUrl' => get_template_directory_uri() . '/data/input.json'
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_residency_map');
```

Update the JavaScript initialization in `mapbox-component.js`:

```javascript
// Replace the DOMContentLoaded section with:
document.addEventListener('DOMContentLoaded', () => {
    const config = window.mapboxConfig || {};
    
    const mapComponent = new ResidencyMapComponent({
        accessToken: config.accessToken || 'YOUR_MAPBOX_ACCESS_TOKEN_HERE',
        dataUrl: config.dataUrl || 'input/input.json',
        containerId: 'map'
    });

    mapComponent.init();
    window.mapComponentInstance = mapComponent;
});
```

### Method 2: Shortcode

Create a shortcode in `functions.php`:

```php
function residency_map_shortcode($atts) {
    $atts = shortcode_atts(array(
        'height' => '600px'
    ), $atts);
    
    ob_start();
    ?>
    <div id="map" style="width: 100%; height: <?php echo esc_attr($atts['height']); ?>;"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('residency_map', 'residency_map_shortcode');
```

Use in posts/pages:
```
[residency_map height="600px"]
```

### Method 3: Gutenberg Block (Advanced)

Create a custom Gutenberg block for more control. This requires React/JSX knowledge.

## API Reference

### ResidencyMapComponent

#### Constructor

```javascript
new ResidencyMapComponent({
    accessToken: 'YOUR_TOKEN',      // Required: Mapbox access token
    dataUrl: 'input/input.json',    // Path to JSON data file
    containerId: 'map',             // DOM element ID
    initialCenter: [-95.7129, 37.0902], // [lng, lat]
    initialZoom: 4,                 // Initial zoom level
    mapStyle: 'mapbox://styles/mapbox/streets-v12' // Map style
})
```

#### Methods

- `init()` - Initialize the map (async)
- `fitBoundsToMarkers()` - Adjust map to show all markers
- `filterBySpeciality(speciality)` - Filter markers by speciality
- `resetFilter()` - Show all markers
- `destroy()` - Clean up and remove map

#### Example Usage

```javascript
const map = new ResidencyMapComponent({
    accessToken: 'pk.your_token_here',
    dataUrl: 'data/programs.json'
});

await map.init();
map.fitBoundsToMarkers();

// Filter by speciality
map.filterBySpeciality('Internal Medicine');

// Reset
map.resetFilter();
```

## Data Format

The component expects JSON data in this format:

```json
{
    "items": [
        {
            "name": "Program Name",
            "location": {
                "city": "City",
                "state": "ST",
                "lat": 42.6534,
                "long": -73.7749
            },
            "specialities": ["Emergency Medicine", "Internal Medicine"],
            "residencyMatches": [
                {
                    "type": "PGY-1",
                    "count": 13
                }
            ]
        }
    ],
    "total": 71
}
```

## Customization

### Marker Styles

Edit `.marker` class in `index.html`:

```css
.marker {
    width: 30px;
    height: 30px;
    background-color: #0066cc; /* Change color */
    /* Add custom styles */
}
```

### Popup Styles

Customize popup appearance in the `<style>` section of `index.html`.

### Map Style

Change the map style in the configuration:

```javascript
mapStyle: 'mapbox://styles/mapbox/dark-v11' // Dark theme
mapStyle: 'mapbox://styles/mapbox/light-v11' // Light theme
mapStyle: 'mapbox://styles/mapbox/satellite-streets-v12' // Satellite
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Loads 71 markers efficiently
- Lazy loading of popups
- Optimized for mobile devices
- Minimal dependencies

## Security

- HTML escaping to prevent XSS attacks
- No inline scripts
- Content Security Policy compatible

## License

MIT License - Feel free to use in your projects

## Support

For issues or questions, refer to:
- [Mapbox GL JS Documentation](https://docs.mapbox.com/mapbox-gl-js/)
- [WordPress Codex](https://codex.wordpress.org/)
