<?php
function get_api_data_by_iri($iri) {
    $api_url = WP_SCHEMA_API_BASE_URL . 'semantic-assets/by-iri?iri=' . urlencode($iri);
    
    // Fetch data from API
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return array('error' => 'Errore nella richiesta API');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return array('error' => 'Errore nella decodifica JSON');
    }

    return $data;
}

function display_api_data_shortcode() {
    // Get 'uri' parameter from page URL
    $iri = isset($_GET['uri']) ? urldecode($_GET['uri']) : '';
    $data = get_api_data_by_iri($iri);
    ob_start();

    // Extract main fields from API data
    $publishers = isset($data['publishers']['0']['summary']) ? esc_html($data['publishers']['0']['summary']) : '';
    $rightsHolder = isset($data['rightsHolder']['summary']) ? esc_html($data['rightsHolder']['summary']) : '';
    $title = isset($data['title']) ? esc_html($data['title']) : '';
    $type = isset($data['type']) ? esc_html($data['type']) : '';
    $issuedOn = isset($data['issuedOn']) ? esc_html($data['issuedOn']) : '';
    $description = isset($data['description']) ? esc_html($data['description']) : '';
    $modifiedOn = isset($data['modifiedOn']) ? esc_html($data['modifiedOn']) : '';
    $versionInfo = isset($data['versionInfo']) ? esc_html($data['versionInfo']) : '';

    // Set type-specific colors and labels
    if ($type == 'ONTOLOGY') {
        $name_type = "Ontologia";
        $btn_bg = "#0043E3";
        $dtl_bg = "#F2F7FC";
        $progress_cnt_bg = "#D3D9EE";
        $progress_bar_bg = "#0043E3";
    } elseif ($type == 'CONTROLLED_VOCABULARY') {
        $name_type = "Vocabolario Controllato";
        $btn_bg = "#077F7B";
        $dtl_bg = "#F7FFFF";
        $progress_cnt_bg = "#52E0DB";
        $progress_bar_bg = "#09AFA9";
    } elseif ($type == 'SCHEMA') {
        $name_type = "Schema";
        $btn_bg = "#EBA704";
        $dtl_bg = "#FFFDF6";
        $progress_cnt_bg = "#FFF8E6";
        $progress_bar_bg = "#FFB400";
    } else {
        $name_type = "Tipologia non disponibile";
        $btn_bg = "#000";
        $dtl_bg = "#FFF";
        $progress_cnt_bg = "#DDD";
        $progress_bar_bg = "#000";
    }

    // Navigation menu mapping by type
    $menu_map = [
        'CONTROLLED_VOCABULARY' => [
            'Descrizione' => 'descrizione',
            'URI' => 'uri',
            'Soggetti coinvolti' => 'soggetti',
            'Concetti principali' => 'concetti',
            'Riferimenti temporali' => 'riferimenti',
            'Versione' => 'versione',
            'Altre informazioni' => 'altre',
            'Progetti' => 'progetti',
        ],
        'SCHEMA' => [
            'Descrizione' => 'descrizione',
            'URI' => 'uri',
            'Soggetti coinvolti' => 'soggetti',
            'Concetti principali' => 'concetti',
            'Riferimenti temporali' => 'riferimenti',
            'Altre informazioni' => 'altre',
            'Progetti' => 'progetti',
            'Editor dello schema API' => 'editor-schema-api',
        ],
        'ONTOLOGY' => [
            'Descrizione' => 'descrizione',
            'URI' => 'uri',
            'Soggetti coinvolti' => 'soggetti',
            'Concetti principali' => 'concetti',
            'Riferimenti temporali' => 'riferimenti',
            'Versione' => 'versione',
            'Altre informazioni' => 'altre',
            'Progetti' => 'progetti',
        ],
    ];

    // Language code mapping
    $language_map = [
        'ITA' => 'Italiano',
        'ENG' => 'Inglese',
        'ESP' => 'Spagnolo',
        'POR' => 'Portoghese',
        'DEU' => 'Tedesco',
        'FRA' => 'Francese',
    ];
    ?>
    <style>
        .cnd-btn-primary {
            background-color: <?php echo $btn_bg; ?>;
        }
        .cnd-dtl-bg {
            background-color: <?php echo $dtl_bg; ?>;
        }
        .cnd-progress-cnt {
            background-color: <?php echo $progress_cnt_bg; ?>;
        }
        .cnd-progress-bar {
            background-color: <?php echo $progress_bar_bg; ?>;
        }
    </style>
    <!-- First section: header and navigation -->
    <section class="cnd-dtl-bg cnd-dt-hd">
        <div class="ist-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>" aria-label="Vai alla home">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>/search/" aria-label="Vai al catalogo">Catalogo</a></li>
                    <li class="breadcrumb-item active breadcrumb-sliced" aria-current="page"><?php echo $title; ?></li>
                </ol>
            </nav>

            <div class="d-flex align-items-center mb-3">
                <button class="btn cnd-btn-primary" aria-label="<?php echo $name_type; ?>"><?php echo $name_type; ?></button>
                <?php             
                    // Display status chip if available
                    if (isset($data['status']) && is_array($data['status']) && !empty($data['status'])) {
                        $status = $data['status'][0];
                        $items_html = '<div class="item-status">';
                        $items_html .= get_status_chip($status);
                        $items_html .= '</div>';
                        echo $items_html;
                    } 
                 ?>
            </div>

            <h1 class="h2"><?php echo $title; ?></h1>
            <div class="cnd-dt-hd-meta">
                <b><?php echo $rightsHolder; ?> </b>
                <span class="cnd-dvd">|</span>
                <svg class="icon icon-sm custom-folder-icon" aria-hidden="true">
                    <use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-folder'); ?>"></use>
                </svg>&nbsp; <?php echo $rightsHolder; ?> 
                <span class="cnd-dvd">|</span>
                <b>PUBBLICAZIONE A CATALOGO </b> 
                <svg class="icon custom-icon" aria-hidden="true">
                    <use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-calendar'); ?>"></use>
                </svg>
                <span class="cnd-data">
                    <?php $fdissuedOn = date('d/m/Y', strtotime($issuedOn)); echo $fdissuedOn; ?>
                </span>
            </div>
        </div>
        <!-- Progress Bar -->
    </section>
    <div class="cnd-progress-cnt" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
        <div class="cnd-progress-bar" style="width: 5px;"></div>
    </div>

    <div class="cnd-section">
        <div class="ct-div-block ist-container space-between" style="padding-top: 24px; padding-bottom: 24px; ">
            <a href="#" onclick="history.back(); return false;" class="cnd-bk-btn">
                <svg class="icon icon-sm custom-arrow-left" aria-hidden="true"><use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-arrow-left'); ?>"></use>
                </svg>
                TORNA AI RISULTATI
            </a>
            <div class="cnd-mt-bx">
                <a id="link-193-196" class="ct-link cnd-btn-bx-lgt" href="#" data-focus-mouse="false">
                    Vai al sorgente
                    <svg class="icon custom-external-link-icon" aria-hidden="true">
                            <use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-external-link'); ?>"></use>
                    </svg>
                </a>
                <a id="link-198-196" class="ct-link cnd-btn-bx-lgt-ng" href="#">
                    <div id="text_block-199-196" class="ct-text-block">Usa SPARQL</div>
                </a>
                <a id="link-201-196" class="ct-link cnd-btn-bx-lgt-ng" href="#">
                    Scarica la risorsa
                    <svg class="icon icon-sm custom-box-arrow-down" aria-hidden="true">
                        <use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-download'); ?>"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <!-- Second section: main content and navigation -->
    <section class="container py-4">
        <div class="row">
            <!-- Side navigation menu -->
            <nav id="navbar" class="col-12 col-md-3" aria-label="Menu di navigazione">
                <ul class="nav flex-column cnd-sticky">
                    <?php
                    $type = isset($data['type']) ? strtoupper($data['type']) : '';
                    $menu = isset($menu_map[$type]) ? $menu_map[$type] : $menu_map['ONTOLOGY']; // fallback

                    // Remove 'Concetti principali' if keyClasses is empty
                    $has_keyclasses = !empty($data['keyClasses']) && is_array($data['keyClasses']) && count($data['keyClasses']) > 0;
                    if (!$has_keyclasses) {
                        $menu = array_filter($menu, function($v) { return $v !== 'concetti'; });
                    }

                    // Remove 'Progetti' if projects is empty
                    $has_projects = !empty($data['projects']) && is_array($data['projects']) && count($data['projects']) > 0;
                    if (!$has_projects) {
                        $menu = array_filter($menu, function($v) { return $v !== 'progetti'; });
                    }

                    foreach ($menu as $label => $anchor) {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="#' . esc_attr($anchor) . '" aria-label="Vai a ' . esc_attr($label) . '">' . esc_html($label) . '</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </nav>

            <!-- Main content -->
            <div class="col-12 col-md-9">
                <?php
                // =====================
                // TEMPLATE RENDERING
                // =====================
                foreach ($menu as $label => $anchor) {
                    if ($anchor === 'descrizione') {
                        // Description section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        echo '<p>' . $description . '</p>';
                        echo '<br />';
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'uri') {
                        // URI section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body d-flex align-items-center">';
                        $assetIri = isset($data['assetIri']) ? $data['assetIri'] : '';
                        if ($assetIri) {
                            $readableIri = preg_replace('#^https?://#', '', rtrim($assetIri, '/'));
                            echo '<span class="me-3">' . esc_html($readableIri) . '</span>';
                            echo '<a href="' . esc_url($assetIri) . '" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">Vai all\'URL</a>';
                        } else {
                            echo '<span>-</span>';
                        }
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'soggetti') {
                        // Involved subjects section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        // Rights holder
                        if (!empty($data['rightsHolder']['summary'])) {
                            echo '<div class="cnd-sc-box">';
                            echo '<img src="' . home_url('/wp-content/plugins/wp-schema/assets/images/Gov.svg') . '" class="cnd-ico-ct" alt="Icona Titolari">&nbsp;&nbsp;';
                            echo '<strong>Titolare&nbsp;&nbsp;</strong>' . esc_html($data['rightsHolder']['summary']);
                            echo '</div>';
                        }
                        // Publishers
                        if (!empty($data['publishers'])) {
                            foreach ($data['publishers'] as $pub) {
                                echo '<div class="cnd-sc-box">';
                                echo '<img src="' . home_url('/wp-content/plugins/wp-schema/assets/images/User-Circle.svg') . '" class="cnd-ico-ct" alt="Icona Editore">&nbsp;&nbsp;';
                                echo '<strong>Editore&nbsp;&nbsp;</strong>' . esc_html($pub['summary']);
                                echo '</div>';
                            }
                        }
                        // Creators
                        if (!empty($data['creators'])) {
                            $creators = array_map(function($c) { return $c['summary']; }, $data['creators']);
                            echo '<div class="cnd-sc-box">';
                            echo '<img src="' . home_url('/wp-content/plugins/wp-schema/assets/images/User-Circle.svg') . '" class="cnd-ico-ct" alt="Icona Creatore">&nbsp;&nbsp;';
                            echo '<strong>Creatore&nbsp;&nbsp;</strong>' . esc_html(implode(', ', $creators));
                            echo '</div>';
                        }
                        echo '<br>';
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'concetti' && $has_keyclasses) {
                        // Main concepts section (only if present)
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        echo '<div class="d-flex flex-wrap gap-3">';
                        foreach ($data['keyClasses'] as $kc) {
                            $kc_label = isset($kc['summary']) ? $kc['summary'] : $kc['iri'];
                            $kc_iri = isset($kc['iri']) ? $kc['iri'] : '#';
                            echo '<a href="' . esc_url($kc_iri) . '" class="d-inline-flex align-items-center text-decoration-none fw-semibold text-primary" target="_blank" rel="noopener" style="margin-bottom: 8px;">';
                            echo esc_html($kc_label);
                            echo '<span class="ms-2" aria-hidden="true">';
                            echo '<svg class="icon custom-external-link-icon" aria-hidden="true" focusable="false" style="width:1em;height:1em;vertical-align:-0.125em;">';
                            echo '<use href="' . home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-external-link') . '"></use>';
                            echo '</svg>';
                            echo '</span>';
                            echo '</a>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'riferimenti') {
                        // Temporal references section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body" id="cnd-riferimenti-body">';
                        echo '<div><strong>Data di creazione:</strong> ' . $issuedOn . '</div>';
                        echo '<div><strong>Data di ultima modifica:</strong> ' . $modifiedOn . '</div>';
                        echo '<div><strong>Frequenza di aggiornamento:</strong> Irregolare</div>';
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'versione') {
                        // Version section as Bootstrap Italia accordion
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="accordion" id="accordion-versione">';
                        echo '  <div class="accordion-item">';
                        echo '    <h2 class="accordion-header" id="heading-versione">';
                        echo '      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-versione" aria-expanded="false" aria-controls="collapse-versione">';
                        echo '        <span class="fw-bold text-primary">Versione:</span>';
                        echo '      </button>';
                        echo '    </h2>';
                        echo '    <div id="collapse-versione" class="accordion-collapse collapse" aria-labelledby="heading-versione" data-bs-parent="#accordion-versione">';
                        echo '      <div class="accordion-body">';
                        echo '        ' . (!empty($versionInfo) ? esc_html($versionInfo) : '-') . '';
                        echo '      </div>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'altre') {
                        // Other information section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        // Prefix
                        if (!empty($data['prefix'])) {
                            echo '<div class="cnd-sc-box">';
                            echo '<strong>Prefisso&nbsp;&nbsp;</strong>' . esc_html($data['prefix']);
                            echo '</div>';
                        }
                        // Languages
                        if (!empty($data['languages']) && is_array($data['languages'])) {
                            $langs = array_map(function($l) use ($language_map) {
                                if (preg_match('/([A-Z]{3})$/', $l, $m)) {
                                    $code = $m[1];
                                    return isset($language_map[$code]) ? $language_map[$code] : $code;
                                }
                                return $l;
                            }, $data['languages']);
                            echo '<div class="cnd-sc-box">';
                            echo '<strong>Lingua&nbsp;&nbsp;</strong>' . esc_html(implode(', ', $langs));
                            echo '</div>';
                        }
                        // Conforms to
                        if (!empty($data['conformsTo']) && is_array($data['conformsTo'])) {
                            $conforms = array();
                            foreach ($data['conformsTo'] as $c) {
                                if (!empty($c['summary'])) {
                                    $conforms[] = $c['summary'];
                                } elseif (!empty($c['iri'])) {
                                    $conforms[] = $c['iri'];
                                }
                            }
                            if (!empty($conforms)) {
                                echo '<div class="cnd-sc-box">';
                                echo '<strong>Conforme a&nbsp;&nbsp;</strong>' . esc_html(implode(', ', $conforms));
                                echo '</div>';
                            }
                        }
                        // Endpoint URL
                        if (!empty($data['endpointUrl'])) {
                            echo '<div class="cnd-sc-box">';
                            echo '<strong>Indirizzo dell\'endpoint&nbsp;&nbsp;</strong>';
                            echo '<a href="' . esc_url($data['endpointUrl']) . '" target="_blank" rel="noopener">' . esc_html($data['endpointUrl']) . '</a>';
                            echo '</div>';
                        }
                        // Contact point
                        if (!empty($data['contactPoint']['summary'])) {
                            echo '<div class="cnd-sc-box">';
                            echo '<strong>Contatti&nbsp;&nbsp;</strong>' . esc_html($data['contactPoint']['summary']);
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'progetti' && $has_projects) {
                        // Projects section (only if present)
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        foreach ($data['projects'] as $proj) {
                            if (!empty($proj['summary'])) {
                                echo '<div class="bg-white rounded p-3 mb-2 fw-semibold">' . esc_html($proj['summary']) . '</div>';
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    } elseif ($anchor === 'editor-schema-api') {
                        // API schema editor section
                        echo '<div id="' . esc_attr($anchor) . '" class="it-card mb-4">';
                        echo '<div class="it-card-header">';
                        echo '<h5 class="it-card-title">' . esc_html($label) . '</h5>';
                        echo '</div>';
                        echo '<div class="it-card-body">';
                        // ...API schema editor content...
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <?php
    // =====================
    // DEBUG SECTION (commented, enable if needed)
    // =====================
    /*
    if (current_user_can('administrator')) { // Only administrators can see debug data
        echo '<div class="container mt-4 mb-4">';
        echo '<div class="card">';
        echo '<div class="card-header bg-light">';
        echo '<h5 class="mb-0">Debug - Dati API</h5>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">';
        echo htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo '</pre>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    */
    return ob_get_clean();
}
add_shortcode('display_api_data', 'display_api_data_shortcode');

function show_section($anchor, $menu) {
    return in_array($anchor, $menu);
}