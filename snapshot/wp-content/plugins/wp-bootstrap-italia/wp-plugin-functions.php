<?php
/**
 * Funzione per generare il menu dinamico con supporto per target _blank
 * 
 * @param int|string $menu_id ID del menu WordPress
 * @param string $container_aria_label Etichetta ARIA per il container
 */
function custom_bootstrap_italia_menu($menu_id, $container_aria_label = 'Navigazione principale') {
    // Verifica se il menu esiste con l'ID passato
    if ($menu_id && is_nav_menu($menu_id)) {
        // Usa wp_nav_menu per generare il menu dinamico
        wp_nav_menu(array(
            'menu' => $menu_id,
            'container' => 'nav',
            'container_class' => 'navbar navbar-expand-lg has-megamenu',
            'container_aria_label' => $container_aria_label,
            'menu_class' => 'navbar-nav',
            'walker' => new Bootstrap_Italia_Walker_Nav_Menu(),
            'depth' => 2,
            'fallback_cb' => false,
        ));
    } else {
        echo '<p>Il menu specificato non esiste.</p>';
    }
}

/**
 * Walker personalizzato per generare il markup corretto del menu con supporto per target _blank
 * e conformità WCAG 2.1 AA
 */
class Bootstrap_Italia_Walker_Nav_Menu extends Walker_Nav_Menu {
    // Inizio livello del menu (es. <ul> per i dropdown)
    function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $indent = str_repeat($t, $depth);

        // Aggiungi le classi appropriate per il dropdown e l'attributo aria-label
        $submenu_class = ($depth > 0) ? 'dropdown-menu sub-menu' : 'dropdown-menu';
        $output .= "{$n}{$indent}<ul class=\"{$submenu_class}\" role=\"menu\" aria-label=\"Sottomenu\">{$n}";
    }

    // Fine livello del menu
    function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $indent = str_repeat($t, $depth);
        $output .= "$indent</ul>{$n}";
    }

    // Inizio singolo elemento del menu (li)
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $indent = ($depth) ? str_repeat($t, $depth) : '';

        // Verifica se l'elemento ha figli (per creare dropdown)
        $has_children = $args->walker->has_children ? true : false;

        // Imposta le classi dell'elemento
        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // Aggiungi la classe 'active' se l'elemento è la pagina corrente
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active';
        }

        // Aggiungi le classi necessarie per i dropdown
        $classes[] = 'nav-item';
        if ($has_children && $depth === 0) {
            $classes[] = 'dropdown';
        }
        if ($has_children && $depth > 0) {
            $classes[] = 'dropdown-submenu';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        // Inizio markup elemento
        $output .= $indent . '<li' . $class_names . ' role="none">';

        // Aggiungi il link e il titolo
        $atts = array();
        $atts['class'] = 'nav-link';

        // Se è la pagina corrente, aggiungi la classe 'active' al link
        if (in_array('current-menu-item', $classes)) {
            $atts['class'] .= ' active';
            $atts['aria-current'] = 'page';
        }

        $atts['href'] = !empty($item->url) ? $item->url : '#';

        // Gestione del target _blank
        if (!empty($item->target)) {
            $atts['target'] = $item->target;
            // Aggiungi rel="noopener noreferrer" per sicurezza quando si usa target="_blank"
            if ($item->target === '_blank') {
                $atts['rel'] = 'noopener noreferrer';
                // Aggiungi indicazione che il link si apre in una nuova finestra
                $atts['aria-label'] = esc_attr($item->title) . ' (si apre in una nuova finestra)';
            }
        }

        // Aggiungi attributi ARIA e data-toggle per il dropdown
        if ($has_children && $depth === 0) {
            $atts['class'] .= ' dropdown-toggle';
            $atts['data-toggle'] = 'dropdown';
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
            $atts['id'] = 'menu-item-dropdown-' . $item->ID . '-' . uniqid();
            $atts['role'] = 'button';
        }

        if ($depth > 0) {
            $atts['class'] .= ' dropdown-item';
            $atts['role'] = 'menuitem';
        }

        // Prepara gli attributi del link
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // Imposta il titolo del link
        $title = apply_filters('the_title', $item->title, $item->ID);

        // Output finale del link
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= '<span>' . $title . '</span>';
        $item_output .= '</a>';
        $item_output .= $args->after;

        // Aggiungi l'output dell'elemento al menu
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    // Fine singolo elemento
    function end_el(&$output, $item, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $output .= "</li>{$n}";
    }
}
?>