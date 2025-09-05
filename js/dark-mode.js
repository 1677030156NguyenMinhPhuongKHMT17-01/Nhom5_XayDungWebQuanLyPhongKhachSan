/**
 * Dark Mode Toggle Functionality
 * BTL Hotel Management System
 */

class ThemeManager {
    constructor() {
        this.init();
    }

    init() {
        // Khởi tạo theme từ localStorage hoặc mặc định
        this.loadTheme();
        
        // Tạo toggle button
        this.createToggleButton();
        
        // Bind events
        this.bindEvents();
        
        console.log('Theme Manager initialized');
    }

    loadTheme() {
        const savedTheme = localStorage.getItem('btl-theme') || 'light';
        this.setTheme(savedTheme, false);
    }

    saveTheme(theme) {
        localStorage.setItem('btl-theme', theme);
    }

    setTheme(theme, save = true) {
        const htmlElement = document.documentElement;
        
        if (theme === 'dark') {
            htmlElement.setAttribute('data-theme', 'dark');
        } else {
            htmlElement.removeAttribute('data-theme');
        }
        
        // Cập nhật icon
        this.updateToggleIcon(theme);
        
        // Lưu vào localStorage
        if (save) {
            this.saveTheme(theme);
        }
        
        // Dispatch event để các component khác có thể lắng nghe
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { theme } 
        }));
    }

    getCurrentTheme() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }

    toggleTheme() {
        const currentTheme = this.getCurrentTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
        
        // Add animation class for smooth transition
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }

    createToggleButton() {
        // Tìm navbar để thêm button
        const navbar = document.querySelector('.navbar .container-fluid');
        if (!navbar) return;

        // Tạo button toggle
        const toggleButton = document.createElement('button');
        toggleButton.className = 'theme-toggle btn ms-2';
        toggleButton.id = 'theme-toggle';
        toggleButton.setAttribute('aria-label', 'Toggle dark mode');
        toggleButton.setAttribute('title', 'Chuyển đổi giao diện tối/sáng');
        
        // Tạo icon container
        const iconContainer = document.createElement('span');
        iconContainer.className = 'theme-icon';
        toggleButton.appendChild(iconContainer);
        
        // Thêm vào navbar (bên cạnh user info)
        const navbarNav = navbar.querySelector('.navbar-nav:last-child');
        if (navbarNav) {
            const li = document.createElement('li');
            li.className = 'nav-item d-flex align-items-center';
            li.appendChild(toggleButton);
            navbarNav.appendChild(li);
        } else {
            // Fallback: thêm trực tiếp vào container
            navbar.appendChild(toggleButton);
        }
        
        // Set initial icon
        this.updateToggleIcon(this.getCurrentTheme());
    }

    updateToggleIcon(theme) {
        const iconContainer = document.querySelector('#theme-toggle .theme-icon');
        if (!iconContainer) return;
        
        if (theme === 'dark') {
            iconContainer.innerHTML = '<i class="fas fa-sun" title="Chuyển sang giao diện sáng"></i>';
        } else {
            iconContainer.innerHTML = '<i class="fas fa-moon" title="Chuyển sang giao diện tối"></i>';
        }
    }

    bindEvents() {
        // Event listener cho toggle button
        document.addEventListener('click', (e) => {
            if (e.target.closest('#theme-toggle')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Keyboard support
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Shift + T để toggle theme
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Listen for system theme changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addListener((e) => {
                // Chỉ áp dụng system theme nếu user chưa có preference
                if (!localStorage.getItem('btl-theme')) {
                    this.setTheme(e.matches ? 'dark' : 'light', false);
                }
            });
        }
    }

    // Public methods for external use
    getDarkMode() {
        return this.getCurrentTheme() === 'dark';
    }

    enableDarkMode() {
        this.setTheme('dark');
    }

    enableLightMode() {
        this.setTheme('light');
    }

    resetToSystemTheme() {
        localStorage.removeItem('btl-theme');
        const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.setTheme(systemDark ? 'dark' : 'light', false);
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Khởi tạo Theme Manager
    window.themeManager = new ThemeManager();
    
    // Thêm một số utility functions global
    window.toggleDarkMode = () => window.themeManager.toggleTheme();
    window.isDarkMode = () => window.themeManager.getDarkMode();
    
    console.log('Dark mode functionality loaded');
});

// Export for modules (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeManager;
}
