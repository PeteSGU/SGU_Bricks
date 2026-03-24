/**
 * UIManager - Handles UI utilities and helper functions
 * Single Responsibility: UI formatting, HTML generation, and DOM utilities
 */
class UIManager {
    constructor() {
        this.currentView = 'map';
    }

    /**
     * Initialize view toggle
     */
    initializeViewToggle(onViewChange) {
        document.querySelectorAll('.view-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
                const clickedBtn = e.currentTarget;
                clickedBtn.classList.add('active');
                this.currentView = clickedBtn.dataset.view;
                
                if (onViewChange) {
                    onViewChange(this.currentView);
                }
            });
        });
    }

    /**
     * Switch between views
     */
    switchView(view) {
        this.currentView = view;
        
        const mapContainer = document.getElementById('map');
        const mapViewContainer = document.getElementById('mapViewContainer');
        const listContainer = document.getElementById('listView');
        const programCounter = document.getElementById('programCounter');
        const listCounter = document.getElementById('listCounter');
        const mapControls = document.querySelector('.map-controls');
        const searchAreaBtn = document.getElementById('searchAreaBtn');

        if (view === 'list') {
            if (mapViewContainer) mapViewContainer.style.display = 'none';
            mapContainer?.classList.add('hidden');
            listContainer?.classList.add('active');
            if (programCounter) programCounter.style.display = 'none';
            if (listCounter) listCounter.style.display = 'block';
            if (mapControls) mapControls.style.display = 'none';
            if (searchAreaBtn) searchAreaBtn.style.display = 'none';
        } else {
            if (mapViewContainer) mapViewContainer.style.display = 'block';
            mapContainer?.classList.remove('hidden');
            listContainer?.classList.remove('active');
            if (programCounter) programCounter.style.display = 'block';
            if (listCounter) listCounter.style.display = 'none';
            if (mapControls) mapControls.style.display = 'flex';
            if (searchAreaBtn) searchAreaBtn.style.display = 'block';
        }
    }

    /**
     * Update program counters
     */
    updateCounters(count) {
        const programCount = document.getElementById('programCount');
        const listCount = document.getElementById('listCount');
        const headerCount = document.getElementById('headerCount');
        
        if (programCount) {
            programCount.textContent = count;
        }
        
        if (listCount) {
            listCount.textContent = count;
        }

        if (headerCount) {
            headerCount.textContent = count;
        }
    }

    /**
     * Create popup content HTML
     */
    createPopupContent(program) {
        const { name, location, specialities, residencyMatches } = program;
        const locationDisplay = this.formatLocation(location);
        
        const specialitiesHtml = this.formatSpecialities(specialities);
        const matchesHtml = this.formatResidencyMatches(residencyMatches);
        
        return `
            <div class="program-card">
                <div class="program-title">${this.escapeHtml(name)}</div>
                <div class="program-location">${this.escapeHtml(locationDisplay)}</div>
                ${specialitiesHtml}
                ${matchesHtml}
            </div>
        `;
    }

    formatLocation(location) {
        const city = String(location?.city || '').trim();
        const stateRaw = String(location?.state || '').trim();
        const stateDisplay = window.StateUtils?.toFullName ? window.StateUtils.toFullName(stateRaw) : stateRaw;

        const country = String(location?.country || '').trim();

        return [city, stateDisplay, country].filter(Boolean).join(', ');
    }

    /**
     * Format specialities for display
     */
    formatSpecialities(specialities) {
        if (!specialities || (Array.isArray(specialities) && specialities.length === 0)) {
            return '';
        }

        const list = Array.isArray(specialities) ? specialities : String(specialities).split(',').map(s => s.trim()).filter(Boolean);

        return `
            <div class="program-specialties">
                <div class="program-specialties-list">${this.escapeHtml(list.join(', '))}</div>
            </div>
        `;
    }

    /**
     * Format residency matches for display
     */
    formatResidencyMatches(residencyMatches) {
        const pgy1Matches = residencyMatches && residencyMatches.length > 0
        ? residencyMatches.filter(m => m.type && m.type.includes('PGY-1'))
            .reduce((sum, m) => sum + (m.count || 0), 0)
        : 0;

        return `
            <div class="program-matches">
                <div class="program-matches-title"> <span class="match-count">${pgy1Matches}</span> PGY-1 Residency Match Here</div>
            </div>
        `;
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        const loadingElement = document.getElementById('loading');
        if (loadingElement) {
            loadingElement.style.display = 'block';
            loadingElement.textContent = 'Loading map...';
            loadingElement.style.color = '#1A1B4B';
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoading() {
        const loadingElement = document.getElementById('loading');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        const loadingElement = document.getElementById('loading');
        if (loadingElement) {
            loadingElement.textContent = message;
            loadingElement.style.color = '#cc0000';
            loadingElement.style.display = 'block';
        }
    }

    /**
     * Get current view
     */
    getCurrentView() {
        return this.currentView;
    }
}
