/**
 * ListViewRenderer - Handles list view rendering and display
 * Single Responsibility: List view UI generation
 */
class ListViewRenderer {
    constructor(uiManager) {
        this.uiManager = uiManager; // Reference to UIManager for shared utilities
    }

    /**
     * Render list view with given data
     */
    render(data) {
        const listContent = document.getElementById('listContent');
        if (!listContent) return;

        if (data.length === 0) {
            listContent.innerHTML = this.createEmptyStateHTML();
        } else {
            listContent.innerHTML = data.map(program => this.createProgramCard(program)).join('');
        }

        // Scroll to top
        const listView = document.getElementById('listView');
        if (listView) {
            listView.scrollTop = 0;
        }
    }

    /**
     * Create HTML for a program card
     */
    createProgramCard(program) {
        const { name, location, specialities, residencyMatches } = program;
        
        // Format specialties
        const specialtiesArray = Array.isArray(specialities)
            ? specialities
            : (specialities ? String(specialities).split(',').map(s => s.trim()).filter(Boolean) : []);

        const specialtiesList = specialtiesArray.length > 0 ? specialtiesArray.join(', ') : 'Not specified';

        // Format matches
        const pgy1Matches = residencyMatches && residencyMatches.length > 0
            ? residencyMatches.filter(m => m.type && m.type.includes('PGY-1'))
                .reduce((sum, m) => sum + (m.count || 0), 0)
            : 0;

        const matchesHtml = pgy1Matches > 0
            ? `<div class="program-matches"><span class="match-count">${pgy1Matches}</span> PGY-1 residents matched here</div>`
            : '';

        return `
            <div class="program-card">
                <div class="program-title">${this.uiManager.escapeHtml(name)}</div>
                <div class="program-location">${this.uiManager.escapeHtml(this.uiManager.formatLocation(location))}</div>
                <div class="program-specialties">
                    <div class="program-specialties-list">${this.uiManager.escapeHtml(specialtiesList)}</div>
                </div>
                ${matchesHtml}
            </div>
        `;
    }

    /**
     * Create empty state HTML
     */
    createEmptyStateHTML() {
        return `
            <div style="text-align: center; padding: 60px 20px; color: #666;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">No programs found</div>
                <div style="font-size: 14px;">Try adjusting your filters to see more results</div>
            </div>
        `;
    }

    /**
     * Show list view
     */
    show() {
        const listContainer = document.getElementById('listView');
        if (listContainer) {
            listContainer.classList.add('active');
        }
    }

    /**
     * Hide list view
     */
    hide() {
        const listContainer = document.getElementById('listView');
        if (listContainer) {
            listContainer.classList.remove('active');
        }
    }
}
