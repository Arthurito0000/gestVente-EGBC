/**
 * Searchable Select Component
 * Transforme un select standard en un champ de recherche avec suggestions
 * Support de la navigation au clavier (↑↓ + Enter)
 */

class SearchableSelect {
    constructor(selectElement, options = {}) {
        this.select = selectElement;
        this.options = {
            placeholder: options.placeholder || 'Rechercher...',
            noResultsText: options.noResultsText || 'Aucun résultat',
            searchFields: options.searchFields || ['text', 'value'],
            onChange: options.onChange || null,
            minChars: options.minChars || 0
        };
        
        this.isOpen = false;
        this.selectedIndex = -1;
        this.filteredOptions = [];
        
        this.init();
    }
    
    init() {
        // Cacher le select original
        this.select.style.display = 'none';
        
        // Créer la structure HTML
        this.createWrapper();
        this.createInput();
        this.createDropdown();
        
        // Extraire les options du select
        this.parseOptions();
        
        // Attacher les événements
        this.attachEvents();
        
        // Initialiser avec la valeur sélectionnée
        this.updateInputFromSelect();
    }
    
    createWrapper() {
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'searchable-select-wrapper';
        this.wrapper.style.position = 'relative';
        this.wrapper.style.width = '100%';
        
        this.select.parentNode.insertBefore(this.wrapper, this.select);
        this.wrapper.appendChild(this.select);
    }
    
    createInput() {
        this.input = document.createElement('input');
        this.input.type = 'text';
        this.input.placeholder = this.options.placeholder;
        this.input.className = this.select.className;
        this.input.autocomplete = 'off';
        
        this.wrapper.appendChild(this.input);
    }
    
    createDropdown() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'searchable-select-dropdown';
        this.dropdown.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        `;
        
        this.wrapper.appendChild(this.dropdown);
    }
    
    parseOptions() {
        this.allOptions = Array.from(this.select.options)
            .filter(opt => opt.value !== '')
            .map(opt => ({
                value: opt.value,
                text: opt.textContent,
                sku: opt.getAttribute('data-sku') || opt.textContent.match(/\(([^)]+)\)/)?.[1] || '',
                name: opt.getAttribute('data-name') || opt.textContent.split('(')[0].trim(),
                price: opt.getAttribute('data-price') || '',
                element: opt
            }));
    }
    
    attachEvents() {
        // Input events
        this.input.addEventListener('input', () => this.handleInput());
        this.input.addEventListener('focus', () => this.open());
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        
        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.close();
            }
        });
    }
    
    handleInput() {
        const query = this.input.value.toLowerCase().trim();
        
        if (query.length < this.options.minChars) {
            this.filteredOptions = [...this.allOptions];
        } else {
            this.filteredOptions = this.allOptions.filter(opt => {
                return opt.text.toLowerCase().includes(query) ||
                       opt.sku.toLowerCase().includes(query) ||
                       opt.name.toLowerCase().includes(query);
            });
        }
        
        this.selectedIndex = -1;
        this.renderDropdown();
        this.open();
    }
    
    handleKeydown(e) {
        if (!this.isOpen) {
            if (e.key === 'ArrowDown' || e.key === 'Enter') {
                this.open();
                e.preventDefault();
            }
            return;
        }
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredOptions.length - 1);
                this.updateHighlight();
                this.scrollToSelected();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                this.updateHighlight();
                this.scrollToSelected();
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0 && this.filteredOptions[this.selectedIndex]) {
                    this.selectOption(this.filteredOptions[this.selectedIndex]);
                }
                break;
                
            case 'Escape':
                this.close();
                break;
        }
    }
    
    renderDropdown() {
        if (this.filteredOptions.length === 0) {
            this.dropdown.innerHTML = `
                <div style="padding: 0.75rem; text-align: center; color: #6b7280;">
                    ${this.options.noResultsText}
                </div>
            `;
            return;
        }
        
        this.dropdown.innerHTML = '';
        
        this.filteredOptions.forEach((opt, index) => {
            const item = document.createElement('div');
            item.className = 'searchable-select-item';
            item.style.cssText = `
                padding: 0.75rem;
                cursor: pointer;
                transition: background-color 0.15s;
            `;
            
            // Mise en évidence du SKU et du nom
            item.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-weight: 600;">${this.highlightMatch(opt.name)}</span>
                        <span style="color: #6b7280; font-size: 0.875rem; margin-left: 0.5rem;">(${this.highlightMatch(opt.sku)})</span>
                    </div>
                    ${opt.price ? `<span style="color: #059669; font-weight: 600;">${opt.price} Fcfa</span>` : ''}
                </div>
            `;
            
            item.addEventListener('click', () => this.selectOption(opt));
            item.addEventListener('mouseenter', () => {
                this.selectedIndex = index;
                this.updateHighlight();
            });
            
            this.dropdown.appendChild(item);
        });
    }
    
    highlightMatch(text) {
        const query = this.input.value.toLowerCase().trim();
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark style="background-color: #fef3c7; padding: 0;">$1</mark>');
    }
    
    updateHighlight() {
        const items = this.dropdown.querySelectorAll('.searchable-select-item');
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.style.backgroundColor = '#eff6ff';
                item.style.color = '#1e40af';
            } else {
                item.style.backgroundColor = 'white';
                item.style.color = 'inherit';
            }
        });
    }
    
    scrollToSelected() {
        const items = this.dropdown.querySelectorAll('.searchable-select-item');
        if (items[this.selectedIndex]) {
            items[this.selectedIndex].scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }
    
    selectOption(option) {
        // Mettre à jour le select original
        this.select.value = option.value;
        
        // Mettre à jour l'input
        this.input.value = `${option.name} (${option.sku})`;
        
        // Déclencher l'événement change sur le select original
        this.select.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Callback personnalisé
        if (this.options.onChange) {
            this.options.onChange(option);
        }
        
        this.close();
    }
    
    updateInputFromSelect() {
        const selectedOption = this.allOptions.find(opt => opt.value === this.select.value);
        if (selectedOption) {
            this.input.value = `${selectedOption.name} (${selectedOption.sku})`;
        }
    }
    
    open() {
        this.isOpen = true;
        this.dropdown.style.display = 'block';
        
        if (this.filteredOptions.length === 0) {
            this.filteredOptions = [...this.allOptions];
            this.renderDropdown();
        }
    }
    
    close() {
        this.isOpen = false;
        this.dropdown.style.display = 'none';
        this.selectedIndex = -1;
        
        // Restaurer la valeur sélectionnée si l'input est vide
        if (!this.input.value.trim()) {
            this.select.value = '';
            this.select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
    
    destroy() {
        this.wrapper.parentNode.insertBefore(this.select, this.wrapper);
        this.wrapper.remove();
        this.select.style.display = '';
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', function() {
    const searchableSelects = document.querySelectorAll('[data-searchable]');
    searchableSelects.forEach(select => {
        new SearchableSelect(select, {
            placeholder: select.getAttribute('data-placeholder') || 'Rechercher un produit...',
            noResultsText: 'Aucun produit trouvé'
        });
    });
});

// Export pour utilisation manuelle
window.SearchableSelect = SearchableSelect;
