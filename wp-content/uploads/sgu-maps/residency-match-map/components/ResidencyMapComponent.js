/**
 * ResidencyMapComponent - Main orchestrator following SOLID principles
 * Single Responsibility: Coordinate between components and manage application flow
 * Open/Closed: Open for extension via component composition
 * Liskov Substitution: Components can be swapped with compatible implementations
 * Interface Segregation: Each component has focused, specific interface
 * Dependency Inversion: Depends on abstractions (component interfaces) not implementations
 */
class ResidencyMapComponent {
    constructor(config) {
        this.config = {
            accessToken: config.accessToken,
            dataUrl: config.dataUrl || 'input/input.json',
            containerId: config.containerId || 'map',
            initialCenter: config.initialCenter || [-95.7129, 37.0902],
            initialZoom: config.initialZoom || 4,
            mapStyle: config.mapStyle || 'mapbox://styles/mapbox/light-v11'
        };

        // Initialize components (Dependency Injection)
        this.uiManager = new UIManager();
        this.dataManager = new DataManager(this.config.dataUrl);
        this.filterManager = new FilterManager(
            (filters) => this.handleFilterChange(filters),
            () => this.handleClearFilters(),
            () => this.handleGeoSearch()
        );
        this.paginationManager = new PaginationManager(() => this.handlePageChange());
        this.listViewRenderer = new ListViewRenderer(this.uiManager);
        this.mapManager = null; // Initialized async
    }

    /**
     * Initialize the application
     */
    async init() {
        try {
            this.uiManager.showLoading();

            // Load data
            await this.dataManager.loadData();
            
            // Extract and populate specialties
            const specialties = this.dataManager.extractSpecialties();
            this.filterManager.populateSpecialtyDropdown(specialties);

            // Populate state + program options for autocomplete
            const states = this.dataManager.extractStates();
            this.filterManager.populateStateDropdown(states);

            const programNames = this.dataManager.extractProgramNames();
            this.filterManager.populateProgramDropdown(programNames);

            // Initialize map
            this.mapManager = new MapManager(this.config, this.uiManager);
            await this.mapManager.initialize();

            // Setup map callbacks
            this.mapManager.setSearchAreaCallback((bounds) => this.handleSearchArea(bounds));
            this.mapManager.setCounterUpdateCallback(() => this.updateMapCounter());

            // Initialize UI components
            this.filterManager.initialize();
            this.paginationManager.initialize();
            this.uiManager.initializeViewToggle((view) => this.handleViewChange(view));

            // Initial data display
            this.updateDisplay();

            this.uiManager.hideLoading();
        } catch (error) {
            console.error('Error initializing application:', error);
            this.uiManager.showError('Failed to load map data');
        }
    }

    /**
     * Update map counter with visible programs
     */
    updateMapCounter() {
        if (this.uiManager.getCurrentView() === 'map' && this.mapManager) {
            const visibleCount = this.dataManager.getFilteredData().length;
            const programCount = document.getElementById('programCount');
            if (programCount) {
                programCount.textContent = visibleCount;
            }
        }
    }

    /**
     * Handle filter changes (Observer pattern)
     */
    handleFilterChange(filters) {
        this.mapManager?.closeAllPopups?.();

        const isMobile = typeof window !== 'undefined' &&
            typeof window.matchMedia === 'function' &&
            window.matchMedia('(max-width: 768px)').matches;

        const scrollResultsIntoView = () => {
            if (!isMobile) return;
            const view = this.uiManager.getCurrentView();
            const targetId = view === 'list' ? 'listView' : 'mapViewContainer';
            const el = document.getElementById(targetId);
            if (!el) return;
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        // Apply filters to data
        this.dataManager.applyFilters(filters);

        // Reset pagination to first page
        this.paginationManager.reset();

        // Update display
        this.updateDisplay();

        // Mobile UX: after EXPLORE, scroll down to results
        // Defer a tick so any view/layout changes settle first.
        setTimeout(scrollResultsIntoView, 0);

        // If a state/province is selected, zoom to it after EXPLORE
        if (this.uiManager.getCurrentView() === 'map' && this.mapManager && filters?.state) {
            const allItems = this.dataManager.getAllData()?.items || [];
            this.mapManager.zoomToStateRadius(filters.state, allItems, 50);
            return;
        }

        // If ONLY a program name was selected, zoom to that program on the map
        if (
            this.uiManager.getCurrentView() === 'map' &&
            this.mapManager &&
            filters?.programName &&
            !filters?.state &&
            !filters?.specialty
        ) {
            const filtered = this.dataManager.getFilteredData();
            this.mapManager.zoomToPrograms(filtered, { preferFitBounds: true });
            return;
        }

        // Fit map bounds if filters are active and in map view
        if (this.uiManager.getCurrentView() === 'map' && this.filterManager.hasActiveFilters()) {
            const geojson = this.dataManager.convertToGeoJSON();
            setTimeout(() => this.mapManager.fitBoundsToData(geojson), 300);
        }
    }

    /**
     * Handle view changes
     */
    handleViewChange(view) {
        this.uiManager.switchView(view);

        if (view === 'list') {
            this.paginationManager.reset();
            this.renderListView();
        } else if (view === 'map') {
            // If the map container was hidden, Mapbox needs a resize to render correctly
            if (this.mapManager?.map) {
                setTimeout(() => this.mapManager.map.resize(), 100);
            }
        }
    }

    /**
     * Handle page changes in list view
     */
    handlePageChange() {
        this.renderListView();
    }

    /**
     * Handle clear filters - reset map and data
     */
    handleClearFilters() {
        this.mapManager?.closeAllPopups?.();

        // Clear data filters
        this.dataManager.clearFilters();

        // Reset pagination
        this.paginationManager.reset();

        // Reset map to initial view
        if (this.mapManager) {
            this.mapManager.reset();
        }

        // Update display
        this.updateDisplay();
    }

    /**
     * Handle search current area
     */
    handleSearchArea(bounds) {
        // Filter data by bounds
        this.dataManager.filterByBounds(bounds);

        // Reset pagination
        this.paginationManager.reset();

        // Update display
        this.updateDisplay();
    }

    /**
     * Handle geolocation search from location arrow icon
     */
    async handleGeoSearch() {
        this.mapManager?.closeAllPopups?.();

        if (!navigator?.geolocation) {
            this.uiManager.showError('Geolocation is not supported by this browser');
            return;
        }

        try {
            const position = await this.getCurrentPosition();
            const latitude = Number(position?.coords?.latitude);
            const longitude = Number(position?.coords?.longitude);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                this.uiManager.showError('Unable to determine current location');
                return;
            }

            const currentFilters = this.filterManager.getCurrentFilters();
            this.dataManager.applyFilters({
                state: '',
                specialty: currentFilters.specialty,
                programName: currentFilters.programName
            });

            this.dataManager.filterByRadius(latitude, longitude, 50);
            this.paginationManager.reset();
            this.updateDisplay();

            if (this.mapManager) {
                await this.mapManager.fitRadiusBounds(longitude, latitude, 50);
            }
        } catch {
            this.uiManager.showError('Location access was denied or unavailable');
        }
    }

    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        });
    }

    /**
     * Update all displays (map and counters)
     */
    updateDisplay() {
        const filteredData = this.dataManager.getFilteredData();
        const geojson = this.dataManager.convertToGeoJSON(filteredData);

        // Update map
        if (this.mapManager) {
            this.mapManager.updateData(geojson);
        }

        // Update counters
        this.uiManager.updateCounters(filteredData.length);

        // Update list view if active
        if (this.uiManager.getCurrentView() === 'list') {
            this.renderListView();
        }
    }

    /**
     * Render list view with pagination
     */
    renderListView() {
        const filteredData = this.dataManager.getFilteredData();

        // Update pagination state
        this.paginationManager.update(filteredData.length);

        // Get paginated data
        const pageData = this.paginationManager.getPaginatedData(filteredData);

        // Render list
        this.listViewRenderer.render(pageData);
    }

    /**
     * Destroy and cleanup
     */
    destroy() {
        if (this.mapManager) {
            this.mapManager.destroy();
        }
    }
}

// Expose to global scope for WordPress integration
if (typeof window !== 'undefined') {
    window.ResidencyMapComponent = ResidencyMapComponent;
}
