# Testimonial Slider for Elementor & WordPress

### Plugin Documentation & Developer Guide by Jagdish Patidar

A high-performance executive testimonial slider for Elementor and WordPress featuring dual-source review management (Central WP Admin & Per-Widget Custom), seamless infinite loop carousel animation, full responsive arrow positioning, and intelligent no-photo fallback handling.

- **Developer:** Jagdish Patidar
- **Company:** [Drunall Technologies](https://drunall.com)
- **Project Type:** WordPress Plugin / Elementor Addon
- **Version:** 2.0.0
- **License:** GPL-2.0-or-later
- **Tested Up To:** WordPress 6.x & Elementor 3.x

---

## Overview

**Testimonial Slider** is engineered for corporate websites, agencies, and consulting platforms that need an authoritative presentation of client endorsements, keynotes, and executive reviews.

Unlike conventional testimonial carousels that provide rigid one-box designs, Testimonial Slider provides an asymmetric layout combining a dedicated portrait photograph column with an independent quotation card, complete with verified company badges, custom SVG navigation arrows, and full touch-swipe gesture controls.

---

## Key Features & What's New in Version 2.0.0

### 1. Centralized Admin Management (Global Content Mode)
- Manage all reviews centrally from **WP Admin &rarr; Testimonials**.
- Add, edit, reorder (Move Up / Down), or delete testimonials from a single screen.
- Updating once in the WordPress Admin dashboard automatically updates every page where Global source is active.
- Native WordPress Media Library integration for author portraits, company logos, and custom quote icons.

### 2. Dual Content Source per Widget
- **Global:** Connects to the central WP Admin review database.
- **Custom:** Enables page-specific overrides where a particular page requires unique testimonials without altering other pages.

### 3. Seamless Continuous Loop Carousel (Zero Rewind Jerk)
- Built with a true infinite looping engine using boundary clones.
- Navigating past the last slide smoothly advances forward into the first slide in the **exact same direction**, completely eliminating rewind jumps.
- When infinite loop is disabled, navigation arrows cleanly disable on the first and last slides.

### 4. Full Responsive Navigation Arrow Positioning
Independently set arrow placement for **Desktop**, **Tablet**, and **Mobile**:
- **Outside:** Side arrows flanking the slider viewport.
- **Half Overlay:** 50% border overlap on card edges (half inside, half outside).
- **Inside:** Floating inner side arrows overlaying the card.
- **Bottom Center:** Both arrows centered beneath the card (*Default on mobile*).
- **Bottom Right / Bottom Left:** Grouped arrows on lower corners.
- **Top Right / Top Left:** Grouped arrows above the slider.
- Responsive horizontal offset, vertical offset, and button gap sliders for pixel-perfect alignment.

### 5. Intelligent No-Photo / Missing Image Handling
- **Hide Column and Expand Card (Full Width):** If a client photo is omitted, the orange quote card automatically expands to 100% full width, and the author details (name, designation, and company logo) move inside the card at the bottom.
- **Show Initials Avatar:** Generates a clean circular initials avatar (e.g. "RW" for Rick Webber) inside the portrait column.
- **Keep Blank Box:** Preserves the portrait box as a placeholder.
- **Global Toggle:** Turn off the portrait column entirely for all slides to create quote-only card layouts.

### 6. Universal Shortcode Support
- Embed the slider anywhere across WordPress using `[testimonial_slider]`.
- Works in Gutenberg blocks, classic editor, text widgets, and custom theme PHP templates without requiring Elementor.

### 7. Clean, Clutter-Free English Interface
- 100% professional English labels and controls without brackets or clutter.

---

## Technical Architecture

```text
testimonial-slider/
├── testimonial-slider.php           # Plugin bootstrap, asset registration, shortcode & update-blocking rules
├── widget.php                       # Elementor Widget Base class, responsive controls & render engine
├── includes/
│   └── admin-settings.php           # Central WP Admin settings page, repeater UI & save handler
└── assets/
    ├── css/
    │   ├── admin-settings.css       # WordPress admin styling for global testimonials repeater
    │   └── slider.css               # Responsive layouts, arrow position modes, and animation transitions
    └── js/
        ├── admin-settings.js        # Admin repeater scripts, WP media uploader & shortcode copy
        └── slider.js                # Vanilla JS carousel engine with seamless infinite loop and touch gestures
```

---

## How to Use

### Method 1: Using Elementor
1. Edit any page with Elementor and drag the **Testimonial Slider** widget onto your page.
2. In the panel under **Content &rarr; Testimonials**:
   - Select **Content Source**: Choose **Global** (synced with WP Admin) or **Custom** (manual for this page).
3. Under **Slider Options &rarr; Navigation Arrows**:
   - Choose your preferred **Arrow Position** for Desktop, Tablet, and Mobile.
4. Under **Layout & Image Behavior**:
   - Select **When No Photo Uploaded** (`Hide Column and Expand Card` or `Show Initials Avatar`).

### Method 2: Central WP Admin Management
1. Navigate to **WP Admin &rarr; Testimonials**.
2. Add, reorder, or update your testimonials.
3. Click **Save Changes**. All widgets set to **Global** source will update instantly.

### Method 3: Using the Shortcode
```text
[testimonial_slider]
[testimonial_slider arrow_position="bottom-center" autoplay="yes"]
[testimonial_slider limit="5" orderby="rand"]
```

---

## Author & License

- **Developer:** Jagdish Patidar
- **Company:** [Drunall Technologies](https://drunall.com)
- **License:** GPL-2.0-or-later
