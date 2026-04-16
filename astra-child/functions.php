<?php
/**
 * Astra Child Theme - Dr Michael Teng
 */

// Enqueue parent + child styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('astra-parent', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('astra-child', get_stylesheet_uri(), ['astra-parent'], '1.0');
});

// Add scroll animation script
add_action('wp_footer', function() {
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.fade-in, .reveal').forEach(el => observer.observe(el));

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
</script>
<?php
});

// Remove Astra default page title for custom pages
add_filter('astra_the_title_enabled', function($enabled) {
    if (is_page()) return false;
    return $enabled;
});

// Add custom body classes
add_filter('body_class', function($classes) {
    $classes[] = 'miketeng-site';
    return $classes;
});

// Disable Gutenberg default styles to avoid conflicts
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}, 100);

// Auto-configure front page on theme activation
add_action('after_switch_theme', function() {
    $home = get_page_by_path('home');
    if ($home) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);
    }
});

// Also run once on init if not yet set
add_action('init', function() {
    if (get_option('show_on_front') !== 'page') {
        $home = get_page_by_path('home');
        if ($home) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home->ID);
        }
    }
});

// Sort auto-generated page menu by menu_order (fallback when no custom menu assigned)
add_filter('wp_page_menu_args', function($args) {
    $args['sort_column'] = 'menu_order';
    return $args;
});

add_filter('wp_list_pages_excludes', function($exclude) {
    return $exclude;
});

// Ensure wp_list_pages also respects menu_order
add_filter('widget_pages_args', function($args) {
    $args['sort_column'] = 'menu_order';
    return $args;
});

// One-time: assign Primary Nav menu to the primary theme location
add_action('init', function() {
    if (get_option('miketeng_menu_assigned_v1')) return;
    $menu = wp_get_nav_menu_object('Primary Nav');
    if ($menu) {
        $locations = get_theme_mod('nav_menu_locations', []);
        if (!is_array($locations)) $locations = [];
        $locations['primary'] = $menu->term_id;
        set_theme_mod('nav_menu_locations', $locations);
        update_option('miketeng_menu_assigned_v1', '1');
    }
}, 20);
