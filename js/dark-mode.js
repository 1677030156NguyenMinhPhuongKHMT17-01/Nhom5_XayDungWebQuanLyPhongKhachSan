/**
 * Optimized Dark Mode Toggle - BTL Hotel Management System
 * Simplified and lightweight implementation
 */

class ThemeManager {
    constructor() {
        this.storageKey = 'btl-theme';
        this.init();
    }

    init() {
        this.loadTheme();
        this.bindEvents();
    }

    loadTheme() {
        const theme = localStorage.getItem(this.storageKey) || 'light';
        this.setTheme(theme, false);
    }

    setTheme(theme, save = true) {
        // Update DOM
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
        
        // Update toggle icons
        this.updateIcons(theme);
        
        // Save to localStorage
        if (save) {
            localStorage.setItem(this.storageKey, theme);
        }
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    toggleTheme() {
        const current = this.getCurrentTheme();
        const newTheme = current === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    updateIcons(theme) {
        const icons = document.querySelectorAll('#theme-toggle i, .theme-toggle i');
        const iconClass = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
        const title = theme === 'dark' ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối';
        
        icons.forEach(icon => {
            icon.className = iconClass;
            icon.setAttribute('title', title);
        });
    }

    bindEvents() {
        // Handle theme toggle clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('#theme-toggle, .theme-toggle')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Handle keyboard shortcuts (Ctrl/Cmd + D)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                this.toggleTheme();
            }
        });
    }
}

// Initialize theme manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeManager = new ThemeManager();
    });
} else {
    window.themeManager = new ThemeManager();
}