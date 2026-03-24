/**
 * MapManager - Handles Mapbox GL map initialization and operations
 * Single Responsibility: Map rendering, layers, and interactions
 */
class MapManager {
    constructor(config, uiManager) {
        this.config = config;
        this.uiManager = uiManager;
        this.map = null;
        this._baseMapColorsScheduled = false;
        this.activePopups = new Set();
    }

    getFitPadding() {
        const isMobile = typeof window !== 'undefined' &&
            typeof window.matchMedia === 'function' &&
            window.matchMedia('(max-width: 768px)').matches;

        if (isMobile) {
            return { top: 120, bottom: 40, left: 40, right: 40 };
        }

        // Desktop: keep room for the left filter panel
        return { top: 140, bottom: 80, left: 400, right: 80 };
    }

    zoomToPrograms(programs, { preferFitBounds = true } = {}) {
        if (!this.map) return false;
        const list = Array.isArray(programs) ? programs : [];

        const points = [];
        for (const p of list) {
            const lat = Number(p?.location?.lat);
            const lng = Number(p?.location?.long);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;
            points.push([lng, lat]);
        }

        if (points.length === 0) return false;

        if (preferFitBounds && points.length >= 2) {
            const bounds = new mapboxgl.LngLatBounds();
            for (const pt of points) bounds.extend(pt);
            if (!bounds.isEmpty()) {
                this.map.fitBounds(bounds, {
                    padding: this.getFitPadding(),
                    maxZoom: 12,
                    duration: 900
                });
                return true;
            }
        }

        const [lng, lat] = points[0];
        this.map.flyTo({
            center: [lng, lat],
            zoom: Math.max(this.map.getZoom?.() ?? 4, 11),
            duration: 900
        });
        return true;
    }

    /**
     * Initialize the Mapbox map
     */
    initialize() {
        mapboxgl.accessToken = this.config.accessToken;

        this.map = new mapboxgl.Map({
            container: this.config.containerId,
            style: this.config.mapStyle,
            center: this.config.initialCenter,
            zoom: this.config.initialZoom
        });

        return new Promise((resolve) => {
            this.map.on('load', () => {
                this.applyBaseMapColors();
                this.setupLayers();
                this.setupInteractions();
                this.setupMapControls();
                this.setupMapEventListeners();

                // Re-apply colors if the style is reloaded at runtime
                this.map.on('styledata', () => this.applyBaseMapColors());
                resolve(this.map);
            });
        });
    }

    /**
     * Apply custom base map colors (water/land) to the current style.
     */
    applyBaseMapColors() {
        if (!this.map) return;
        if (this._baseMapColorsScheduled) return;

        this._baseMapColorsScheduled = true;
        requestAnimationFrame(() => {
            this._baseMapColorsScheduled = false;

            const style = this.map.getStyle?.();
            const layers = style?.layers;
            if (!layers || layers.length === 0) return;

            const WATER_COLOR = '#2699ea';
            const LAND_COLOR = '#ccecfb';
            const LABEL_COLOR = '#1A1B4B';

            for (const layer of layers) {
                if (!layer?.id || !layer?.type) continue;

                const layerId = String(layer.id).toLowerCase();
                const sourceLayer = String(layer['source-layer'] || '').toLowerCase();

                const isWater = layerId.includes('water') || sourceLayer.includes('water');
                const isLand =
                    layerId === 'background' ||
                    layerId.includes('land') ||
                    layerId.includes('landcover') ||
                    layerId.includes('park') ||
                    sourceLayer.includes('land') ||
                    sourceLayer.includes('landcover');

                const isLabel =
                    layer.type === 'symbol' &&
                    (layerId.includes('label') ||
                        layerId.includes('place') ||
                        layerId.includes('poi') ||
                        layerId.includes('road') ||
                        layerId.includes('transit') ||
                        sourceLayer.includes('label') ||
                        sourceLayer.includes('place') ||
                        sourceLayer.includes('poi'));

                try {
                    if (layer.type === 'background') {
                        this.map.setPaintProperty(layer.id, 'background-color', LAND_COLOR);
                        continue;
                    }

                    if (isLabel) {
                        // Make map copy (labels) match the UI theme
                        this.map.setPaintProperty(layer.id, 'text-color', LABEL_COLOR);
                        // Some label layers render icons (POIs) that can be tinted
                        this.map.setPaintProperty(layer.id, 'icon-color', LABEL_COLOR);
                        // Don't continue; some symbol layers also represent water labels, etc.
                    }

                    if (isWater) {
                        if (layer.type === 'fill') {
                            this.map.setPaintProperty(layer.id, 'fill-color', WATER_COLOR);
                        } else if (layer.type === 'line') {
                            this.map.setPaintProperty(layer.id, 'line-color', WATER_COLOR);
                        }
                        continue;
                    }

                    if (isLand && layer.type === 'fill') {
                        this.map.setPaintProperty(layer.id, 'fill-color', LAND_COLOR);
                    }
                } catch {
                    // Some layers may not support these paint properties; skip safely.
                }
            }
        });
    }

    /**
     * Setup map layers for clustering
     */
    setupLayers() {
        // Add GeoJSON source with clustering
        this.map.addSource('programs', {
            type: 'geojson',
            data: {
                type: 'FeatureCollection',
                features: []
            },
            cluster: true,
            clusterMaxZoom: 14,
            clusterRadius: 50
        });

        // Add cluster circle layer
        this.map.addLayer({
            id: 'clusters',
            type: 'circle',
            source: 'programs',
            filter: ['has', 'point_count'],
            paint: {
                'circle-color': '#1A1B4B',
                'circle-radius': [
                    'step',
                    ['get', 'point_count'],
                    20,
                    10, 25,
                    100, 30
                ],
                'circle-stroke-width': 2,
                'circle-stroke-color': '#1A1B4B'
            }
        });

        // Add cluster count label layer
        this.map.addLayer({
            id: 'cluster-count',
            type: 'symbol',
            source: 'programs',
            filter: ['has', 'point_count'],
            layout: {
                'text-field': ['get', 'point_count_abbreviated'],
                'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
                'text-size': 16
            },
            paint: {
                'text-color': '#ffffff'
            }
        });

        // Add unclustered point layer
        this.map.addLayer({
            id: 'unclustered-point',
            type: 'circle',
            source: 'programs',
            filter: ['!', ['has', 'point_count']],
            paint: {
                'circle-color': '#1A1B4B',
                'circle-radius': 8,
                'circle-stroke-width': 3,
                'circle-stroke-color': '#fff'
            }
        });
    }

    /**
     * Setup map interactions (clicks, hovers)
     */
    setupInteractions() {
        // Cluster click: zoom in
        this.map.on('click', 'clusters', (e) => {
            const features = this.map.queryRenderedFeatures(e.point, {
                layers: ['clusters']
            });
            const clusterId = features[0].properties.cluster_id;
            this.map.getSource('programs').getClusterExpansionZoom(
                clusterId,
                (err, zoom) => {
                    if (err) return;

                    this.map.easeTo({
                        center: features[0].geometry.coordinates,
                        zoom: zoom
                    });
                }
            );
        });

        // Point click: show popup
        this.map.on('click', 'unclustered-point', (e) => {
            e.originalEvent.stopPropagation();
            const coordinates = e.features[0].geometry.coordinates.slice();
            const properties = e.features[0].properties;

            const popupContent = this.uiManager.createPopupContent({
                name: properties.name,
                location: {
                    city: properties.city,
                    state: properties.state,
                    country: properties.country
                },
                specialities: this.safeJsonParse(properties.specialities, []),
                residencyMatches: this.safeJsonParse(properties.residencyMatches, [])
            });

            const popup = new mapboxgl.Popup({
                closeButton: false,
                closeOnClick: true
            })
                .setLngLat(coordinates)
                .setHTML(popupContent)
                .addTo(this.map);

            this.activePopups.add(popup);
            popup.on('close', () => this.activePopups.delete(popup));
        });

        // Cursor changes
        this.map.on('mouseenter', 'clusters', () => {
            this.map.getCanvas().style.cursor = 'pointer';
        });
        this.map.on('mouseleave', 'clusters', () => {
            this.map.getCanvas().style.cursor = '';
        });
        this.map.on('mouseenter', 'unclustered-point', () => {
            this.map.getCanvas().style.cursor = 'pointer';
        });
        this.map.on('mouseleave', 'unclustered-point', () => {
            this.map.getCanvas().style.cursor = '';
        });
    }

    safeJsonParse(value, fallback) {
        try {
            if (typeof value !== 'string') return fallback;
            return JSON.parse(value);
        } catch {
            return fallback;
        }
    }

    /**
     * Zoom to a state with a circular radius (miles). Uses program locations
     * in that state to compute a centroid; falls back to Mapbox geocoding.
     */
    async zoomToStateRadius(stateQuery, items, radiusMiles = 50) {
        if (!this.map || !stateQuery) return;

        const query = String(stateQuery).trim();
        if (!query) return;

        const matching = Array.isArray(items)
            ? items.filter(p => (p?.location?.state || '').toLowerCase() === query.toLowerCase())
            : [];

        if (matching.length >= 2) {
            const bounds = new mapboxgl.LngLatBounds();
            for (const p of matching) {
                const lat = Number(p?.location?.lat);
                const lng = Number(p?.location?.long);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;
                bounds.extend([lng, lat]);
            }

            if (!bounds.isEmpty()) {
                this.map.fitBounds(bounds, {
                    padding: this.getFitPadding(),
                    duration: 900
                });
                return true;
            }
        }

        if (matching.length === 1) {
            const only = matching[0];
            const lat = Number(only?.location?.lat);
            const lng = Number(only?.location?.long);
            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                await this.fitRadiusBounds(lng, lat, radiusMiles);
                return true;
            }
        }

        // Fallback: Mapbox Geocoding (region)
        try {
            const regionQuery = window.StateUtils?.getRegionQuery ? window.StateUtils.getRegionQuery(query) : query;
            const url = new URL(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json`);
            url.pathname = `/geocoding/v5/mapbox.places/${encodeURIComponent(regionQuery)}.json`;
            url.searchParams.set('access_token', mapboxgl.accessToken);
            url.searchParams.set('types', 'region');
            url.searchParams.set('limit', '1');

            const res = await fetch(url.toString());
            if (!res.ok) return;
            const data = await res.json();
            const feature = data?.features?.[0];
            const center = feature?.center;
            if (!center || center.length < 2) return;

            const [lng, lat] = center;
            await this.fitRadiusBounds(lng, lat, radiusMiles);
            return true;
        } catch {
            // Ignore geocoding failures
        }

        return false;
    }

    computeCentroid(programs) {
        let sumLat = 0;
        let sumLng = 0;
        let count = 0;

        for (const p of programs) {
            const lat = Number(p?.location?.lat);
            const lng = Number(p?.location?.long);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) continue;
            sumLat += lat;
            sumLng += lng;
            count += 1;
        }

        return count === 0 ? { lat: NaN, lng: NaN } : { lat: sumLat / count, lng: sumLng / count };
    }

    async fitRadiusBounds(centerLng, centerLat, radiusMiles) {
        const miles = Number(radiusMiles);
        if (!Number.isFinite(miles) || miles <= 0) return;

        const lat = Number(centerLat);
        const lng = Number(centerLng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

        const deltaLat = miles / 69;
        const cosLat = Math.cos((lat * Math.PI) / 180);
        const deltaLng = miles / (69 * Math.max(cosLat, 0.01));

        const sw = [lng - deltaLng, lat - deltaLat];
        const ne = [lng + deltaLng, lat + deltaLat];

        const movePromise = new Promise(resolve => {
            // moveend fires after fitBounds animation completes
            this.map.once('moveend', () => resolve());
        });

        this.map.fitBounds([sw, ne], {
            padding: this.getFitPadding(),
            duration: 900
        });

        await movePromise;
    }

    /**
     * Setup map event listeners for counter updates
     */
    setupMapEventListeners() {
        // Update counter when map moves or zooms
        this.map.on('moveend', () => {
            if (this.onCounterUpdate) {
                this.onCounterUpdate();
            }
        });

        this.map.on('zoomend', () => {
            if (this.onCounterUpdate) {
                this.onCounterUpdate();
            }
        });
    }

    /**
     * Setup custom map controls
     */
    setupMapControls() {
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const fitScreenBtn = document.getElementById('fitScreenBtn');
        const searchAreaBtn = document.getElementById('searchAreaBtn');
        const mapViewContainer = document.getElementById('mapViewContainer');

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', () => this.map.zoomIn());
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', () => this.map.zoomOut());
        }

        if (fitScreenBtn) {
            fitScreenBtn.addEventListener('click', async () => {
                await this.toggleFullscreen(mapViewContainer);
            });
        }

        if (searchAreaBtn) {
            searchAreaBtn.addEventListener('click', () => {
                if (this.onSearchArea) {
                    this.onSearchArea(this.map.getBounds());
                }
            });
        }

        // Keep Mapbox canvas sized correctly when entering/exiting fullscreen
        document.addEventListener('fullscreenchange', () => {
            if (this.map) {
                this.map.resize();
            }
        });
    }

    /**
     * Toggle fullscreen for the map view (map + overlays)
     */
    async toggleFullscreen(containerEl) {
        try {
            if (!containerEl) return;

            if (document.fullscreenElement) {
                await document.exitFullscreen();
            } else {
                await containerEl.requestFullscreen();
            }
        } catch (err) {
            // Fullscreen can fail due to permissions/user gesture restrictions
            console.warn('Fullscreen request failed:', err);
        }
    }

    /**
     * Update map data source
     */
    updateData(geojson) {
        const source = this.map.getSource('programs');
        if (source) {
            source.setData(geojson);
        }
    }

    /**
     * Fit map bounds to show all data
     */
    fitBoundsToData(data = null) {
        if (!data || data.features.length === 0) {
            // Fit to current source data
            const features = this.map.querySourceFeatures('programs', {
                sourceLayer: null,
                filter: ['!', ['has', 'point_count']]
            });

            if (features.length === 0) return;

            const bounds = new mapboxgl.LngLatBounds();
            features.forEach(feature => {
                bounds.extend(feature.geometry.coordinates);
            });

            this.map.fitBounds(bounds, {
                padding: this.getFitPadding(),
                maxZoom: 12,
                duration: 1500
            });
        } else {
            // Fit to provided GeoJSON data
            const bounds = new mapboxgl.LngLatBounds();
            data.features.forEach(feature => {
                bounds.extend(feature.geometry.coordinates);
            });

            this.map.fitBounds(bounds, {
                padding: this.getFitPadding(),
                maxZoom: 12,
                duration: 1500
            });
        }
    }

    /**
     * Reset map to initial view
     */
    reset() {
        this.map.flyTo({
            center: this.config.initialCenter,
            zoom: this.config.initialZoom,
            duration: 1500
        });
    }

    /**
     * Get map bounds
     */
    getBounds() {
        return this.map.getBounds();
    }

    /**
     * Get visible program count
     */
    getVisibleProgramCount() {
        if (!this.map.getSource('programs')) {
            return 0;
        }

        const features = this.map.querySourceFeatures('programs', {
            sourceLayer: null,
            filter: ['!', ['has', 'point_count']]
        });

        return features.length;
    }

    /**
     * Show map
     */
    show() {
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.classList.remove('hidden');
        }
    }

    /**
     * Hide map
     */
    hide() {
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.classList.add('hidden');
        }
    }

    /**
     * Set callback for search area
     */
    setSearchAreaCallback(callback) {
        this.onSearchArea = callback;
    }

    /**
     * Set callback for counter updates
     */
    setCounterUpdateCallback(callback) {
        this.onCounterUpdate = callback;
    }

    /**
     * Close all open popups currently displayed on the map
     */
    closeAllPopups() {
        if (this.activePopups.size > 0) {
            for (const popup of this.activePopups) {
                popup.remove();
            }
            this.activePopups.clear();
        }

        if (typeof document !== 'undefined') {
            document.querySelectorAll('.mapboxgl-popup').forEach((el) => el.remove());
        }
    }

    /**
     * Destroy map instance
     */
    destroy() {
        this.closeAllPopups();
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }
}
