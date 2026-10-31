<?php
if (!defined('ABSPATH')) exit;

/**
 * Register Admin Menu for Global Testimonial Settings
 */
add_action('admin_menu', function () {
    add_menu_page(
        __('Testimonial Slider', 'testimonial-slider'),
        __('Testimonial Slider', 'testimonial-slider'),
        'manage_options',
        'mts-testimonial-slider',
        'mts_render_admin_settings_page',
        'dashicons-testimonial',
        28
    );
});

/**
 * Enqueue Media Uploader and Admin Assets
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_mts-testimonial-slider') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style('mts-admin-style', MTS_URL . 'assets/css/admin-settings.css', [], MTS_VERSION);
    wp_enqueue_script('mts-admin-script', MTS_URL . 'assets/js/admin-settings.js', ['jquery'], MTS_VERSION, true);
});

/**
 * Render the Admin Settings Page
 */
function mts_render_admin_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'testimonial-slider'));
    }

    $saved_notice = false;

    // Handle Form Submission
    if (isset($_POST['mts_save_testimonials'])) {
        check_admin_referer('mts_save_testimonials_nonce', 'mts_nonce');

        $raw_slides = isset($_POST['slides']) && is_array($_POST['slides']) ? $_POST['slides'] : [];
        $cleaned_slides = [];

        foreach ($raw_slides as $slide) {
            $name  = sanitize_text_field($slide['person_name'] ?? '');
            $role  = sanitize_text_field($slide['person_role'] ?? '');
            $quote = sanitize_textarea_field($slide['quote'] ?? '');
            $photo = esc_url_raw($slide['person_image'] ?? '');
            $logo  = esc_url_raw($slide['company_logo'] ?? '');
            $q_img = esc_url_raw($slide['quote_image'] ?? '');
            $q_ico = sanitize_text_field($slide['quote_icon'] ?? '❝');
            $link  = esc_url_raw($slide['slide_link'] ?? '');
            $ext   = !empty($slide['is_external']) ? 'yes' : 'no';

            // Save if at least name or quote is provided
            if (!empty($name) || !empty($quote)) {
                $cleaned_slides[] = [
                    'person_name'  => $name,
                    'person_role'  => $role,
                    'quote'        => $quote,
                    'person_image' => $photo,
                    'company_logo' => $logo,
                    'quote_image'  => $q_img,
                    'quote_icon'   => $q_ico,
                    'slide_link'   => $link,
                    'is_external'  => $ext,
                ];
            }
        }

        update_option('mts_global_testimonials', $cleaned_slides);
        $saved_notice = true;
    }

    $slides = get_option('mts_global_testimonials', null);

    // Initial defaults if never saved
    if ($slides === null) {
        $slides = [
            [
                'person_name'  => 'Rick Webber',
                'person_role'  => 'CEO - CBC Federal Credit Union',
                'quote'        => 'We hired Michel to join our regional conference to share innovative customer and employee strategies with our team. He was the highest rated speaker during our conference.',
                'person_image' => '',
                'company_logo' => '',
                'quote_image'  => '',
                'quote_icon'   => '❝',
                'slide_link'   => '',
                'is_external'  => 'no',
            ]
        ];
    }
    ?>
    <div class="wrap mts-admin-wrap">
        <div class="mts-admin-header">
            <div class="mts-admin-title-area">
                <h1><?php esc_html_e('Testimonial Slider - Global Settings', 'testimonial-slider'); ?></h1>
                <p class="mts-admin-subtitle">
                    <?php esc_html_e('Manage testimonials from 1 central place. Any page using the widget with "Content Source: Global" will automatically sync with this content.', 'testimonial-slider'); ?>
                </p>
            </div>
            <div class="mts-header-actions">
                <span class="mts-badge-version">v2.0.0</span>
            </div>
        </div>

        <?php if ($saved_notice): ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php esc_html_e('Global testimonials saved successfully!', 'testimonial-slider'); ?></strong></p>
            </div>
        <?php endif; ?>

        <!-- Shortcode & Quick Info Box -->
        <div class="mts-shortcode-card">
            <div class="mts-sc-col">
                <div class="mts-sc-label"><?php esc_html_e('Central Shortcode:', 'testimonial-slider'); ?></div>
                <div class="mts-sc-box">
                    <code id="mts-sc-text">[testimonial_slider]</code>
                    <button type="button" class="button button-secondary" id="mts-copy-sc-btn">
                        <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy Shortcode', 'testimonial-slider'); ?>
                    </button>
                    <span id="mts-copied-notice" style="display:none; color:#10b981; font-weight:600; margin-left:8px;">✓ Copied!</span>
                </div>
            </div>
            <div class="mts-sc-col mts-sc-tips">
                <p><strong>Tip:</strong> In Elementor, drop the <strong>Testimonial Slider</strong> widget on any page and choose <em>"Content Source &rarr; Global"</em>. If a specific page needs different testimonials, choose <em>"Content Source &rarr; Custom"</em> on that page.</p>
            </div>
        </div>

        <!-- Form for Testimonials -->
        <form method="post" action="" id="mts-testimonials-form">
            <?php wp_nonce_field('mts_save_testimonials_nonce', 'mts_nonce'); ?>

            <div class="mts-slides-header-bar">
                <h2><?php esc_html_e('All Global Testimonials', 'testimonial-slider'); ?> (<span id="mts-slides-count"><?php echo count($slides); ?></span>)</h2>
                <div class="mts-top-btn-group">
                    <button type="button" class="button button-primary mts-add-slide-btn">
                        <span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e('Add New Testimonial', 'testimonial-slider'); ?>
                    </button>
                    <input type="submit" name="mts_save_testimonials" class="button button-primary button-hero-save" value="<?php esc_attr_e('Save Changes', 'testimonial-slider'); ?>">
                </div>
            </div>

            <div id="mts-slides-list" class="mts-slides-list">
                <?php if (empty($slides)): ?>
                    <div class="mts-empty-message">
                        <p><?php esc_html_e('No global testimonials added yet. Click "Add New Testimonial" below to get started.', 'testimonial-slider'); ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($slides as $index => $slide): ?>
                        <?php mts_render_admin_slide_item($index, $slide); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mts-bottom-actions-bar">
                <button type="button" class="button button-primary button-large mts-add-slide-btn">
                    <span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e('Add Another Testimonial', 'testimonial-slider'); ?>
                </button>
                <input type="submit" name="mts_save_testimonials" class="button button-primary button-large" value="<?php esc_attr_e('Save All Changes', 'testimonial-slider'); ?>">
            </div>
        </form>
    </div>

    <!-- Hidden Template for Dynamic Addition -->
    <template id="mts-slide-template">
        <?php mts_render_admin_slide_item('__INDEX__', [
            'person_name'  => '',
            'person_role'  => '',
            'quote'        => '',
            'person_image' => '',
            'company_logo' => '',
            'quote_image'  => '',
            'quote_icon'   => '❝',
            'slide_link'   => '',
            'is_external'  => 'no',
        ]); ?>
    </template>
    <?php
}

/**
 * Render a Single Slide Item in Admin Repeater
 */
function mts_render_admin_slide_item($index, $slide) {
    $name   = $slide['person_name'] ?? '';
    $role   = $slide['person_role'] ?? '';
    $quote  = $slide['quote'] ?? '';
    $photo  = $slide['person_image'] ?? '';
    $logo   = $slide['company_logo'] ?? '';
    $q_img  = $slide['quote_image'] ?? '';
    $q_ico  = !empty($slide['quote_icon']) ? $slide['quote_icon'] : '❝';
    $link   = $slide['slide_link'] ?? '';
    $ext    = ($slide['is_external'] ?? 'no') === 'yes';

    $display_title = !empty($name) ? $name : __('(Untitled Testimonial)', 'testimonial-slider');
    ?>
    <div class="mts-slide-card" data-index="<?php echo esc_attr($index); ?>">
        <div class="mts-card-header">
            <div class="mts-drag-handle" title="<?php esc_attr_e('Click and drag to reorder', 'testimonial-slider'); ?>">
                <span class="dashicons dashicons-menu"></span>
                <span class="mts-slide-num">#<span class="mts-num-val"><?php echo is_numeric($index) ? ((int)$index + 1) : 1; ?></span></span>
            </div>
            <div class="mts-card-summary">
                <strong class="mts-summary-name"><?php echo esc_html($display_title); ?></strong>
                <?php if (!empty($role)): ?>
                    <span class="mts-summary-role"> — <?php echo esc_html($role); ?></span>
                <?php endif; ?>
            </div>
            <div class="mts-card-controls">
                <button type="button" class="button-link mts-move-up-btn" title="<?php esc_attr_e('Move Up', 'testimonial-slider'); ?>">
                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                </button>
                <button type="button" class="button-link mts-move-down-btn" title="<?php esc_attr_e('Move Down', 'testimonial-slider'); ?>">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <button type="button" class="button-link mts-toggle-card-btn" title="<?php esc_attr_e('Toggle Expand/Collapse', 'testimonial-slider'); ?>">
                    <span class="dashicons dashicons-arrow-down"></span>
                </button>
                <button type="button" class="button-link-delete mts-remove-slide-btn" title="<?php esc_attr_e('Delete Testimonial', 'testimonial-slider'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>

        <div class="mts-card-body">
            <div class="mts-grid-2">
                <!-- Person Name -->
                <div class="mts-field-group">
                    <label><?php esc_html_e('Person Name', 'testimonial-slider'); ?> <span class="required">*</span></label>
                    <input type="text" name="slides[<?php echo esc_attr($index); ?>][person_name]" class="widefat mts-name-input" value="<?php echo esc_attr($name); ?>" placeholder="e.g. Rick Webber">
                </div>

                <!-- Person Role / Designation -->
                <div class="mts-field-group">
                    <label><?php esc_html_e('Designation', 'testimonial-slider'); ?></label>
                    <input type="text" name="slides[<?php echo esc_attr($index); ?>][person_role]" class="widefat mts-role-input" value="<?php echo esc_attr($role); ?>" placeholder="e.g. CEO - CBC Federal Credit Union">
                </div>
            </div>

            <!-- Testimonial Quote Text -->
            <div class="mts-field-group">
                <label><?php esc_html_e('Testimonial Quote', 'testimonial-slider'); ?> <span class="required">*</span></label>
                <textarea name="slides[<?php echo esc_attr($index); ?>][quote]" rows="4" class="widefat" placeholder="<?php esc_attr_e('Enter client testimonial here...', 'testimonial-slider'); ?>"><?php echo esc_textarea($quote); ?></textarea>
            </div>

            <div class="mts-grid-2">
                <!-- Person Photo -->
                <div class="mts-field-group mts-media-field">
                    <label><?php esc_html_e('Person Photo', 'testimonial-slider'); ?></label>
                    <div class="mts-media-uploader-row">
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][person_image]" class="widefat mts-media-input" value="<?php echo esc_attr($photo); ?>" placeholder="<?php esc_attr_e('Upload or enter image URL', 'testimonial-slider'); ?>">
                        <button type="button" class="button mts-upload-btn" data-title="<?php esc_attr_e('Select Person Photo', 'testimonial-slider'); ?>">
                            <span class="dashicons dashicons-camera"></span> <?php esc_html_e('Choose Image', 'testimonial-slider'); ?>
                        </button>
                    </div>
                    <div class="mts-media-preview">
                        <?php if (!empty($photo)): ?>
                            <div class="mts-thumb-wrap">
                                <img src="<?php echo esc_url($photo); ?>" alt="Preview" class="mts-thumb-img">
                                <button type="button" class="button-link-delete mts-remove-media"><?php esc_html_e('Remove', 'testimonial-slider'); ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <small class="description"><?php esc_html_e('Leave blank if no photo is available. The widget will cleanly adapt to full width or initials.', 'testimonial-slider'); ?></small>
                </div>

                <!-- Company Logo -->
                <div class="mts-field-group mts-media-field">
                    <label><?php esc_html_e('Company Logo', 'testimonial-slider'); ?></label>
                    <div class="mts-media-uploader-row">
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][company_logo]" class="widefat mts-media-input" value="<?php echo esc_attr($logo); ?>" placeholder="<?php esc_attr_e('Upload or enter logo URL', 'testimonial-slider'); ?>">
                        <button type="button" class="button mts-upload-btn" data-title="<?php esc_attr_e('Select Company Logo', 'testimonial-slider'); ?>">
                            <span class="dashicons dashicons-format-image"></span> <?php esc_html_e('Choose Logo', 'testimonial-slider'); ?>
                        </button>
                    </div>
                    <div class="mts-media-preview">
                        <?php if (!empty($logo)): ?>
                            <div class="mts-thumb-wrap">
                                <img src="<?php echo esc_url($logo); ?>" alt="Preview" class="mts-thumb-img">
                                <button type="button" class="button-link-delete mts-remove-media"><?php esc_html_e('Remove', 'testimonial-slider'); ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mts-grid-3">
                <!-- Quote Symbol / Character -->
                <div class="mts-field-group">
                    <label><?php esc_html_e('Quote Symbol', 'testimonial-slider'); ?></label>
                    <input type="text" name="slides[<?php echo esc_attr($index); ?>][quote_icon]" class="widefat" value="<?php echo esc_attr($q_ico); ?>" placeholder="❝">
                </div>

                <!-- Custom Quote Image -->
                <div class="mts-field-group mts-media-field">
                    <label><?php esc_html_e('Custom Quote Image', 'testimonial-slider'); ?></label>
                    <div class="mts-media-uploader-row">
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][quote_image]" class="widefat mts-media-input" value="<?php echo esc_attr($q_img); ?>" placeholder="Optional icon image">
                        <button type="button" class="button mts-upload-btn" data-title="<?php esc_attr_e('Select Quote Icon', 'testimonial-slider'); ?>">
                            <span class="dashicons dashicons-upload"></span>
                        </button>
                    </div>
                    <div class="mts-media-preview">
                        <?php if (!empty($q_img)): ?>
                            <div class="mts-thumb-wrap">
                                <img src="<?php echo esc_url($q_img); ?>" alt="Preview" class="mts-thumb-img">
                                <button type="button" class="button-link-delete mts-remove-media"><?php esc_html_e('Remove', 'testimonial-slider'); ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Slide Optional Link -->
                <div class="mts-field-group">
                    <label><?php esc_html_e('Slide Link', 'testimonial-slider'); ?></label>
                    <input type="url" name="slides[<?php echo esc_attr($index); ?>][slide_link]" class="widefat" value="<?php echo esc_attr($link); ?>" placeholder="https://example.com">
                    <label class="mts-checkbox-label">
                        <input type="checkbox" name="slides[<?php echo esc_attr($index); ?>][is_external]" value="1" <?php checked($ext, true); ?>>
                        <?php esc_html_e('Open in new tab', 'testimonial-slider'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?php
}
