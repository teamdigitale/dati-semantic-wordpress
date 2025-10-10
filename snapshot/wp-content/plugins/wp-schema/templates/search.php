<?php 
add_shortcode('custom_search_form', 'render_custom_search_form');

function render_custom_search_form() {
    ob_start();

    // Helper to get GET parameters as array
    function get_param_values($param_name) {
        if (isset($_GET[$param_name])) {
            $values = $_GET[$param_name];
            if (!is_array($values)) {
                $values = [$values];
            }
            return $values;
        }
        return [];
    }

    // Retrieve GET parameters
    $type_params        = get_param_values('type');
    $rightsHolder_params = get_param_values('rightsHolder');
    $theme_params       = get_param_values('theme');
    $q                  = isset($_GET['q']) ? $_GET['q'] : '';
    $sortBy             = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'TITLE';
    $direction          = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';
    $columns            = isset($_GET['columns']) ? intval($_GET['columns']) : 3;
    $offset             = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $limit              = isset($_GET['limit']) ? intval($_GET['limit']) : 12;

    // Get search results
    $results = get_assets($q, $type_params, $rightsHolder_params, $theme_params, $sortBy, $direction, $offset, $limit, $columns);
    ?>

    <!-- Search form -->
    <div id="search-form-box">
        <div class="cnd-src-col">
            <span class="SearchSelTitle" id="search-label">Cerca per parola chiave</span>
            <div class="SearchByText" id="SearchByText">
                <input type="text" id="searchTextInput" class="form-control" placeholder="Inserisci una o più parole chiave" value="<?php echo htmlspecialchars($q); ?>" aria-labelledby="search-label">
            </div>
        </div>
        <div class="cnd-src-col">
            <span class="SearchSelTitle">Filtra per strumento semantico
                <a href="#" data-bs-toggle="tooltip"  data-bs-placement="top"
                    title="Seleziona la tipologia di risorsa semantica tra ontologie, vocabolari controllati e schemi dati">
                    <svg class="icon-expand icon icon-sm icon-primary"><use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-info-circle'); ?>"></use></svg>
                </a>
            </span>
            <div class="MultiSelectFilter select-container" id="MultiSelectFilter">
                <div class="select-box">Scegli un'opzione</div>
                <div class="checkbox-container" role="group" aria-labelledby="filter-type">
                    <label for="check-ONTOLOGY"><input id="check-ONTOLOGY"class="form-check-input" name="type[]" type="checkbox" value="ONTOLOGY" <?php if (in_array('ONTOLOGY', $type_params)) echo 'checked'; ?>> Ontologia</label>
                    <label for="check-SCHEMA"><input id="check-SCHEMA"class="form-check-input" name="type[]" type="checkbox" value="SCHEMA" <?php if (in_array('SCHEMA', $type_params)) echo 'checked'; ?>> Schema</label>
                    <label for="check-CONTROLLED_VOCABULARY"><input id="check-CONTROLLED_VOCABULARY"class="form-check-input" name="type[]" type="checkbox" value="CONTROLLED_VOCABULARY" <?php if (in_array('CONTROLLED_VOCABULARY', $type_params)) echo 'checked'; ?>> Vocabolario Controllato</label>
                </div>
            </div>
        </div>
        <div class="cnd-src-col">
            <span class="SearchSelTitle" id="filter-rightsHolder-label">Filtra per titolare
                <a href="#" data-bs-toggle="tooltip"  data-bs-placement="top"
                    title="Scegli un'organizzazione responsabile delle risorse semantiche">
                    <svg class="icon-expand icon icon-sm icon-primary"><use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-info-circle'); ?>"></use></svg>
                </a>
            </span>
            <div class="rightsHolderFilter select-container" id="rightsHolderFilter">
                <div class="select-box" aria-haspopup="listbox" aria-labelledby="filter-rightsHolder-label">Scegli un'opzione</div>
                <div class="checkbox-container" role="group" aria-labelledby="filter-rightsHolder-label">
                    <?php
                    // Fetch rights holders from API
                    $api_url = WP_SCHEMA_API_BASE_URL .'semantic-assets/rights-holders';
                    $response = wp_remote_get($api_url);
                    if (is_wp_error($response)) {
                        echo 'Errore nella chiamata API.';
                    } else {
                        $body = wp_remote_retrieve_body($response);
                        $rights_holders = json_decode($body, true);
                        if (is_array($rights_holders)) {
                            foreach ($rights_holders as $holder) {
                                if (isset($holder['name']['it'])) {
                                    $value   = esc_attr($holder['identifier']);
                                    $label   = esc_html($holder['name']['it']);
                                    $checked = in_array($value, $rightsHolder_params) ? 'checked' : '';
                                    echo '<label for="check-' . $value . '"><input id="check-' . $value . '" class="form-check-input" type="checkbox" name="rightsHolder[]" value="' . $value . '" ' . $checked . '> ' . $label . '</label>';
                                }
                            }
                        } else {
                            echo 'Nessun dato disponibile dall\'API.';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="cnd-src-col">
            <span class="SearchSelTitle" id="filter-theme-label">Filtra per categoria
                <a href="#" data-bs-toggle="tooltip"  data-bs-placement="top"
                    title="Seleziona l'ambito o il tema  di tuo interesse">
                    <svg class="icon-expand icon icon-sm icon-primary"><use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-info-circle'); ?>"></use></svg>
                </a>
            </span>
            <div class="multiSelect-Filtra_per_Categoria select-container" id="multiSelect-Filtra_per_Categoria">
                <div class="select-box" aria-haspopup="listbox" aria-labelledby="filter-theme-label">Scegli un'opzione</div>
                <div class="checkbox-container" role="group" aria-labelledby="filter-theme-label">
                    <label for="check-AGRI"><input id="check-AGRI" class="form-check-input" name="theme[]" type="checkbox" value="AGRI" <?php if (in_array('AGRI', $theme_params)) echo 'checked'; ?>> Agricoltura, pesca, silvicoltura e prodotti alimentari</label>
                    <label for="check-ENVI"><input id="check-ENVI" class="form-check-input" name="theme[]" type="checkbox" value="ENVI" <?php if (in_array('ENVI', $theme_params)) echo 'checked'; ?>> Ambiente</label>
                    <label for="check-ECON"><input id="check-ECON" class="form-check-input" name="theme[]" type="checkbox" value="ECON" <?php if (in_array('ECON', $theme_params)) echo 'checked'; ?>> Economie e finanze</label>
                    <label for="check-ENER"><input id="check-ENER" class="form-check-input" name="theme[]" type="checkbox" value="ENER" <?php if (in_array('ENER', $theme_params)) echo 'checked'; ?>> Energia</label>
                    <label for="check-JUST"><input id="check-JUST" class="form-check-input" name="theme[]" type="checkbox" value="JUST" <?php if (in_array('JUST', $theme_params)) echo 'checked'; ?>> Giustizia, sistema giuridico e sicurezza pubblica</label>
                    <label for="check-EDUC"><input id="check-EDUC" class="form-check-input" name="theme[]" type="checkbox" value="EDUC" <?php if (in_array('EDUC', $theme_params)) echo 'checked'; ?>> Istruzione, cultura e sport</label>
                    <label for="check-SOCI"><input id="check-SOCI" class="form-check-input" name="theme[]" type="checkbox" value="SOCI" <?php if (in_array('SOCI', $theme_params)) echo 'checked'; ?>> Popolazione e società</label>
                    <label for="check-REGI"><input id="check-REGI" class="form-check-input" name="theme[]" type="checkbox" value="REGI" <?php if (in_array('REGI', $theme_params)) echo 'checked'; ?>> Regioni e città</label>
                    <label for="check-HEAL"><input id="check-HEAL" class="form-check-input" name="theme[]" type="checkbox" value="HEAL" <?php if (in_array('HEAL', $theme_params)) echo 'checked'; ?>> Salute</label>
                    <label for="check-GOVE"><input id="check-GOVE" class="form-check-input" name="theme[]" type="checkbox" value="GOVE" <?php if (in_array('GOVE', $theme_params)) echo 'checked'; ?>> Governo e settore pubblico</label>
                    <label for="check-TECH"><input id="check-TECH" class="form-check-input" name="theme[]" type="checkbox" value="TECH" <?php if (in_array('TECH', $theme_params)) echo 'checked'; ?>> Scienza e tecnologia</label>
                    <label for="check-INTR"><input id="check-INTR" class="form-check-input" name="theme[]" type="checkbox" value="INTR" <?php if (in_array('INTR', $theme_params)) echo 'checked'; ?>> Tematiche internazionali</label>
                    <label for="check-TRAN"><input id="check-TRAN" class="form-check-input" name="theme[]" type="checkbox" value="TRAN" <?php if (in_array('TRAN', $theme_params)) echo 'checked'; ?>> Trasporti</label>
                </div>
            </div>
        </div>
        <div class="cnd-src-col-lts">
            <button class="btn btn-primary"  id="apply-filters" >Applica</button>
        </div>
    </div>

    <!-- Display current selections (should be right after search-form-box) -->
    <div id="result"></div>

    <!-- Start of .row with three columns -->
    <div class="row cnd-fltr-rw" style="margin-top: 20px;">
        <!-- Display total results count -->
        <div class="results-count col-md-7 col-sm-12" id="results-count"><?php echo $results['totalResults']; ?> Risultati</div>
        <div class="col-md-3 col-sm-12 d-flex justify-content-end">
            <div id="columnsContainer" class="cnd-col-sel-cnt" role="group" aria-labelledby="columns-label">
                <span id="columns-label" class="sr-only">Seleziona il numero di colonne</span>
                <button id="columns3" class="columns-button <?php if($columns==3) echo 'active'; ?>" data-columns="3" aria-pressed="<?php echo $columns==3 ? 'true' : 'false'; ?>" aria-label="Visualizza risultati su 3 colonne">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__)); ?>assets/images/3col.svg" class="icon-columns" aria-hidden="true" alt="3 colonne"> 3 colonne
                </button>
                <button id="columns2" class="columns-button <?php if($columns==2) echo 'active'; ?>" data-columns="2" aria-pressed="<?php echo $columns==2 ? 'true' : 'false'; ?>" aria-label="Visualizza risultati su 2 colonne">
                    <img src="<?php echo plugin_dir_url(dirname(__FILE__)); ?>assets/images/2col.svg" class="icon-columns" aria-hidden="true" alt="2 colonne"> 2 colonne
                </button>
            </div>
        </div>
        <div class="col-md-2 col-sm-12 d-flex align-items-center">
            <div class="dropdown text-center">
                <a class="btn btn-dropdown dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Ordina risultati per">
                    <span id="current-selection">Ordina per:</span>
                    <svg class="icon-expand icon icon-sm icon-primary" aria-hidden="true"><use href="<?php echo home_url('/wp-content/plugins/wp-bootstrap-italia/dist/svg/sprites.svg#it-expand'); ?>"></use></svg>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <div class="link-list-wrapper">
                        <ul class="link-list">
                            <li><a class="dropdown-item list-item" href="#" data-value="MODIFIED_ON_DESC" <?php if($sortBy=='MODIFIED_ON' && $direction=='DESC') echo 'aria-current="true"'; ?>>Ultima modifica</a></li>
                            <li><a class="dropdown-item list-item" href="#" data-value="ISSUED_ON_ASC" <?php if($sortBy=='ISSUED_ON' && $direction=='ASC') echo 'aria-current="true"'; ?>>Data creazione</a></li>
                            <li><a class="dropdown-item list-item" href="#" data-value="TITLE_ASC" <?php if($sortBy=='TITLE' && $direction=='ASC') echo 'aria-current="true"'; ?>>Nome (A-z)</a></li>
                            <li><a class="dropdown-item list-item" href="#" data-value="TITLE_DESC" <?php if($sortBy=='TITLE' && $direction=='DESC') echo 'aria-current="true"'; ?>>Nome (Z-a)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Display results grid -->
    <div class="container-results">
        <div id="search-container" class="row row-cols-1 <?php echo $columns == 2 ? 'row-cols-md-2' : 'row-cols-md-3'; ?> g-3">
        <?php 
            if ($results['totalResults'] > 0) {
                echo $results['items_html']; 
            } else {
                // No results found
                echo '<div class="search-noresults">
                        <img src="' . plugin_dir_url(dirname(__FILE__)) . 'assets/images/no-result.svg" alt="No results"><br>
                        <h3 class="h3-nr">Nessun risultato trovato</h3>
                        <p class="txt-nr">La ricerca non ha prodotto nessun risultato, modifica i filtri o prova un\'altra chiave di ricerca.</p>
                      </div>';
            }
            ?>
        </div>
        <?php if (!empty($results['load_more_button_html'])) {
            echo $results['load_more_button_html'];
        } ?>
    </div>
    <?php
    return ob_get_clean();
}