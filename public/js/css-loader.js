/**
 * Dynamic CSS Loader for SNIA Conference System
 * Loads role-specific and view-specific CSS based on body classes
 */

class CSSLoader {
    constructor() {
        this.loadedCSS = new Set();
        this.cssCache = new Map();
        this.init();
    }

    init() {
        // DISABLE DYNAMIC CSS LOADING TO PREVENT FOUC
        console.log('CSS Loader: Dynamic loading disabled to prevent FOUC');
        // this.autoLoadCSS();
        // this.watchClassChanges();
    }

    autoLoadCSS() {
        const body = document.body;
        const classList = Array.from(body.classList);
        
        // Extract role and view from classes
        const role = this.extractRole(classList);
        const view = this.extractView(classList);
        
        console.log('CSS Loader: Detected role:', role, 'view:', view);
        
        // Load role-specific CSS
        if (role) {
            this.loadRoleCSS(role);
        }
        
        // Load view-specific CSS  
        if (view) {
            this.loadViewCSS(view);
        }
    }

    extractRole(classList) {
        const roleClasses = ['admin', 'presenter', 'reviewer', 'audience'];
        
        for (const className of classList) {
            for (const role of roleClasses) {
                if (className.includes(role)) {
                    return role;
                }
            }
        }
        
        return null;
    }

    extractView(classList) {
        const viewClasses = ['auth', 'dashboard', 'events', 'registrations', 'payments', 'certificates'];
        
        for (const className of classList) {
            for (const view of viewClasses) {
                if (className.includes(view)) {
                    return view;
                }
            }
        }
        
        // Check if we're on a specific page based on URL
        const path = window.location.pathname;
        
        if (path.includes('login') || path.includes('register')) {
            return 'auth';
        } else if (path.includes('event') || path.includes('schedule')) {
            return 'events';
        } else if (path.includes('registration')) {
            return 'registrations';
        } else if (path.includes('payment')) {
            return 'payments';
        } else if (path.includes('certificate')) {
            return 'certificates';
        } else if (path.includes('dashboard') || path === '/') {
            return 'dashboard';
        }
        
        return null;
    }

    async loadRoleCSS(role) {
        const cssPath = `/css/roles/${role}/${role}.css`;
        await this.loadCSS(cssPath, `role-${role}`);
    }

    async loadViewCSS(view) {
        const cssPath = `/css/views/${view}/${view}.css`;
        await this.loadCSS(cssPath, `view-${view}`);
    }

    async loadCSS(cssPath, id) {
        // Check if already loaded
        if (this.loadedCSS.has(id)) {
            console.log('CSS Loader: Already loaded', id);
            return;
        }

        try {
            // Check if CSS file exists
            const response = await fetch(cssPath, { method: 'HEAD' });
            
            if (!response.ok) {
                console.warn('CSS Loader: CSS file not found:', cssPath);
                return;
            }

            // Create and append link element
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = cssPath;
            link.id = id;
            
            // Add to head
            document.head.appendChild(link);
            
            // Mark as loaded
            this.loadedCSS.add(id);
            
            console.log('CSS Loader: Loaded', cssPath);
            
            // Wait for CSS to load
            return new Promise((resolve, reject) => {
                link.onload = () => {
                    console.log('CSS Loader: CSS ready', cssPath);
                    resolve();
                };
                link.onerror = () => {
                    console.error('CSS Loader: Failed to load', cssPath);
                    reject(new Error(`Failed to load CSS: ${cssPath}`));
                };
            });
            
        } catch (error) {
            console.error('CSS Loader: Error loading CSS', cssPath, error);
        }
    }

    watchClassChanges() {
        // Watch for body class changes (for SPA-like behavior)
        if (window.MutationObserver) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        this.autoLoadCSS();
                    }
                });
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    }

    // Public method to manually load specific CSS
    async loadSpecificCSS(role = null, view = null) {
        if (role) {
            await this.loadRoleCSS(role);
        }
        
        if (view) {
            await this.loadViewCSS(view);
        }
    }

    // Public method to unload CSS (for SPA transitions)
    unloadCSS(id) {
        const link = document.getElementById(id);
        if (link) {
            link.remove();
            this.loadedCSS.delete(id);
            console.log('CSS Loader: Unloaded', id);
        }
    }

    // Get list of loaded CSS
    getLoadedCSS() {
        return Array.from(this.loadedCSS);
    }
}

// Initialize CSS Loader when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.cssLoader = new CSSLoader();
    });
} else {
    window.cssLoader = new CSSLoader();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CSSLoader;
}