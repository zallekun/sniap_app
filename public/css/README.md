# SNIA Conference System - CSS Architecture

## Overview
Sistem CSS yang terorganisir berdasarkan role dan view untuk memudahkan maintenance dan pengembangan.

## Struktur Directory

```
css/
├── app.css                 # Master CSS file
├── base/                   # Foundation CSS
│   ├── variables.css       # CSS Variables
│   ├── reset.css          # CSS Reset & Base styles
│   └── components.css     # Shared components
├── roles/                 # Role-specific styles
│   ├── admin/
│   │   └── admin.css      # Admin-specific styles
│   ├── presenter/
│   │   └── presenter.css  # Presenter-specific styles
│   ├── reviewer/
│   │   └── reviewer.css   # Reviewer-specific styles
│   └── audience/
│       └── audience.css   # Audience-specific styles
└── views/                 # View-specific styles
    ├── auth/
    │   └── auth.css       # Login/Register pages
    ├── events/
    │   └── events.css     # Event-related pages
    ├── registrations/
    │   └── registrations.css
    ├── payments/
    │   └── payments.css
    └── certificates/
        └── certificates.css
```

## CSS Loading System

### 1. Base Loading
Semua halaman memuat `app.css` yang berisi:
- CSS Variables
- CSS Reset
- Base Components
- Bootstrap Grid System

### 2. Dynamic Loading
CSS role dan view dimuat secara dinamis berdasarkan:
- Body classes
- URL path
- User role

### 3. Body Class Convention

```html
<!-- Admin Dashboard -->
<body class="admin-dashboard dashboard-page">

<!-- Audience Events Page -->
<body class="audience-dashboard events-page">

<!-- Auth Pages -->
<body class="auth-page">
```

## Implementasi

### 1. Menggunakan Base Layout

```php
<?= $this->extend('shared/layouts/base_layout') ?>

<?= $this->section('title') ?>Page Title<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page content -->
<?= $this->endSection() ?>
```

### 2. Layout dengan Role Detection

Base layout secara otomatis mendeteksi role dan menambahkan class yang sesuai:

```php
// Auto-generated body classes
$bodyClasses = [];

// Add role class
if (isset($userRole)) {
    $bodyClasses[] = $userRole . '-dashboard';
}

// Add page class based on route
if (strpos($routeName, 'event') !== false) {
    $bodyClasses[] = 'events-page';
}
```

### 3. Manual CSS Loading

```javascript
// Load specific CSS manually
window.cssLoader.loadSpecificCSS('admin', 'events');

// Check loaded CSS
console.log(window.cssLoader.getLoadedCSS());
```

## CSS Variables

### Colors
```css
:root {
    --primary: #2563eb;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #06b6d4;
}
```

### Spacing
```css
:root {
    --spacing-1: 0.25rem;
    --spacing-2: 0.5rem;
    --spacing-3: 0.75rem;
    --spacing-4: 1rem;
    --spacing-6: 1.5rem;
    --spacing-8: 2rem;
}
```

### Typography
```css
:root {
    --font-family: 'Inter', system-ui, -apple-system, sans-serif;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
}
```

## Component Classes

### Buttons
```html
<button class="btn btn-primary">Primary Button</button>
<button class="btn btn-outline-success">Outline Success</button>
<button class="btn btn-sm">Small Button</button>
```

### Cards
```html
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Card Title</h5>
    </div>
    <div class="card-body">
        Card content
    </div>
</div>
```

### Badges
```html
<span class="badge bg-success">Success</span>
<span class="badge bg-warning">Warning</span>
<span class="badge bg-info">Info</span>
```

## Role-Specific Classes

### Admin
```css
.admin-dashboard { /* Admin background */ }
.admin-navbar { /* Admin navigation */ }
.admin-stat-card { /* Admin statistics */ }
.admin-table-container { /* Admin tables */ }
```

### Presenter
```css
.presenter-dashboard { /* Presenter background */ }
.presenter-abstract-card { /* Abstract cards */ }
.presenter-presentation-card { /* Presentation cards */ }
```

### Reviewer
```css
.reviewer-dashboard { /* Reviewer background */ }
.reviewer-assignment-card { /* Review assignments */ }
.reviewer-criteria-grid { /* Review criteria */ }
```

### Audience
```css
.audience-dashboard { /* Audience background */ }
.audience-registration-card { /* Registration cards */ }
.audience-payment-card { /* Payment cards */ }
```

## Migration Guide

### Dari CSS Lama ke Baru

1. **Update Layout References**
```php
// OLD
<?= $this->extend('shared/layouts/user_layout') ?>

// NEW
<?= $this->extend('shared/layouts/base_layout') ?>
```

2. **Remove Manual CSS Links**
```html
<!-- OLD - Remove these -->
<link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">

<!-- NEW - Only use app.css (automatically loaded) -->
```

3. **Update Body Classes**
```html
<!-- OLD -->
<body class="user-dashboard">

<!-- NEW -->
<body class="audience-dashboard events-page">
```

4. **Update CSS Class Names**
```css
/* OLD */
.stat-card { }
.user-avatar { }

/* NEW */
.audience-stat-card { }
.audience-user-avatar { }
```

## Best Practices

### 1. Class Naming Convention
- Role prefix: `admin-`, `presenter-`, `reviewer-`, `audience-`
- View prefix: `auth-`, `events-`, `dashboard-`
- Component suffix: `-card`, `-button`, `-form`, `-table`

### 2. CSS Variables Usage
```css
/* GOOD */
.my-component {
    color: var(--primary);
    padding: var(--spacing-4);
    border-radius: var(--border-radius);
}

/* AVOID */
.my-component {
    color: #2563eb;
    padding: 1rem;
    border-radius: 0.5rem;
}
```

### 3. Responsive Design
```css
/* Mobile first approach */
.component {
    /* Mobile styles */
}

@media (min-width: 768px) {
    .component {
        /* Tablet styles */
    }
}

@media (min-width: 992px) {
    .component {
        /* Desktop styles */
    }
}
```

### 4. Performance
- CSS files dimuat on-demand
- Gunakan CSS variables untuk consistency
- Hindari deep nesting (max 3 levels)
- Gunakan shorthand properties

## Testing

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Performance Metrics
- First Contentful Paint: < 1.5s
- CSS file size: < 50KB per role
- Loading time: < 200ms per CSS file

## Troubleshooting

### CSS Tidak Termuat
1. Check console untuk error
2. Verify file path exists
3. Check browser network tab
4. Verify CSS loader initialization

### Styling Tidak Apply
1. Check body class names
2. Verify CSS selector specificity
3. Check for CSS conflicts
4. Clear browser cache

### JavaScript Errors
1. Verify CSS loader script loaded
2. Check for jQuery conflicts
3. Verify Bootstrap JS loaded
4. Check console for errors