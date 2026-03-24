/**
 * DataManager - Handles all data operations (loading, filtering, transformations)
 * Single Responsibility: Data management and business logic
 */
class DataManager {
    constructor(dataUrl) {
        this.dataUrl = dataUrl;
        this.data = null;
        this.filteredData = null;
        this.specialties = new Set();
    }

    /**
     * Load JSON data from file
     */
    async loadData() {
        try {
            const response = await fetch(this.dataUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.data = await response.json();
            // Normalize items so downstream components have consistent shapes
            if (Array.isArray(this.data?.items)) {
                this.data.items = this.data.items.map(item => this.normalizeProgram(item));
            } else {
                this.data.items = [];
            }

            this.filteredData = [...this.data.items];
            return this.data;
        } catch (error) {
            console.error('Error loading data:', error);
            throw error;
        }
    }

    /**
     * Normalize a program record from input JSON
     */
    normalizeProgram(program) {
        const normalizedState = this.normalizeState(program?.location?.state);
        const normalizedCountry = this.normalizeCountry(program?.location?.country);

        const normalized = { ...program };
        normalized.location = {
            city: program?.location?.city || '',
            state: normalizedState,
            country: normalizedCountry,
            lat: Number(program?.location?.lat),
            long: Number(program?.location?.long)
        };

        normalized.specialities = this.normalizeSpecialities(program?.specialities);
        normalized.residencyMatches = Array.isArray(program?.residencyMatches) ? program.residencyMatches : [];
        normalized.name = program?.name || '';

        return normalized;
    }

    normalizeState(value) {
        const raw = String(value || '').trim();
        if (!raw) return '';

        if (window.StateUtils?.toCode) {
            const code = window.StateUtils.toCode(raw);
            if (code) return code;
        }

        return raw;
    }

    normalizeCountry(countryValue) {
        const raw = String(countryValue || '').trim();
        if (!raw) return '';

        const lower = raw.toLowerCase();
        if (lower === 'usa' || lower === 'us' || lower === 'united states' || lower === 'united states of america') {
            return 'USA';
        }
        if (lower === 'canada' || lower === 'ca') {
            return 'Canada';
        }

        return raw;
    }

    /**
     * Normalize specialities to an array of strings
     */
    normalizeSpecialities(value) {
        if (!value) return [];

        if (Array.isArray(value)) {
            return value
                .flatMap(v => String(v).split(','))
                .map(s => s.trim())
                .filter(Boolean);
        }

        return String(value)
            .split(',')
            .map(s => s.trim())
            .filter(Boolean);
    }

    /**
     * Extract unique specialties from data
     */
    extractSpecialties() {
        if (!this.data) return [];

        this.data.items.forEach(program => {
            const specialties = this.normalizeSpecialities(program.specialities);
            specialties.forEach(s => this.specialties.add(s));
        });

        return Array.from(this.specialties).sort();
    }

    /**
     * Extract unique states (location.state) from data
     */
    extractStates() {
        if (!this.data) return [];

        const states = new Set();
        this.data.items.forEach(program => {
            const state = (program?.location?.state || '').trim();
            if (state) states.add(state);
        });

        return Array.from(states).sort();
    }

    /**
     * Extract unique program names from data
     */
    extractProgramNames() {
        if (!this.data) return [];

        const names = new Set();
        this.data.items.forEach(program => {
            const name = (program?.name || '').trim();
            if (name) names.add(name);
        });

        return Array.from(names).sort();
    }

    /**
     * Apply filters to data
     */
    applyFilters(filters) {
        const { state, specialty, programName } = filters;
        const stateQuery = String(state || '').trim();
        const stateQueryLower = stateQuery.toLowerCase();
        const stateQueryCode = window.StateUtils?.toCode ? window.StateUtils.toCode(stateQuery) : '';
        const stateQueryName = window.StateUtils?.toFullName ? window.StateUtils.toFullName(stateQuery).toLowerCase() : stateQueryLower;

        this.filteredData = this.data.items.filter(program => {
            // State filter
            const matchesState = !stateQuery || (() => {
                const programStateRaw = String(program?.location?.state || '').trim();
                const programStateCode = window.StateUtils?.toCode ? window.StateUtils.toCode(programStateRaw) : '';
                const programStateName = window.StateUtils?.toFullName
                    ? window.StateUtils.toFullName(programStateRaw).toLowerCase()
                    : programStateRaw.toLowerCase();

                if (stateQueryCode) {
                    return programStateCode === stateQueryCode;
                }

                return programStateName.includes(stateQueryName) || programStateRaw.toLowerCase().includes(stateQueryLower);
            })();

            // Specialty filter
            const matchesSpecialty = !specialty ||
                (Array.isArray(program.specialities) && program.specialities.some(s => s.toLowerCase().includes(specialty.toLowerCase())));

            // Program name filter
            const matchesProgram = !programName ||
                program.name.toLowerCase().includes(programName.toLowerCase());

            return matchesState && matchesSpecialty && matchesProgram;
        });

        return this.filteredData;
    }

    /**
     * Filter by map bounds
     */
    filterByBounds(bounds) {
        const base = Array.isArray(this.filteredData) ? this.filteredData : this.data.items;

        this.filteredData = base.filter(program => {
            return bounds.contains([program.location.long, program.location.lat]);
        });

        return this.filteredData;
    }

    /**
     * Filter programs by distance from a center point
     */
    filterByRadius(centerLat, centerLng, radiusMiles) {
        const base = Array.isArray(this.filteredData) ? this.filteredData : this.data.items;

        const targetLat = Number(centerLat);
        const targetLng = Number(centerLng);
        const radius = Number(radiusMiles);

        if (!Number.isFinite(targetLat) || !Number.isFinite(targetLng) || !Number.isFinite(radius) || radius <= 0) {
            this.filteredData = [];
            return this.filteredData;
        }

        this.filteredData = base.filter(program => {
            const lat = Number(program?.location?.lat);
            const lng = Number(program?.location?.long);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return false;

            return this.calculateDistanceMiles(targetLat, targetLng, lat, lng) <= radius;
        });

        return this.filteredData;
    }

    calculateDistanceMiles(lat1, lng1, lat2, lng2) {
        const toRadians = (deg) => (deg * Math.PI) / 180;
        const earthRadiusMiles = 3958.8;

        const dLat = toRadians(lat2 - lat1);
        const dLng = toRadians(lng2 - lng1);

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return earthRadiusMiles * c;
    }

    /**
     * Reset filters
     */
    clearFilters() {
        this.filteredData = [...this.data.items];
        return this.filteredData;
    }

    /**
     * Get filtered data
     */
    getFilteredData() {
        return this.filteredData;
    }

    /**
     * Get all data
     */
    getAllData() {
        return this.data;
    }

    /**
     * Get specialties
     */
    getSpecialties() {
        return Array.from(this.specialties).sort();
    }

    /**
     * Convert data to GeoJSON format
     */
    convertToGeoJSON(data = this.filteredData) {
        return {
            type: 'FeatureCollection',
            features: data.map(program => ({
                type: 'Feature',
                properties: {
                    name: program.name,
                    city: program.location.city,
                    state: program.location.state,
                    country: program.location.country,
                    specialities: JSON.stringify(program.specialities),
                    residencyMatches: JSON.stringify(program.residencyMatches)
                },
                geometry: {
                    type: 'Point',
                    coordinates: [program.location.long, program.location.lat]
                }
            }))
        };
    }

    /**
     * Validate location coordinates
     */
    isValidLocation(location) {
        return location && 
               typeof location.lat === 'number' && 
               typeof location.long === 'number' &&
               !isNaN(location.lat) && 
               !isNaN(location.long) &&
               location.lat >= -90 && 
               location.lat <= 90 &&
               location.long >= -180 && 
               location.long <= 180;
    }
}
