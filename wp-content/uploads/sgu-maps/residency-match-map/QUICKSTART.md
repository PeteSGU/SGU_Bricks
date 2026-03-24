# Quick Start Guide

## Step 1: Get Your Mapbox Token

1. Visit https://account.mapbox.com/
2. Sign up or log in
3. Copy your default public token (starts with `pk.`)

## Step 2: Configure the Token

Open `mapbox-component.js` and find this line (near the bottom):

```javascript
const MAPBOX_ACCESS_TOKEN = 'YOUR_MAPBOX_ACCESS_TOKEN_HERE';
```

Replace `YOUR_MAPBOX_ACCESS_TOKEN_HERE` with your actual token.

## Step 3: Start the Server

```bash
npm start
```

## Step 4: Open in Browser

Navigate to: http://localhost:3000

You should see the map with 71 pins representing residency programs!

## Next Steps

- Click on any pin to see program details
- Use the zoom and navigation controls
- Read README.md for WordPress integration instructions
