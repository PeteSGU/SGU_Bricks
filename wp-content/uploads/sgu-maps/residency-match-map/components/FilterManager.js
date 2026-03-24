/**
 * FilterManager - Handles filter UI and state management
 * Single Responsibility: Filter interaction and DOM manipulation
 */
class FilterManager {
    constructor(onFilterChange, onClearFilters, onGeoSearch) {
        this.onFilterChange = onFilterChange; // Callback when filters change
        this.onClearFilters = onClearFilters; // Callback when filters cleared
        this.onGeoSearch = onGeoSearch; // Callback when location icon is clicked
        this.currentFilters = {
            state: '',
            specialty: '',
            programName: ''
        };

        this._states = [];
        this._specialties = [];
        this._programs = [];
    }

    /**
     * Initialize filter UI and event listeners
     */
    initialize() {
        this.setupEventListeners();
        this.setupAutocomplete();
    }

    setupAutocomplete() {
        this.setupAutocompleteInput({
            inputId: 'stateFilter',
            dropdownId: 'stateDropdown',
            getItems: () => this._states,
            getLabel: (code) => {
                if (window.StateUtils?.toFullName) return window.StateUtils.toFullName(code);
                return String(code);
            },
            onSelect: (code, label) => {
                const input = document.getElementById('stateFilter');
                if (!input) return;
                input.value = label;
                input.dataset.stateCode = code;
            },
            onInput: () => {
                const input = document.getElementById('stateFilter');
                if (input) input.dataset.stateCode = '';
            }
        });

        this.setupAutocompleteInput({
            inputId: 'specialtyFilter',
            dropdownId: 'specialtyDropdown',
            getItems: () => this._specialties,
            getLabel: (value) => String(value),
            onSelect: (_value, label) => {
                const input = document.getElementById('specialtyFilter');
                if (!input) return;
                input.value = label;
            }
        });

        this.setupAutocompleteInput({
            inputId: 'programFilter',
            dropdownId: 'programDropdown',
            getItems: () => this._programs,
            getLabel: (value) => String(value),
            onSelect: (_value, label) => {
                const input = document.getElementById('programFilter');
                if (!input) return;
                input.value = label;
            }
        });
    }

    setupAutocompleteInput({ inputId, dropdownId, getItems, getLabel, onSelect, onInput }) {
        const input = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        if (!input || !dropdown) return;

        const close = () => dropdown.classList.remove('open');

        const render = () => {
            const allItems = getItems() || [];
            const q = String(input.value || '').trim().toLowerCase();
            const filtered = q
                ? allItems.filter(v => getLabel(v).toLowerCase().includes(q))
                : allItems;

            const limited = filtered.slice(0, 200);
            dropdown.innerHTML = limited
                .map(v => {
                    const label = this.escapeHtml(getLabel(v));
                    const value = this.escapeHtml(String(v));
                    return `<div class="autocomplete-item" data-value="${value}" data-label="${label}">${label}</div>`;
                })
                .join('');

            if (limited.length > 0) dropdown.classList.add('open');
            else close();
        };

        input.addEventListener('focus', () => render());
        input.addEventListener('input', () => {
            if (onInput) onInput();
            render();
        });

        // Prevent input blur from closing before click registers
        dropdown.addEventListener('mousedown', (e) => e.preventDefault());
        dropdown.addEventListener('click', (e) => {
            const item = e.target.closest('.autocomplete-item');
            if (!item) return;
            const value = item.getAttribute('data-value') || '';
            const label = item.getAttribute('data-label') || value;
            close();
            if (onSelect) onSelect(value, label);
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.classList.contains('open')) return;
            if (e.target === input || dropdown.contains(e.target)) return;
            close();
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text ?? '');
        return div.innerHTML;
    }

    /**
     * Setup filter event listeners
     */
    setupEventListeners() {
        // Explore button
        const exploreBtn = document.getElementById('exploreBtn');
        if (exploreBtn) {
            exploreBtn.addEventListener('click', () => this.applyFilters());
        }

        // Clear filters button
        const clearBtn = document.getElementById('clearBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearFilters());
        }

        // Enter key support for text inputs
        const stateFilter = document.getElementById('stateFilter');
        const programFilter = document.getElementById('programFilter');
        const specialtyFilter = document.getElementById('specialtyFilter');
        
        if (stateFilter) {
            stateFilter.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.applyFilters();
            });
        }
        
        if (programFilter) {
            programFilter.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.applyFilters();
            });
        }

        if (specialtyFilter) {
            specialtyFilter.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.applyFilters();
            });
        }

        const locationArrowBtn = document.querySelector('.filter-input-icon .fa-location-arrow');
        if (locationArrowBtn) {
            locationArrowBtn.addEventListener('click', async () => {
                if (typeof this.onGeoSearch === 'function') {
                    await this.onGeoSearch();
                }
            });
        }
    }

    /**
     * Apply filters and trigger callback
     */
    applyFilters() {
        const stateEl = document.getElementById('stateFilter');
        const stateCode = stateEl?.dataset?.stateCode;

        this.currentFilters = {
            state: stateCode || stateEl?.value || '',
            specialty: document.getElementById('specialtyFilter')?.value || '',
            programName: document.getElementById('programFilter')?.value || ''
        };

        if (this.onFilterChange) {
            this.onFilterChange(this.currentFilters);
        }
    }

    /**
     * Clear all filters
     */
    clearFilters() {
        const stateEl = document.getElementById('stateFilter');
        if (stateEl) {
            stateEl.value = '';
            stateEl.dataset.stateCode = '';
        }
        document.getElementById('specialtyFilter').value = '';
        document.getElementById('programFilter').value = '';

        this.currentFilters = {
            state: '',
            specialty: '',
            programName: ''
        };

        if (this.onClearFilters) {
            this.onClearFilters();
        } else if (this.onFilterChange) {
            this.onFilterChange(this.currentFilters);
        }
    }

    /**
     * Populate specialty dropdown
     */
    populateSpecialtyDropdown(specialties) {
        this._specialties = Array.isArray(specialties) ? specialties : [];
    }

    /**
     * Populate state autocomplete options
     */
    populateStateDropdown(states) {
        this._states = Array.isArray(states) ? states : [];
    }

    /**
     * Populate program autocomplete options
     */
    populateProgramDropdown(programNames) {
        this._programs = Array.isArray(programNames) ? programNames : [];
    }

    /**
     * Get current filter values
     */
    getCurrentFilters() {
        return { ...this.currentFilters };
    }

    /**
     * Check if any filters are active
     */
    hasActiveFilters() {
        return this.currentFilters.state !== '' || 
               this.currentFilters.specialty !== '' || 
               this.currentFilters.programName !== '';
    }
}
