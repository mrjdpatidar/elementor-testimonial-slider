<?php
/**
 * Plugin Name: Testimonial Slider for Elementor
 * Description: Fully configurable testimonial slider with central WP Admin management, dual content mode (Global & Custom), flexible arrow overlay positions, and smart no-photo handling.
 * Version: 2.0.0
 * Author: Drunall Technologies
 * Text Domain: testimonial-slider
 * Update URI: false
 */
if (!defined('ABSPATH')) exit;

define('MTS_VERSION', '2.0.0');
define('MTS_URL', plugin_dir_url(__FILE__));
define('MTS_PATH', plugin_dir_path(__FILE__));

// Completely prevent WordPress.org from checking or offering updates for this plugin
add_filter('http_request_args', function ($r, $url) {
    if (strpos($url, 'api.wordpress.org/plugins/update-check') !== false && !empty($r['body']['plugins'])) {
        $plugins = json_decode($r['body']['plugins'], true);
        $basename = plugin_basename(__FILE__);
        if (isset($plugins['plugins'][$basename])) {
            unset($plugins['plugins'][$basename]);
            $r['body']['plugins'] = wp_json_encode($plugins);
        }
    }
    return $r;
}, 10, 2);

add_filter('site_transient_update_plugins', 'mts_disable_plugin_updates');
add_filter('pre_set_site_transient_update_plugins', 'mts_disable_plugin_updates');
function mts_disable_plugin_updates($transient) {
    if (is_object($transient)) {
        $basename = plugin_basename(__FILE__);
        if (isset($transient->response[$basename])) {
            unset($transient->response[$basename]);
        }
        if (isset($transient->no_update[$basename])) {
            unset($transient->no_update[$basename]);
        }
    }
    return $transient;
}

// Register Frontend Assets
add_action('wp_enqueue_scripts', function () {
    wp_register_style('mts-style', MTS_URL . 'assets/css/slider.css', [], MTS_VERSION);
    wp_register_script('mts-script', MTS_URL . 'assets/js/slider.js', [], MTS_VERSION, true);
});

// Include Central Admin Settings Page
require_once MTS_PATH . 'includes/admin-settings.php';

// Register Elementor Widget
add_action('elementor/widgets/register', function($manager) {
    if (!class_exists('\Elementor\Widget_Base')) return;
    require_once MTS_PATH . 'widget.php';
    $manager->register(new \Testimonial_Slider_Widget());
});

/**
 * Helper to get initials from a person's name
 */
if (!function_exists('mts_get_initials')) {
    function mts_get_initials($name) {
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
}

/**
 * Shortcode [testimonial_slider] to display global testimonials anywhere
 */
add_shortcode('testimonial_slider', function ($atts) {
    $a = shortcode_atts([
        'limit'              => 0,
        'autoplay'           => 'yes',
        'interval'          => 4500,
        'speed'              => 550,
        'animation'          => 'slide',
        'easing'             => 'ease',
        'loop'               => 'yes',
        'arrows'             => 'yes',
        'arrow_position'     => 'outside',
        'dots'               => 'no',
        'show_portrait_col'  => 'yes',
        'no_photo_behavior'  => 'hide',
        'orderby'            => 'default',
    ], $atts, 'testimonial_slider');

    // Enqueue frontend assets
    wp_enqueue_style('mts-style');
    wp_enqueue_script('mts-script');

    $slides = get_option('mts_global_testimonials', []);
    if (empty($slides)) {
        return '<p class="mts-no-testimonials">' . esc_html__('No testimonials found. Please add testimonials in WP Admin &rarr; Testimonials.', 'testimonial-slider') . '</p>';
    }

    if ($a['orderby'] === 'rand') {
        shuffle($slides);
    } elseif ($a['orderby'] === 'reverse') {
        $slides = array_reverse($slides);
    }

    $limit = (int)$a['limit'];
    if ($limit > 0 && count($slides) > $limit) {
        $slides = array_slice($slides, 0, $limit);
    }

    $has_multiple_slides = count($slides) > 1;
    $id = 'mts-sc-' . wp_rand(1000, 9999);

    $arrow_position = in_array($a['arrow_position'], ['outside', 'half-overlay', 'inside', 'bottom-center', 'bottom-right', 'bottom-left', 'top-right', 'top-left'], true) ? $a['arrow_position'] : 'outside';

    $show_portrait_col = $a['show_portrait_col'] === 'yes';
    $no_photo_behavior = $a['no_photo_behavior'];

    $config = [
        'autoplay'  => $a['autoplay'] === 'yes',
        'delay'     => max(500, (int)$a['interval']),
        'speed'     => max(100, (int)$a['speed']),
        'animation' => $a['animation'],
        'easing'    => $a['easing'],
        'loop'      => $a['loop'] === 'yes',
        'hover'     => true,
        'focus'     => true,
        'swipe'     => true,
        'keyboard'  => true
    ];
    $json = wp_json_encode($config);

    ob_start();
    ?>
    <div id="<?php echo esc_attr($id); ?>" class="mts-root mts-arrows--<?php echo esc_attr($arrow_position); ?>" data-config="<?php echo esc_attr($json); ?>" aria-roledescription="carousel" aria-label="Testimonials">

        <div class="mts-main">
            <?php if ($has_multiple_slides && $a['arrows'] === 'yes'): ?>
                <button class="mts-arrow mts-prev" type="button" aria-label="Previous testimonial"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14"/></svg></button>
            <?php endif; ?>

            <div class="mts-viewport">
                <div class="mts-track">
                    <?php foreach ($slides as $slide):
                        $photo = !empty($slide['person_image']) ? esc_url($slide['person_image']) : '';
                        $logo  = !empty($slide['company_logo']) ? esc_url($slide['company_logo']) : '';
                        $name  = $slide['person_name'] ?? '';
                        $role  = $slide['person_role'] ?? '';
                        $quote = $slide['quote'] ?? '';
                        $link  = !empty($slide['slide_link']) ? esc_url($slide['slide_link']) : '';
                        $ext   = ($slide['is_external'] ?? '') === 'yes';

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
                                        <img class="mts-person-photo" src="<?php echo $photo; ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
                                    <?php elseif ($no_photo_behavior === 'avatar'): ?>
                                        <div class="mts-portrait-avatar">
                                            <span class="mts-avatar-initials"><?php echo esc_html(mts_get_initials($name)); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mts-person-badge">
                                        <?php if ($logo): ?>
                                            <img class="mts-company-logo" src="<?php echo $logo; ?>" alt="Company logo" loading="lazy">
                                        <?php endif; ?>
                                        <div class="mts-person-details">
                                            <div class="mts-person-name"><?php echo esc_html($name); ?></div>
                                            <div class="mts-person-role"><?php echo esc_html($role); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mts-quote-card">
                                <?php if (!empty($slide['quote_image'])): ?>
                                    <img class="mts-quote-image" src="<?php echo esc_url($slide['quote_image']); ?>" alt="" aria-hidden="true" loading="lazy">
                                <?php else: ?>
                                    <div class="mts-symbol" aria-hidden="true"><?php echo esc_html(!empty($slide['quote_icon']) ? $slide['quote_icon'] : '❝'); ?></div>
                                <?php endif; ?>

                                <div class="mts-quote-text"><?php echo nl2br(esc_html($quote)); ?></div>

                                <?php if (!empty($link)): ?>
                                    <a class="mts-slide-link" href="<?php echo $link; ?>" <?php echo $ext ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                        Read more <span aria-hidden="true">&rarr;</span>
                                    </a>
                                <?php endif; ?>

                                <?php if (!$show_portrait): ?>
                                    <div class="mts-person-badge mts-badge-in-card">
                                        <?php if ($logo): ?>
                                            <img class="mts-company-logo" src="<?php echo $logo; ?>" alt="Company logo" loading="lazy">
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

            <?php if ($has_multiple_slides && $a['arrows'] === 'yes'): ?>
                <button class="mts-arrow mts-next" type="button" aria-label="Next testimonial"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5 7 7-7 7M19 12H5"/></svg></button>
            <?php endif; ?>
        </div>

        <?php if ($has_multiple_slides && $a['dots'] === 'yes'): ?>
            <div class="mts-dots" aria-label="Select testimonial"></div>
        <?php endif; ?>

    </div>
    <?php
    return ob_get_clean();
});
