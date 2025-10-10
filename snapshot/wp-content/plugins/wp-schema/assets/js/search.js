function closeAllDropdowns() {
    var dropdowns = document.querySelectorAll('.checkbox-container');
    dropdowns.forEach(function(dropdown) {
        dropdown.style.display = 'none';
    });
}

// Aggiunge l'evento di click a tutte le select-box
var selectBoxes = document.querySelectorAll('.select-box');
selectBoxes.forEach(function(selectBox) {
    selectBox.addEventListener('click', function(event) {
        event.stopPropagation(); // Previene la propagazione dell'evento
        var checkboxContainer = this.nextElementSibling;
        if (checkboxContainer.style.display === 'block') {
            checkboxContainer.style.display = 'none';
        } else {
            closeAllDropdowns(); // Chiude gli altri dropdown aperti
            checkboxContainer.style.display = 'block';
        }
    });
});

// Chiude i dropdown quando si clicca fuori
document.addEventListener('click', function(event) {
    if (!event.target.matches('.select-box') && !event.target.closest('.checkbox-container')) {
        closeAllDropdowns();
    }
});

// Funzione per raccogliere le selezioni e aggiornare l'URL con i parametri GET
function submitSelection() {
    var urlParams = new URLSearchParams(window.location.search);
    var searchQuery = urlParams.get('q') || '';
    var type = urlParams.getAll('type[]');
    var rightsHolder = urlParams.getAll('rightsHolder[]');
    var theme = urlParams.getAll('theme[]');
    
    // Get sort values from orderByInput
    var orderInput = document.getElementById('orderByInput');
    if (!orderInput) {
        return;
    }
    
    var data = {
        action: 'load_search_results',
        q: searchQuery,
        type: type,
        rightsHolder: rightsHolder,
        theme: theme,
        columns: urlParams.get('columns') || 3,
        offset: 0,
        limit: 12
    };

    // Add sorting parameters if they are present in URL or if a new sorting has been selected
    if (urlParams.has('sortBy') || orderInput.value !== 'TITLE_ASC') {
        var orderValue = orderInput.value;
        var [sortBy, direction] = orderValue.split('_');

        // Gestione speciale per MODIFIED_ON e ISSUED_ON
        if (sortBy === 'MODIFIED' || sortBy === 'ISSUED') {
            sortBy = sortBy + '_ON';
            direction = direction === 'DESC' ? 'DESC' : 'ASC';
        }

        data.sortBy = sortBy;
        if (direction) {
            data.direction = direction;
        }
    }

    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                jQuery('#search-container').html(response.data.items_html);
                
                // Aggiorna il conteggio dei risultati
                var resultsCount = response.data.totalResults || 0;
                var resultsCountText = resultsCount === 0 ? 'Nessun risultato trovato' : resultsCount + ' Risultati';
                jQuery('#results-count').text(resultsCountText);

                // Handle "show more results" button
                var loadMoreContainer = jQuery('.load-more-container');
                if (response.data.load_more_button_html) {
                    if (loadMoreContainer.length) {
                        loadMoreContainer.replaceWith(response.data.load_more_button_html);
                    } else {
                        jQuery('#search-container').after(response.data.load_more_button_html);
                    }
                    // Assicurati che il container sia visibile
                    jQuery('.load-more-container').show();
                } else {
                    if (loadMoreContainer.length) {
                        loadMoreContainer.hide();
                    }
                }
                
                // AGGIORNA L'URL CON OFFSET=0 PER NUOVA RICERCA
                var currentUrlParams = new URLSearchParams(window.location.search);
                currentUrlParams.set('offset', '0');
                window.history.replaceState({}, '', '?' + currentUrlParams.toString());

                // Aggiorna le chips dopo la ricerca
                displaySelectedChips();
            } else {
                console.error('Errore nella risposta AJAX');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore AJAX:', error);
        }
    });
}

// Funzione per visualizzare le chip all'avvio
function displaySelectedChips() {
    var resultDiv = document.getElementById('result');
    if (!resultDiv) {
        return;
    }
    
    resultDiv.innerHTML = ''; // Pulisce il contenuto precedente

    var params = new URLSearchParams(window.location.search);

    // Aggiungi la chip per il testo di ricerca
    var searchText = params.get('q');
    if (searchText) {
        var span = document.createElement('span');
        span.classList.add('chip');
        span.textContent = 'Parola: ' + searchText;

        var closeBtn = document.createElement('span');
        closeBtn.classList.add('close-button');
        closeBtn.innerHTML = '&times;';
        closeBtn.dataset.paramName = 'q';
        closeBtn.dataset.paramValue = searchText;

        closeBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            removeSelection(this.dataset.paramName, this.dataset.paramValue);
        });

        span.appendChild(closeBtn);
        resultDiv.appendChild(span);
    }

    // Selezioni per ogni filtro
    var filters = [
        {paramName: 'type', prefix: 'Strumento: '},
        {paramName: 'rightsHolder', prefix: 'Titolare: '},
        {paramName: 'theme', prefix: 'Categoria: '}
    ];

    filters.forEach(function(filter) {
        // Leggiamo i parametri con filter.paramName + '[]'
        var values = params.getAll(filter.paramName + '[]');
        values.forEach(function(value) {
            var labelText = getLabelText(filter.paramName, value);
            if (labelText) {
                var span = document.createElement('span');
                span.classList.add('chip');
                span.textContent = filter.prefix + labelText;

                var closeBtn = document.createElement('span');
                closeBtn.classList.add('close-button');
                closeBtn.innerHTML = '&times;';

                closeBtn.dataset.paramName = filter.paramName;
                closeBtn.dataset.paramValue = value;

                closeBtn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    removeSelection(this.dataset.paramName, this.dataset.paramValue);
                });

                span.appendChild(closeBtn);
                resultDiv.appendChild(span);
            }
        });
    });

    // Verifica se ci sono filtri attivi
    var hasActiveFilters = false;
    
    // Check if there is search text
    if (params.get('q')) {
        hasActiveFilters = true;
    }
    
    // Controlla se ci sono parametri di filtro
    if (!hasActiveFilters) {
        filters.forEach(function(filter) {
            if (params.getAll(filter.paramName + '[]').length > 0) {
                hasActiveFilters = true;
            }
        });
    }

    // Aggiungi il pulsante "Annulla filtri" se ci sono filtri attivi
    if (hasActiveFilters) {
        var resetButton = document.createElement('button');
        resetButton.id = 'reset-filters';
        resetButton.classList.add('btn', 'btn-link', 'reset-filters');
        resetButton.textContent = 'Annulla filtri';
        
        // Aggiungi l'event listener direttamente qui
        resetButton.addEventListener('click', function(e) {
            e.preventDefault();
            resetFilters();
        });
        
        resultDiv.appendChild(resetButton);
    } else {
        resultDiv.textContent = 'Nessuna selezione effettuata.';
    }
}

// Funzione per ottenere l'etichetta corrispondente al valore
function getLabelText(paramName, value) {
    // Cerchiamo i checkbox con name="paramName[]"
    var checkboxes = document.querySelectorAll('input[name="' + paramName + '[]"]');
    var labelText = null;
    checkboxes.forEach(function(checkbox) {
        if (checkbox.value === value) {
            labelText = checkbox.parentNode.textContent.trim();
        }
    });
    return labelText;
}

// Funzione per rimuovere una selezione e aggiornare l'URL
function removeSelection(paramName, paramValue) {
    var url = new URL(window.location.href);
    var params = new URLSearchParams(url.search);

    if (paramName === 'q') {
        // If it's the search parameter, remove it directly
        params.delete('q');
    } else {
        // Rimuoviamo il valore da paramName + '[]'
        var allValues = params.getAll(paramName + '[]');
        var index = allValues.indexOf(paramValue);
        if (index > -1) {
            allValues.splice(index, 1);
        }

        // Cancella il parametro e riaggiungi i valori rimanenti
        params.delete(paramName + '[]');
        allValues.forEach(function(value) {
            params.append(paramName + '[]', value);
        });
    }

    url.search = params.toString();
    window.location.href = url.toString();
}

// Funzione per aggiornare il testo dei select-box in base alle selezioni
function updateSelectBoxText() {
    var filters = [
        {className: 'MultiSelectFilter', singular: 'Strumento selezionato', plural: 'Strumenti selezionati'},
        {className: 'rightsHolderFilter', singular: 'Titolare selezionato', plural: 'Titolari selezionati'},
        {className: 'multiSelect-Filtra_per_Categoria', singular: 'Categoria selezionata', plural: 'Categorie selezionate'}
    ];

    filters.forEach(function(filter) {
        var container = document.querySelector('.' + filter.className);
        if (!container) return;
        var selectBox = container.querySelector('.select-box');
        var checkboxes = container.querySelectorAll('input[type="checkbox"]');
        var count = 0;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                count++;
            }
        });
        if (count === 0) {
            selectBox.textContent = 'Scegli un\'opzione';
        } else if (count === 1) {
            selectBox.textContent = '1 ' + filter.singular;
        } else {
            selectBox.textContent = count + ' ' + filter.plural;
        }
    });
}

// Aggiungi event listener alle checkbox per aggiornare il testo dei select-box
var allCheckboxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
allCheckboxes.forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        updateSelectBoxText();
    });
});

function loadMoreResults() {
    var urlParams = new URLSearchParams(window.location.search);
    var offset = parseInt(urlParams.get('offset')) || 0;
    var limit = parseInt(urlParams.get('limit')) || 12;

    var data = {
        action: 'load_more_results',
        q: urlParams.get('q') || '',
        type: urlParams.getAll('type[]'),
        rightsHolder: urlParams.getAll('rightsHolder[]'),
        theme: urlParams.getAll('theme[]'),
        sortBy: urlParams.get('sortBy') || 'TITLE',
        direction: urlParams.get('direction') || 'ASC',
        columns: urlParams.get('columns') || 3,
        offset: offset + limit,
        limit: limit,
    };

    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                jQuery('#search-container').append(response.data.items_html);
                
                // Aggiorna il conteggio dei risultati
                var resultsCount = response.data.totalResults || 0;
                var resultsCountText = resultsCount === 0 ? 'Nessun risultato trovato' : resultsCount + ' Risultati';
                var resultsCountEl = document.getElementById('results-count');
                if (resultsCountEl) {
                    resultsCountEl.textContent = resultsCountText;
                }

                // Handle "show more results" button
                var loadMoreContainer = jQuery('.load-more-container');
                if (response.data.load_more_button_html) {
                    if (loadMoreContainer.length) {
                        loadMoreContainer.replaceWith(response.data.load_more_button_html);
                    } else {
                        jQuery('#search-container').after(response.data.load_more_button_html);
                    }
                    // Assicurati che il container sia visibile
                    jQuery('.load-more-container').show();
                } else {
                    if (loadMoreContainer.length) {
                        loadMoreContainer.hide();
                    }
                }
                
                // AGGIORNA L'URL CON IL NUOVO OFFSET
                var newOffset = offset + limit;
                var currentUrlParams = new URLSearchParams(window.location.search);
                currentUrlParams.set('offset', newOffset);
                window.history.replaceState({}, '', '?' + currentUrlParams.toString());

                // Aggiorna le chips dopo la ricerca
                if (typeof displaySelectedChips === 'function') {
                    displaySelectedChips();
                }
            } else {
                console.error('Errore nella risposta AJAX');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore AJAX:', error);
        }
    });
}

function updateColumns(columns) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('columns', columns);
    window.history.replaceState({}, '', '?' + urlParams.toString());

    const searchContainer = document.getElementById('search-container');
    if (searchContainer) {
        if (columns == '2') {
            searchContainer.classList.remove('row-cols-md-3');
            searchContainer.classList.add('row-cols-md-2');
        } else {
            searchContainer.classList.remove('row-cols-md-2');
            searchContainer.classList.add('row-cols-md-3');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inizializza il valore della casella di ricerca dall'URL
    var urlParams = new URLSearchParams(window.location.search);
    var searchText = urlParams.get('q');
    if (searchText) {
        document.getElementById('searchTextInput').value = searchText;
    }

    const navLinks = document.querySelectorAll('#navbar .nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            // Previene il comportamento di default per i link con anchor
            event.preventDefault();

            // Rimuove 'active' da tutte le voci
            navLinks.forEach(item => item.classList.remove('active'));

            // Aggiunge 'active' alla voce cliccata
            link.classList.add('active');

            // Gestisce lo scroll alla sezione specificata
            const targetId = link.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    var orderInput = document.getElementById('orderByInput');
    if (!orderInput) {
        orderInput = document.createElement('input');
        orderInput.type = 'hidden';
        orderInput.id = 'orderByInput';

        var params = new URLSearchParams(window.location.search);
        var currentSortBy = params.get('sortBy') || 'TITLE';
        var currentDirection = params.get('direction') || 'ASC';
        var combinedValue = currentSortBy + '_' + currentDirection;
        var allowedValues = ['MODIFIED_ON_DESC','ISSUED_ON_ASC','TITLE_ASC','TITLE_DESC'];
        if (!allowedValues.includes(combinedValue)) {
            combinedValue = 'TITLE_ASC';
        }
        orderInput.value = combinedValue;
        document.body.appendChild(orderInput);
    }

    var orderDropdownItems = document.querySelectorAll('.dropdown-item.list-item[data-value]');
    orderDropdownItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var orderValue = this.getAttribute('data-value');
            var orderInput = document.getElementById('orderByInput');
            if (orderInput) {
                orderInput.value = orderValue;
            }
            
            // Rimuovi aria-current="true" da tutti gli elementi del dropdown
            orderDropdownItems.forEach(function(dropdownItem) {
                dropdownItem.removeAttribute('aria-current');
            });
            // Aggiungi aria-current="true" all'elemento selezionato
            this.setAttribute('aria-current', 'true');
            
            // Aggiorna il testo del dropdown
            var currentSelection = document.getElementById('current-selection');
            if (currentSelection) {
                currentSelection.textContent = this.textContent;
            }

            // Aggiorna l'URL con i parametri di ordinamento
            var urlParams = new URLSearchParams(window.location.search);
            var [sortBy, direction] = orderValue.split('_');
            
            // Gestione speciale per MODIFIED_ON e ISSUED_ON
            if (sortBy === 'MODIFIED' || sortBy === 'ISSUED') {
                sortBy = sortBy + '_ON';
                direction = direction === 'DESC' ? 'DESC' : 'ASC';
            }
            
            urlParams.set('sortBy', sortBy);
            if (direction) {
                urlParams.set('direction', direction);
            }
            window.history.replaceState({}, '', '?' + urlParams.toString());
            
            submitSelection();
        });
    });

    var searchTextInput = document.getElementById('searchTextInput');
    if (searchTextInput) {
        searchTextInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                document.getElementById('apply-filters').click();
            }
        });
    }

    const buttons = document.querySelectorAll('.columns-button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            buttons.forEach(btn => {
                btn.classList.remove('active');
                btn.setAttribute('aria-pressed', 'false');
            });
            this.classList.add('active');
            this.setAttribute('aria-pressed', 'true');
            const columns = this.getAttribute('data-columns');
            updateColumns(columns);
        });
    });

    // All'avvio, mostra le chip e aggiorna i testi
    displaySelectedChips();
    updateSelectBoxText();

    // Aggiungi event listener alle checkbox per aggiornare le chips
    var allCheckboxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
    allCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            displaySelectedChips();
        });
    });

    // Update current selection text on page load
    var orderInput = document.getElementById('orderByInput');
    var currentSelection = document.getElementById('current-selection');
    
    // Imposta il testo predefinito
    if (currentSelection) {
        currentSelection.textContent = 'Ordina per:';
    }

    // Update text only if there is a specific selection in URL
    if (orderInput) {
        var orderValue = orderInput.value;
        var selectedItem = document.querySelector(`.dropdown-item.list-item[data-value="${orderValue}"]`);
        if (currentSelection && selectedItem && window.location.search.includes('sortBy')) {
            currentSelection.textContent = selectedItem.textContent;
            
            // Rimuovi aria-current="true" da tutti gli elementi del dropdown
            orderDropdownItems.forEach(function(dropdownItem) {
                dropdownItem.removeAttribute('aria-current');
            });
            // Aggiungi aria-current="true" all'elemento selezionato
            selectedItem.setAttribute('aria-current', 'true');
        }
    }
});

document.addEventListener('scroll', function () {
    const scrollTop = window.scrollY;
    const docHeight = document.body.scrollHeight - window.innerHeight;
    const progress = (scrollTop / docHeight) * 100;
    
    // Cerca la progress bar esistente
    var progressBar = document.querySelector('.cnd-progress-bar');
    
    // Se non esiste, creala
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.className = 'cnd-progress-bar';
        progressBar.style.position = 'fixed';
        progressBar.style.top = '0';
        progressBar.style.left = '0';
        progressBar.style.height = '4px';
        progressBar.style.backgroundColor = '#0066cc';
        progressBar.style.transition = 'width 0.2s ease';
        progressBar.style.zIndex = '9999';
        document.body.appendChild(progressBar);
    }
    
    // Aggiorna la larghezza
    progressBar.style.width = progress + '%';
});

var applyBtn = document.getElementById('apply-filters');
if (applyBtn) {
    applyBtn.addEventListener('click', function () {
        // Aggiorna prima l'URL con i filtri
        updateURLWithFilters();
        // Poi esegui la ricerca
        submitSelection();
    });
}

function updateURLWithFilters() {
    const params = new URLSearchParams();

    const q = document.querySelector('#searchTextInput')?.value;
    if (q) params.set('q', q);

    document.querySelectorAll('input[name="type[]"]:checked').forEach(el => {
        params.append('type[]', el.value);
    });

    document.querySelectorAll('input[name="rightsHolder[]"]:checked').forEach(el => {
        params.append('rightsHolder[]', el.value);
    });

    document.querySelectorAll('input[name="theme[]"]:checked').forEach(el => {
        params.append('theme[]', el.value);
    });

    // Add sorting parameters if they are present in URL or if a new sorting has been selected
    const orderInput = document.getElementById('orderByInput');
    if (orderInput && (window.location.search.includes('sortBy') || orderInput.value !== 'TITLE_ASC')) {
        const [sortBy, direction] = orderInput.value.split('_');
        
        // Gestione speciale per MODIFIED_ON e ISSUED_ON
        if (sortBy === 'MODIFIED' || sortBy === 'ISSUED') {
            params.set('sortBy', sortBy + '_ON');
            params.set('direction', direction === 'DESC' ? 'DESC' : 'ASC');
        } else {
            params.set('sortBy', sortBy);
            if (direction) params.set('direction', direction);
        }
    }

    const colBtn = document.querySelector('.columns-button.active');
    const columns = colBtn ? colBtn.getAttribute('data-columns') : '3';
    params.set('columns', columns);
    params.set('offset', 0);
    params.set('limit', 12);

    const newUrl = window.location.pathname + '?' + params.toString();
    window.history.replaceState({}, '', newUrl);
}

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);

    const q = params.get('q');
    if (q && document.querySelector('#searchTextInput')) {
        document.querySelector('#searchTextInput').value = q;
    }

    params.getAll('type[]').forEach(val => {
        const el = document.querySelector(`input[name="type[]"][value="${val}"]`);
        if (el) el.checked = true;
    });

    params.getAll('rightsHolder[]').forEach(val => {
        const el = document.querySelector(`input[name="rightsHolder[]"][value="${val}"]`);
        if (el) el.checked = true;
    });

    params.getAll('theme[]').forEach(val => {
        const el = document.querySelector(`input[name="theme[]"][value="${val}"]`);
        if (el) el.checked = true;
    });

    const sortBy = params.get('sortBy');
    if (sortBy) {
        const el = document.querySelector(`input[name="sortBy"][value="${sortBy}"]`);
        if (el) el.checked = true;
    }

    const direction = params.get('direction');
    if (direction) {
        const el = document.querySelector(`input[name="direction"][value="${direction}"]`);
        if (el) el.checked = true;
    }

    const col = params.get('columns');
    if (col) {
        const el = document.querySelector(`.columns-button[data-columns="${col}"]`);
        if (el) el.click();
    }
});

// Funzione per resettare i filtri
function resetFilters() {
    // Crea una Promise per gestire le operazioni asincrone
    new Promise((resolve) => {
        // Resetta la casella di ricerca
        var searchTextInput = document.getElementById('searchTextInput');
        if (searchTextInput) {
            searchTextInput.value = '';
        }
        
        // Deseleziona tutte le checkbox
        document.querySelectorAll('.checkbox-container input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.checked = false;
        });
        
        // Resetta l'ordinamento a quello predefinito
        var orderInput = document.getElementById('orderByInput');
        if (orderInput) {
            orderInput.value = 'TITLE_ASC';
        }
        
        // Resetta le colonne a 3
        var columnsButton = document.querySelector('.columns-button[data-columns="3"]');
        if (columnsButton) {
            columnsButton.click();
        }
        
        // Pulisci immediatamente le chips e il pulsante "Annulla filtri"
        var resultDiv = document.getElementById('result');
        if (resultDiv) {
            resultDiv.innerHTML = 'Nessuna selezione effettuata.';
        }
        
        // Aggiorna immediatamente il testo dei select-box
        updateSelectBoxText();
        
        // Crea un nuovo URL con i parametri di default
        var newUrl = window.location.pathname;
        window.history.pushState({}, '', newUrl);
        
        // Forza un reflow del DOM per assicurarci che tutte le modifiche siano applicate
        document.body.offsetHeight;
        
        resolve();
    }).then(() => {
        // Dopo che tutte le operazioni sono state completate, esegui direttamente la ricerca
        var data = {
            action: 'load_search_results',
            q: '',
            type: [],
            rightsHolder: [],
            theme: [],
            sortBy: 'TITLE',
            direction: 'ASC',
            columns: 3
        };

        jQuery.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiorna i risultati
                    jQuery('#search-container').html(response.data.items_html);
                    
                    // Aggiorna il conteggio dei risultati
                    var resultsCount = response.data.totalResults || 0;
                    var resultsCountText = resultsCount === 0 ? 'Nessun risultato trovato' : resultsCount + ' Risultati';
                    jQuery('#results-count').text(resultsCountText);

                    // Handle "show more results" button
                    if (response.data.load_more_button_html) {
                        if (jQuery('.load-more-container').length) {
                            jQuery('.load-more-container').replaceWith(response.data.load_more_button_html);
                        } else {
                            jQuery('#search-container').after(response.data.load_more_button_html);
                        }
                    } else {
                        var loadMoreContainer = document.querySelector('.load-more-container');
                        if (loadMoreContainer) {
                            loadMoreContainer.style.display = 'none';
                        }
                    }
                } else {
                    console.error('Errore nella risposta AJAX');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore AJAX:', error);
            }
        });
    });
}