<?php
if (!defined('ABSPATH')) exit;

class Testimonial_Slider_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'testimonial_slider'; }
    public function get_title() { return 'Testimonial Slider'; }
    public function get_icon() { return 'eicon-testimonial-carousel'; }
    public function get_categories() { return ['general']; }
    public function get_style_depends() { return ['mts-style']; }
    public function get_script_depends() { return ['mts-script']; }

    protected function register_controls() {
        // ==========================================
        // 1. CONTENT / TESTIMONIALS SECTION
        // ==========================================
        $this->start_controls_section('items_section', ['label' => 'Testimonials']);

        $this->add_control('content_source', [
            'label' => 'Content Source',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'custom' => 'Custom',
                'global' => 'Global',
            ],
            'default' => 'custom',
            'description' => 'Select Global to load reviews from WP Admin. Select Custom to add reviews for this page.',
        ]);

        $this->add_control('global_info_box', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw' => '<div style="padding:10px 14px;background:#f0f7ff;border-left:4px solid #0284c7;border-radius:4px;font-size:12px;line-height:1.5;color:#333;">' .
                     '<strong>Global Mode:</strong> Testimonials are managed centrally under <a href="' . esc_url(admin_url('admin.php?page=mts-testimonial-slider')) . '" target="_blank" style="color:#0284c7;font-weight:600;text-decoration:underline;">WP Admin &rarr; Testimonials</a>.' .
                     '</div>',
            'condition' => ['content_source' => 'global'],
        ]);

        $this->add_control('global_limit', [
            'label' => 'Max Testimonials to Show',
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 50,
            'default' => 0,
            'description' => '0 means show all global testimonials.',
            'condition' => ['content_source' => 'global'],
        ]);

        $this->add_control('global_orderby', [
            'label' => 'Order Testimonials',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'default' => 'Default',
                'reverse' => 'Reverse',
                'rand'    => 'Random',
            ],
            'default' => 'default',
            'condition' => ['content_source' => 'global'],
        ]);

        // Custom Repeater
        $r = new \Elementor\Repeater();
        $r->add_control('person_image', [
            'label' => 'Person Photo',
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => ['url' => ''],
            'description' => 'Leave blank if no photo is available.'
        ]);
        $r->add_control('person_name', [
            'label' => 'Name',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Rick Webber',
            'label_block' => true
        ]);
        $r->add_control('person_role', [
            'label' => 'Designation',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'CEO - CBC Federal Credit Union',
            'label_block' => true
        ]);
        $r->add_control('company_logo', [
            'label' => 'Company Logo',
            'type' => \Elementor\Controls_Manager::MEDIA
        ]);
        $r->add_control('quote', [
            'label' => 'Testimonial',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'rows' => 5,
            'default' => 'We hired the keynote speaker to join our regional conference to share innovative customer and employee strategies with our team. Highly rated presentation that resonated deeply across our leadership.'
        ]);
        $r->add_control('quote_image', [
            'label' => 'Quote Icon Image',
            'type' => \Elementor\Controls_Manager::MEDIA,
            'description' => 'Replaces the quote symbol when uploaded.'
        ]);
        $r->add_control('quote_icon', [
            'label' => 'Quote Symbol',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '❝'
        ]);
        $r->add_control('slide_link', [
            'label' => 'Optional Link',
            'type' => \Elementor\Controls_Manager::URL,
            'placeholder' => 'https://example.com'
        ]);

        $this->add_control('slides', [
            'label' => 'Slides',
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $r->get_controls(),
            'title_field' => '{{{ person_name }}}',
            'default' => [
                [
                    'person_name' => 'Rick Webber',
                    'person_role' => 'CEO - CBC Federal Credit Union',
                    'quote'       => 'We hired the keynote speaker to join our regional conference to share innovative customer and employee strategies with our team. Highly rated presentation that resonated deeply across our leadership.'
                ]
            ],
            'condition' => ['content_source' => 'custom'],
        ]);
        $this->end_controls_section();

        // ==========================================
        // 2. SLIDER OPTIONS SECTION
        // ==========================================
        $this->start_controls_section('slider_section', ['label' => 'Slider Options']);
        $this->add_control('autoplay', ['label' => 'Autoplay', 'type' => \Elementor\Controls_Manager::SWITCHER, 'label_on' => 'Yes', 'label_off' => 'No', 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('interval', ['label' => 'Autoplay Delay (ms)', 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 500, 'max' => 30000, 'step' => 100, 'default' => 4500, 'condition' => ['autoplay' => 'yes']]);
        $this->add_control('animation_type', ['label' => 'Transition Animation', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['slide' => 'Slide', 'fade' => 'Fade', 'fade-up' => 'Fade Up', 'zoom' => 'Zoom In', 'none' => 'None'], 'default' => 'slide']);
        $this->add_control('animation_easing', ['label' => 'Animation Easing', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['ease' => 'Ease', 'ease-in-out' => 'Ease In Out', 'ease-out' => 'Ease Out', 'linear' => 'Linear', 'cubic-bezier(.22,1,.36,1)' => 'Smooth'], 'default' => 'cubic-bezier(.22,1,.36,1)']);
        $this->add_control('speed', ['label' => 'Transition Duration (ms)', 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 100, 'max' => 5000, 'step' => 50, 'default' => 550]);
        $this->add_control('loop', ['label' => 'Infinite Loop', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('pause_hover', ['label' => 'Pause on Hover', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('pause_focus', ['label' => 'Pause on Focus', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('swipe', ['label' => 'Touch Swipe', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);
        $this->add_control('keyboard', ['label' => 'Keyboard Navigation', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes']);

        // Navigation Arrows
        $this->add_control('show_arrows', ['label' => 'Show Navigation Arrows', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before']);

        // FULL RESPONSIVE ARROW POSITION
        $this->add_responsive_control('arrow_position', [
            'label' => 'Arrow Position',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'outside'       => 'Outside',
                'half-overlay'  => 'Half Overlay',
                'inside'        => 'Inside',
                'bottom-center' => 'Bottom Center',
                'bottom-right'  => 'Bottom Right',
                'bottom-left'   => 'Bottom Left',
                'top-right'     => 'Top Right',
                'top-left'      => 'Top Left',
            ],
            'default' => 'outside',
            'tablet_default' => 'outside',
            'mobile_default' => 'bottom-center',
            'condition' => ['show_arrows' => 'yes'],
            'description' => 'Select arrow placement relative to the slide items.'
        ]);

        $this->add_control('prev_image', ['label' => 'Previous Arrow Image', 'type' => \Elementor\Controls_Manager::MEDIA, 'condition' => ['show_arrows' => 'yes']]);
        $this->add_control('next_image', ['label' => 'Next Arrow Image', 'type' => \Elementor\Controls_Manager::MEDIA, 'condition' => ['show_arrows' => 'yes']]);
        $this->add_control('show_dots', ['label' => 'Show Pagination Dots', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'separator' => 'before']);
        $this->end_controls_section();

        // ==========================================
        // 3. LAYOUT & IMAGE BEHAVIOR SECTION
        // ==========================================
        $this->start_controls_section('layout_section', ['label' => 'Layout & Image Behavior']);

        $this->add_control('show_portrait_col', [
            'label' => 'Show Photo Column',
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off' => 'No',
            'return_value' => 'yes',
            'default' => 'yes',
            'description' => 'Turn off to display full-width quote cards.'
        ]);

        $this->add_control('no_photo_behavior', [
            'label' => 'When No Photo Uploaded',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'hide'   => 'Hide Column and Expand Card',
                'avatar' => 'Show Initials Avatar',
                'blank'  => 'Keep Blank Box',
            ],
            'default' => 'hide',
            'condition' => ['show_portrait_col' => 'yes'],
            'description' => 'Controls how a slide displays if a client photo is not provided.'
        ]);

        $this->add_responsive_control('slider_max', ['label' => 'Slider Max Width', 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 300, 'max' => 1800], '%' => ['min' => 40, 'max' => 100]], 'default' => ['unit' => 'px', 'size' => 1200], 'selectors' => ['{{WRAPPER}} .mts-root' => 'max-width:{{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('image_width', ['label' => 'Portrait Column Width', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 20, 'max' => 65]], 'size_units' => ['%'], 'default' => ['unit' => '%', 'size' => 35], 'selectors' => ['{{WRAPPER}} .mts-slide' => '--mts-image-width:{{SIZE}}%'], 'condition' => ['show_portrait_col' => 'yes']]);
        $this->add_responsive_control('card_height', ['label' => 'Card Height', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 180, 'max' => 750]], 'default' => ['size' => 350, 'unit' => 'px'], 'tablet_default' => ['size' => 350, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-slide' => '--mts-card-height:{{SIZE}}px']]);
        $this->add_responsive_control('gap', ['label' => 'Image / Quote Gap', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 70]], 'default' => ['size' => 12, 'unit' => 'px'], 'mobile_default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-slide' => '--mts-gap:{{SIZE}}px'], 'condition' => ['show_portrait_col' => 'yes']]);
        $this->add_responsive_control('outer_gap', ['label' => 'Outer Spacing', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 60]], 'default' => ['size' => 20, 'unit' => 'px'], 'tablet_default' => ['size' => 14, 'unit' => 'px'], 'mobile_default' => ['size' => 8, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-root' => '--mts-outer-gap:{{SIZE}}px']]);
        $this->add_responsive_control('card_radius', ['label' => 'Corner Radius', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 80]], 'default' => ['size' => 10, 'unit' => 'px'], 'mobile_default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-slide' => '--mts-radius:{{SIZE}}px']]);
        $this->add_responsive_control('quote_padding', ['label' => 'Quote Card Padding', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'default' => ['top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30, 'unit' => 'px', 'isLinked' => true], 'tablet_default' => ['top' => 24, 'right' => 24, 'bottom' => 24, 'left' => 24, 'unit' => 'px', 'isLinked' => true], 'mobile_default' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true], 'selectors' => ['{{WRAPPER}} .mts-quote-card' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}']]);

        $this->add_responsive_control('layout_direction', ['label' => 'Responsive Layout', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['row' => 'Image Left / Content Right', 'row-reverse' => 'Content Left / Image Right', 'column' => 'Image Top / Content Bottom', 'column-reverse' => 'Content Top / Image Bottom'], 'default' => 'row', 'tablet_default' => 'row', 'mobile_default' => 'column', 'selectors' => ['{{WRAPPER}} .mts-slide' => 'flex-direction:{{VALUE}}']]);
        $this->add_responsive_control('portrait_fit', ['label' => 'Portrait Image Fit', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill'], 'default' => 'cover', 'selectors' => ['{{WRAPPER}} .mts-person-photo' => 'object-fit:{{VALUE}}']]);
        $this->add_responsive_control('portrait_position', ['label' => 'Portrait Image Position', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['center center' => 'Center', 'center top' => 'Top', 'center bottom' => 'Bottom', 'left center' => 'Left', 'right center' => 'Right'], 'default' => 'center center', 'selectors' => ['{{WRAPPER}} .mts-person-photo' => 'object-position:{{VALUE}}']]);
        $this->add_responsive_control('portrait_height', ['label' => 'Portrait Column Height', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 120, 'max' => 650]], 'default' => ['size' => 280, 'unit' => 'px'], 'tablet_default' => ['size' => 240, 'unit' => 'px'], 'mobile_default' => ['size' => 220, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-slide' => '--mts-portrait-height:{{SIZE}}px']]);
        $this->add_responsive_control('quote_min_height', ['label' => 'Quote Card Minimum Height', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 100, 'max' => 650]], 'mobile_default' => ['size' => 180, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-quote-card' => 'min-height:{{SIZE}}px']]);
        $this->add_responsive_control('quote_align', ['label' => 'Quote Text Alignment', 'type' => \Elementor\Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'], 'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'], 'right' => ['title' => 'Right', 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .mts-quote-card' => 'text-align:{{VALUE}}']]);
        $this->add_responsive_control('quote_justify', ['label' => 'Quote Vertical Alignment', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['space-between' => 'Space Between', 'center' => 'Center', 'flex-start' => 'Top', 'flex-end' => 'Bottom'], 'default' => 'space-between', 'selectors' => ['{{WRAPPER}} .mts-quote-card' => 'justify-content:{{VALUE}}']]);
        $this->add_responsive_control('badge_padding', ['label' => 'Author Badge Padding', 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => ['px'], 'default' => ['top' => 8, 'right' => 10, 'bottom' => 8, 'left' => 10, 'unit' => 'px', 'isLinked' => false], 'mobile_default' => ['top' => 6, 'right' => 10, 'bottom' => 6, 'left' => 10, 'unit' => 'px', 'isLinked' => false], 'selectors' => ['{{WRAPPER}} .mts-person-badge' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}']]);
        $this->add_responsive_control('badge_gap', ['label' => 'Logo / Name Gap', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .mts-person-badge' => 'gap:{{SIZE}}px']]);
        $this->add_responsive_control('badge_radius', ['label' => 'Badge Border Radius', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 70]], 'selectors' => ['{{WRAPPER}} .mts-person-badge' => 'border-radius:{{SIZE}}px']]);
        $this->add_responsive_control('quote_image_size', ['label' => 'Quote Image Size', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 10, 'max' => 150]], 'selectors' => ['{{WRAPPER}} .mts-quote-image' => 'width:{{SIZE}}px;height:{{SIZE}}px']]);
        $this->end_controls_section();

        // ==========================================
        // 4. STYLE: BACKGROUND
        // ==========================================
        $this->start_controls_section('background_section', ['label' => 'Quote Card Background', 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_group_control(\Elementor\Group_Control_Background::get_type(), ['name' => 'quote_card_background', 'label' => 'Background', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .mts-quote-card']);
        $this->add_control('decorations', ['label' => 'Decorative Squares', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['block' => 'Show', 'none' => 'Hide'], 'default' => 'block', 'selectors' => ['{{WRAPPER}} .mts-quote-card:before,{{WRAPPER}} .mts-quote-card:after' => 'display:{{VALUE}}'], 'description' => 'Toggle background decorative squares.']);
        $this->end_controls_section();

        // ==========================================
        // 5. STYLE: AUTHOR BADGE
        // ==========================================
        $this->start_controls_section('badge_style_section', ['label' => 'Author Badge Styling', 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), ['name' => 'badge_shadow', 'label' => 'Badge Box Shadow', 'selector' => '{{WRAPPER}} .mts-person-badge']);
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), ['name' => 'badge_border', 'label' => 'Badge Border', 'selector' => '{{WRAPPER}} .mts-person-badge']);
        $this->end_controls_section();

        // ==========================================
        // 6. STYLE: COLORS & TYPOGRAPHY
        // ==========================================
        $this->start_controls_section('colors_section', ['label' => 'Colors & Typography', 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_control('quote_color', ['label' => 'Quote Text Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .mts-quote-text' => 'color:{{VALUE}}']]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name' => 'quote_typo', 'label' => 'Quote Typography', 'selector' => '{{WRAPPER}} .mts-quote-text']);
        $this->add_control('symbol_color', ['label' => 'Quote Symbol Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .mts-symbol' => 'color:{{VALUE}}']]);
        $this->add_responsive_control('symbol_size', ['label' => 'Quote Symbol Size', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 20, 'max' => 120]], 'default' => ['size' => 65, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-symbol' => 'font-size:{{SIZE}}{{UNIT}}']]);
        $this->add_control('badge_bg', ['label' => 'Badge Background', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .mts-person-badge' => 'background:{{VALUE}}', '{{WRAPPER}} .mts-root' => '--mts-in-card-badge-bg:{{VALUE}}']]);
        $this->add_control('name_color', ['label' => 'Name Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#111111', 'selectors' => ['{{WRAPPER}} .mts-person-name' => 'color:{{VALUE}}']]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name' => 'name_typo', 'label' => 'Name Typography', 'selector' => '{{WRAPPER}} .mts-person-name']);
        $this->add_control('role_color', ['label' => 'Designation Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#555555', 'selectors' => ['{{WRAPPER}} .mts-person-role' => 'color:{{VALUE}}']]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name' => 'role_typo', 'label' => 'Designation Typography', 'selector' => '{{WRAPPER}} .mts-person-role']);
        $this->add_responsive_control('badge_inset', ['label' => 'Badge Offset', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 60]], 'default' => ['size' => 8, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-person-badge:not(.mts-badge-in-card)' => 'left:{{SIZE}}{{UNIT}};right:{{SIZE}}{{UNIT}};bottom:{{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('logo_size', ['label' => 'Company Logo Width', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 20, 'max' => 160]], 'default' => ['size' => 35, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-company-logo' => 'width:{{SIZE}}{{UNIT}}']]);
        $this->end_controls_section();

        // ==========================================
        // 7. STYLE: NAVIGATION ARROWS
        // ==========================================
        $this->start_controls_section('arrow_style', ['label' => 'Navigation Arrows', 'tab' => \Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('arrow_box', [
            'label' => 'Arrow Button Size',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 20, 'max' => 110]],
            'default' => ['size' => 34, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .mts-arrow' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
                '{{WRAPPER}} .mts-root'  => '--mts-arrow-size:{{SIZE}}px;',
            ]
        ]);
        $this->add_responsive_control('arrow_icon_size', ['label' => 'Arrow Icon Size', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 8, 'max' => 90]], 'default' => ['size' => 19, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-arrow svg,{{WRAPPER}} .mts-arrow img,{{WRAPPER}} .mts-arrow .mts-svg-mask' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}']]);

        // Horizontal & Vertical Offset
        $this->add_responsive_control('arrow_h_offset', [
            'label' => 'Arrow Horizontal Offset',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -100, 'max' => 100]],
            'default' => ['size' => 0, 'unit' => 'px'],
            'selectors' => ['{{WRAPPER}} .mts-root' => '--mts-arrow-h-offset:{{SIZE}}px;'],
            'condition' => ['show_arrows' => 'yes'],
            'description' => 'Shift arrows outward or inward.'
        ]);

        $this->add_responsive_control('arrow_v_offset', [
            'label' => 'Arrow Vertical Offset',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => -120, 'max' => 120]],
            'default' => ['size' => 0, 'unit' => 'px'],
            'selectors' => ['{{WRAPPER}} .mts-root' => '--mts-arrow-v-offset:{{SIZE}}px;'],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        $this->add_responsive_control('arrows_gap', [
            'label' => 'Arrow Spacing',
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 50]],
            'default' => ['size' => 10, 'unit' => 'px'],
            'selectors' => ['{{WRAPPER}} .mts-root' => '--mts-arrows-gap:{{SIZE}}px;'],
            'condition' => ['show_arrows' => 'yes'],
        ]);

        $this->add_control('arrow_color', ['label' => 'Arrow Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FF9945', 'selectors' => ['{{WRAPPER}} .mts-arrow' => 'color:{{VALUE}};border-color:{{VALUE}}']]);
        $this->add_control('arrow_bg', ['label' => 'Arrow Background', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .mts-arrow' => 'background-color:{{VALUE}}']]);
        $this->add_control('svg_arrow_mode', ['label' => 'SVG Inherits Arrow Color', 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'description' => 'SVG uploads follow normal and hover colors. PNG files retain original colors.']);
        $this->add_control('arrow_hover_color', ['label' => 'Hover Arrow Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FFFFFF', 'selectors' => ['{{WRAPPER}} .mts-arrow:hover' => 'color:{{VALUE}}']]);
        $this->add_control('arrow_hover_bg', ['label' => 'Hover Background', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FF9945', 'selectors' => ['{{WRAPPER}} .mts-arrow:hover' => 'background-color:{{VALUE}}']]);
        $this->add_responsive_control('arrow_radius', ['label' => 'Arrow Corner Radius', 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 100]], 'default' => ['size' => 9, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .mts-arrow' => 'border-radius:{{SIZE}}{{UNIT}}']]);
        $this->add_responsive_control('arrows_display', ['label' => 'Arrow Visibility', 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['flex' => 'Show', 'none' => 'Hide'], 'default' => 'flex', 'selectors' => ['{{WRAPPER}} .mts-arrow' => 'display:{{VALUE}}']]);
        $this->add_control('dots_color', ['label' => 'Pagination Dot Color', 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#FF9945', 'selectors' => ['{{WRAPPER}} .mts-dot.is-active' => 'background:{{VALUE}}']]);
        $this->end_controls_section();
    }

    private function media_url($field) {
        if (empty($field)) return '';
        if (is_string($field)) return esc_url($field);
        if (is_array($field) && !empty($field['url'])) return esc_url($field['url']);
        return '';
    }

    protected function get_initials($name) {
        $name = trim(strip_tags((string)$name));
        if (empty($name)) return 'TS';
        $parts = preg_split('/\s+/', $name);
        $initials = '';
        if (count($parts) >= 2) {
            $initials = mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1);
        } else {
            $initials = mb_substr($parts[0], 0, 2);
        }
        return mb_strtoupper($initials);
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $source = $s['content_source'] ?? 'custom';
        $slides = [];

        if ($source === 'global') {
            $slides = get_option('mts_global_testimonials', []);
            if (empty($slides)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    echo '<div style="padding:24px;text-align:center;background:#f0f7ff;border:1px dashed #0284c7;border-radius:8px;color:#333;">' .
                         '<strong>No global testimonials found.</strong> Add testimonials in <a href="' . esc_url(admin_url('admin.php?page=mts-testimonial-slider')) . '" target="_blank" style="color:#0284c7;text-decoration:underline;">WP Admin &rarr; Testimonials</a>.' .
                         '</div>';
                }
                return;
            }

            $orderby = $s['global_orderby'] ?? 'default';
            if ($orderby === 'rand') {
                shuffle($slides);
            } elseif ($orderby === 'reverse') {
                $slides = array_reverse($slides);
            }

            $limit = (int)($s['global_limit'] ?? 0);
            if ($limit > 0 && count($slides) > $limit) {
                $slides = array_slice($slides, 0, $limit);
            }
        } else {
            $slides = $s['slides'] ?? [];
        }

        if (empty($slides) || !is_array($slides)) return;

        $has_multiple_slides = count($slides) > 1;
        $id = 'mts-' . esc_attr($this->get_id());

        // Responsive Arrow Classes
        $arrow_desktop = !empty($s['arrow_position']) ? $s['arrow_position'] : 'outside';
        $arrow_tablet  = !empty($s['arrow_position_tablet']) ? $s['arrow_position_tablet'] : $arrow_desktop;
        $arrow_mobile  = !empty($s['arrow_position_mobile']) ? $s['arrow_position_mobile'] : 'bottom-center';

        $root_classes = ['mts-root', 'mts-arrows--' . esc_attr($arrow_desktop)];
        if (!empty($arrow_tablet)) {
            $root_classes[] = 'mts-arrows-tablet--' . esc_attr($arrow_tablet);
        }
        if (!empty($arrow_mobile)) {
            $root_classes[] = 'mts-arrows-mobile--' . esc_attr($arrow_mobile);
        }

        $show_portrait_col = ($s['show_portrait_col'] ?? 'yes') === 'yes';
        $no_photo_behavior = $s['no_photo_behavior'] ?? 'hide';

        $config = [
            'autoplay'  => ($s['autoplay'] ?? 'yes') === 'yes',
            'delay'     => max(500, (int)($s['interval'] ?? 4500)),
            'speed'     => max(100, (int)($s['speed'] ?? 550)),
            'animation' => $s['animation_type'] ?? 'slide',
            'easing'    => $s['animation_easing'] ?? 'ease',
            'loop'      => ($s['loop'] ?? 'yes') === 'yes',
            'hover'     => ($s['pause_hover'] ?? 'yes') === 'yes',
            'focus'     => ($s['pause_focus'] ?? 'yes') === 'yes',
            'swipe'     => ($s['swipe'] ?? 'yes') === 'yes',
            'keyboard'  => ($s['keyboard'] ?? 'yes') === 'yes'
        ];
        $json = wp_json_encode($config);
        ?>
        <div id="<?php echo $id; ?>" class="<?php echo esc_attr(implode(' ', $root_classes)); ?>" data-config="<?php echo esc_attr($json); ?>" aria-roledescription="carousel" aria-label="Testimonials">

            <div class="mts-main">
                <?php if ($has_multiple_slides && ($s['show_arrows'] ?? 'yes') === 'yes'): ?>
                    <button class="mts-arrow mts-prev" type="button" aria-label="Previous testimonial"><?php $this->arrow($s['prev_image'] ?? [], true, ($s['svg_arrow_mode'] ?? 'yes') === 'yes'); ?></button>
                <?php endif; ?>

                <div class="mts-viewport">
                    <div class="mts-track">
                        <?php foreach ($slides as $slide):
                            $photo = $this->media_url($slide['person_image'] ?? '');
                            $logo  = $this->media_url($slide['company_logo'] ?? '');
                            $name  = $slide['person_name'] ?? '';
                            $role  = $slide['person_role'] ?? '';
                            $quote = $slide['quote'] ?? '';

                            $link_url = '';
                            $is_ext   = false;
                            if (!empty($slide['slide_link'])) {
                                if (is_array($slide['slide_link'])) {
                                    $link_url = $slide['slide_link']['url'] ?? '';
                                    $is_ext   = !empty($slide['slide_link']['is_external']);
                                } else {
                                    $link_url = $slide['slide_link'];
                                    $is_ext   = ($slide['is_external'] ?? '') === 'yes';
                                }
                            }

                            $has_photo = !empty($photo);
                            $show_portrait = $show_portrait_col && ($has_photo || $no_photo_behavior !== 'hide');
                            $slide_classes = ['mts-slide'];
                            if (!$show_portrait) {
                                $slide_classes[] = 'mts-no-portrait';
                            }
                            ?>
                            <div class="<?php echo esc_attr(implode(' ', $slide_classes)); ?>" role="group" aria-roledescription="slide">

                                <?php if ($show_portrait): ?>
                                    <div class="mts-portrait">
                                        <?php if ($has_photo): ?>
                                            <img class="mts-person-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
                                        <?php elseif ($no_photo_behavior === 'avatar'): ?>
                                            <div class="mts-portrait-avatar">
                                                <span class="mts-avatar-initials"><?php echo esc_html($this->get_initials($name)); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mts-person-badge">
                                            <?php if ($logo): ?>
                                                <img class="mts-company-logo" src="<?php echo esc_url($logo); ?>" alt="Company logo" loading="lazy">
                                            <?php endif; ?>
                                            <div class="mts-person-details">
                                                <div class="mts-person-name"><?php echo esc_html($name); ?></div>
                                                <div class="mts-person-role"><?php echo esc_html($role); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mts-quote-card">
                                    <?php $quote_img = $this->media_url($slide['quote_image'] ?? ''); ?>
                                    <?php if ($quote_img): ?>
                                        <img class="mts-quote-image" src="<?php echo esc_url($quote_img); ?>" alt="" aria-hidden="true" loading="lazy">
                                    <?php else: ?>
                                        <div class="mts-symbol" aria-hidden="true"><?php echo esc_html(!empty($slide['quote_icon']) ? $slide['quote_icon'] : '❝'); ?></div>
                                    <?php endif; ?>

                                    <div class="mts-quote-text"><?php echo nl2br(esc_html($quote)); ?></div>

                                    <?php if (!empty($link_url)): ?>
                                        <a class="mts-slide-link" href="<?php echo esc_url($link_url); ?>" <?php echo $is_ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                            Read more <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!$show_portrait): ?>
                                        <div class="mts-person-badge mts-badge-in-card">
                                            <?php if ($logo): ?>
                                                <img class="mts-company-logo" src="<?php echo esc_url($logo); ?>" alt="Company logo" loading="lazy">
                                            <?php endif; ?>
                                            <div class="mts-person-details">
                                                <div class="mts-person-name"><?php echo esc_html($name); ?></div>
                                                <div class="mts-person-role"><?php echo esc_html($role); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($has_multiple_slides && ($s['show_arrows'] ?? 'yes') === 'yes'): ?>
                    <button class="mts-arrow mts-next" type="button" aria-label="Next testimonial"><?php $this->arrow($s['next_image'] ?? [], false, ($s['svg_arrow_mode'] ?? 'yes') === 'yes'); ?></button>
                <?php endif; ?>
            </div>

            <?php if ($has_multiple_slides && ($s['show_dots'] ?? '') === 'yes'): ?>
                <div class="mts-dots" aria-label="Select testimonial"></div>
            <?php endif; ?>

        </div>
        <?php
    }

    private function arrow($media, $previous, $mask_svg = true) {
        $url = $this->media_url($media ?? []);
        if ($url) {
            $path = parse_url($url, PHP_URL_PATH);
            if ($mask_svg && strtolower(pathinfo((string)$path, PATHINFO_EXTENSION)) === 'svg') {
                echo '<span class="mts-svg-mask" aria-hidden="true" style="--mts-svg-url:url(&quot;' . esc_url($url) . '&quot;)"></span>';
            } else {
                echo '<img src="' . esc_url($url) . '" alt="" aria-hidden="true">';
            }
            return;
        }
        if ($previous) {
            echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14"/></svg>';
        } else {
            echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5 7 7-7 7M19 12H5"/></svg>';
        }
    }
}
