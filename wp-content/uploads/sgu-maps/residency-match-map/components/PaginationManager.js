/**
 * PaginationManager - Handles pagination state and UI
 * Single Responsibility: Pagination logic and controls
 */
class PaginationManager {
    constructor(onPageChange) {
        this.onPageChange = onPageChange; // Callback when page changes
        this.currentPage = 1;
        this.itemsPerPage = 4;
        this.totalItems = 0;
    }

    /**
     * Initialize pagination event listeners
     */
    initialize() {
        this.setupEventListeners();
    }

    /**
     * Setup pagination event listeners
     */
    setupEventListeners() {
        // Previous button
        const prevBtn = document.getElementById('prevPageBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousPage());
        }

        // Next button
        const nextBtn = document.getElementById('nextPageBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        // Items per page selector
        const itemsSelect = document.getElementById('itemsPerPageSelect');
        if (itemsSelect) {
            itemsSelect.addEventListener('change', (e) => {
                this.setItemsPerPage(parseInt(e.target.value));
            });
        }

        // Page number clicks (event delegation)
        const pageNumbers = document.getElementById('pageNumbers');
        if (pageNumbers) {
            pageNumbers.addEventListener('click', (e) => {
                if (e.target.classList.contains('pagination-btn')) {
                    const page = parseInt(e.target.dataset.page);
                    if (!isNaN(page)) {
                        this.goToPage(page);
                    }
                }
            });
        }
    }

    /**
     * Update pagination state and render
     */
    update(totalItems) {
        this.totalItems = totalItems;
        this.render();
    }

    /**
     * Render pagination UI
     */
    render() {
        const totalPages = this.getTotalPages();
        const startIndex = this.getStartIndex();
        const endIndex = this.getEndIndex();

        // Update info text
        this.updateInfoText(startIndex, endIndex);

        // Update page numbers
        this.updatePageNumbers(totalPages);

        // Update prev/next buttons
        this.updateNavigationButtons(totalPages);
    }

    /**
     * Update pagination info text
     */
    updateInfoText(startIndex, endIndex) {
        const paginationInfo = document.getElementById('paginationInfo');
        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${startIndex + 1}-${endIndex} of ${this.totalItems} results`;
        }
    }

    /**
     * Update page number buttons
     */
    updatePageNumbers(totalPages) {
        const pageNumbers = document.getElementById('pageNumbers');
        if (pageNumbers) {
            pageNumbers.innerHTML = this.createPageNumbersHTML(totalPages);
        }
    }

    /**
     * Create page numbers HTML
     */
    createPageNumbersHTML(totalPages) {
        let pages = [];

        if (totalPages <= 5) {
            // Show all pages if 5 or less
            pages = Array.from({ length: totalPages }, (_, i) => i + 1);
        } else {
            // Show: 1 (first), prev, current, next, last
            pages = [1];
            
            // Add previous page if not at start
            if (this.currentPage > 2) {
                if (this.currentPage > 3) {
                    pages.push('...');
                }
                pages.push(this.currentPage - 1);
            }
            
            // Add current page if not first or last
            if (this.currentPage !== 1 && this.currentPage !== totalPages) {
                pages.push(this.currentPage);
            }
            
            // Add next page if not at end
            if (this.currentPage < totalPages - 1) {
                pages.push(this.currentPage + 1);
                if (this.currentPage < totalPages - 2) {
                    pages.push('...');
                }
            }
            
            // Always show last page
            if (totalPages > 1) {
                pages.push(totalPages);
            }
        }

        return pages.map(page => {
            if (page === '...') {
                return '<span style="padding: 0 4px; color: #999;">...</span>';
            }
            const isActive = page === this.currentPage ? 'active' : '';
            return `<button class="pagination-btn ${isActive}" data-page="${page}">${page}</button>`;
        }).join('');
    }

    /**
     * Update navigation buttons state
     */
    updateNavigationButtons(totalPages) {
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        
        if (prevBtn) {
            prevBtn.disabled = this.currentPage === 1;
        }
        
        if (nextBtn) {
            nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
        }
    }

    /**
     * Go to specific page
     */
    goToPage(pageNumber) {
        const totalPages = this.getTotalPages();
        
        if (pageNumber < 1 || pageNumber > totalPages) return;
        
        this.currentPage = pageNumber;
        this.render();

        if (this.onPageChange) {
            this.onPageChange(this.currentPage);
        }
    }

    /**
     * Go to previous page
     */
    previousPage() {
        this.goToPage(this.currentPage - 1);
    }

    /**
     * Go to next page
     */
    nextPage() {
        this.goToPage(this.currentPage + 1);
    }

    /**
     * Set items per page
     */
    setItemsPerPage(count) {
        this.itemsPerPage = count;
        this.currentPage = 1;
        this.render();

        if (this.onPageChange) {
            this.onPageChange(this.currentPage);
        }
    }

    /**
     * Reset to first page
     */
    reset() {
        this.currentPage = 1;
    }

    /**
     * Get total pages
     */
    getTotalPages() {
        return Math.ceil(this.totalItems / this.itemsPerPage);
    }

    /**
     * Get start index for current page
     */
    getStartIndex() {
        return (this.currentPage - 1) * this.itemsPerPage;
    }

    /**
     * Get end index for current page
     */
    getEndIndex() {
        return Math.min(this.getStartIndex() + this.itemsPerPage, this.totalItems);
    }

    /**
     * Get paginated data
     */
    getPaginatedData(data) {
        const start = this.getStartIndex();
        const end = this.getEndIndex();
        return data.slice(start, end);
    }
}
