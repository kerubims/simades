/**
 * Reusable Client-Side Pagination for Tables
 */

class TablePagination {
    constructor(tbodyId, searchInputId) {
        this.tbody = document.getElementById(tbodyId);
        this.searchInput = document.getElementById(searchInputId);
        
        if (!this.tbody) return;

        this.currentPage = 1;
        this.rowsPerPage = 10;
        
        // Exclude rows with colspan (e.g. empty messages)
        this.allRows = Array.from(this.tbody.querySelectorAll('tr')).filter(r => !r.querySelector('td[colspan]'));
        this.filteredRows = [...this.allRows];
        
        this.buildUI();
        this.update();
        
        if (this.searchInput) {
            // override the old filterTable from inline scripts to use this
            this.searchInput.addEventListener('input', () => this.filter());
        }
    }
    
    buildUI() {
        if (this.allRows.length === 0) return;
        
        // Find or create wrapper for pagination controls
        let wrapper = this.tbody.closest('.overflow-x-auto');
        if (!wrapper) wrapper = this.tbody.parentElement;
        
        const controlsHtml = `
            <div class="p-4 border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface-container-lowest pagination-controls">
                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span>Tampilkan</span>
                    <select class="rounded-lg border-outline-variant py-1 text-sm bg-surface per-page-select">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Semua</option>
                    </select>
                    <span>data</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <span class="text-sm text-on-surface-variant page-info">Menampilkan 0 dari 0</span>
                    <div class="flex gap-1">
                        <button class="p-1 rounded-lg hover:bg-surface-variant text-on-surface-variant disabled:opacity-50 disabled:cursor-not-allowed btn-prev">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button class="p-1 rounded-lg hover:bg-surface-variant text-on-surface-variant disabled:opacity-50 disabled:cursor-not-allowed btn-next">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        wrapper.insertAdjacentHTML('afterend', controlsHtml);
        
        // Get the inserted elements
        this.controlsEl = wrapper.nextElementSibling;
        this.selectEl = this.controlsEl.querySelector('.per-page-select');
        this.infoEl = this.controlsEl.querySelector('.page-info');
        this.btnPrev = this.controlsEl.querySelector('.btn-prev');
        this.btnNext = this.controlsEl.querySelector('.btn-next');
        
        // Add events
        this.selectEl.addEventListener('change', (e) => {
            this.rowsPerPage = e.target.value === 'all' ? (this.filteredRows.length || 1) : parseInt(e.target.value);
            this.currentPage = 1;
            this.update();
        });
        
        this.btnPrev.addEventListener('click', (e) => {
            e.preventDefault();
            if (this.currentPage > 1) {
                this.currentPage--;
                this.update();
            }
        });
        
        this.btnNext.addEventListener('click', (e) => {
            e.preventDefault();
            const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage);
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.update();
            }
        });
    }

    filter() {
        const q = this.searchInput.value.toLowerCase();
        this.filteredRows = this.allRows.filter(row => row.textContent.toLowerCase().includes(q));
        this.currentPage = 1;
        this.update();
    }
    
    update() {
        if (!this.controlsEl) return;
        
        const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage) || 1;
        if (this.currentPage > totalPages) this.currentPage = totalPages;
        
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        
        // Hide all original rows first
        this.allRows.forEach(row => row.style.display = 'none');
        
        // Handle empty search
        let emptyRow = this.tbody.querySelector('tr.empty-row');
        if (this.filteredRows.length === 0 && this.allRows.length > 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-row';
                const cols = this.allRows[0].querySelectorAll('td').length;
                emptyRow.innerHTML = `<td colspan="${cols}" class="p-6 text-center text-on-surface-variant">Data tidak ditemukan.</td>`;
                this.tbody.appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
        
        // Show paginated rows
        const visibleRows = this.filteredRows.slice(start, end);
        visibleRows.forEach(row => row.style.display = '');
        
        // Update UI info
        if (this.filteredRows.length === 0) {
            this.infoEl.textContent = 'Tidak ada data';
        } else {
            this.infoEl.textContent = `Menampilkan ${start + 1}-${Math.min(end, this.filteredRows.length)} dari ${this.filteredRows.length}`;
        }
        
        // Update Buttons
        this.btnPrev.disabled = this.currentPage === 1;
        this.btnNext.disabled = this.currentPage === totalPages || this.filteredRows.length === 0;
    }
}
