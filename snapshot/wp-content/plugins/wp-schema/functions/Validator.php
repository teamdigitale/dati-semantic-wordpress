<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Shortcode per il validatore di metadati
add_shortcode('metadata_validator', 'render_metadata_validator');

function render_metadata_validator($atts) {
    $atts = shortcode_atts(array(
        'height' => '600px'
    ), $atts);
    
    // Genera un ID univoco per il form
    $form_id = 'metadata-validator-' . uniqid();
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($form_id); ?>" class="metadata-validator-container">
        <div class="validator-form">
            <h2>Validatore di Metadati</h2>
            <p>Seleziona per quale tipo di risorsa semantica vuoi validare il file Turtle</p>
            
            <form id="validator-form-<?php echo esc_attr($form_id); ?>" enctype="multipart/form-data">
                <div class="validator-options">
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="validation_type" value="ontology" checked>
                            <span class="radio-label">Ontologia</span>
                            <span class="info-icon" title="Validazione per file di ontologie">ⓘ</span>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="validation_type" value="controlled vocabulary">
                            <span class="radio-label">Vocabolario controllato</span>
                            <span class="info-icon" title="Validazione per vocabolari controllati">ⓘ</span>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="validation_type" value="schema">
                            <span class="radio-label">Schema dati</span>
                            <span class="info-icon" title="Validazione per schemi dati">ⓘ</span>
                        </label>
                    </div>
                </div>
                
                <div class="file-upload-section">
                    <h3>Allega il file Turtle</h3>
                    <div class="file-upload-area">
                        <input type="file" id="turtle-file-<?php echo esc_attr($form_id); ?>" name="turtle_file" accept=".ttl,.turtle" required>
                        <label for="turtle-file-<?php echo esc_attr($form_id); ?>" class="upload-button">
                            <span class="upload-icon">↑</span>
                            Upload
                        </label>
                        <div class="file-info" id="file-info-<?php echo esc_attr($form_id); ?>" style="display: none;">
                            <div class="file-details">
                                <span class="file-name"></span>
                                <span class="file-size"></span>
                                <span class="file-status">✓</span>
                            </div>
                            <button type="button" class="remove-file-btn" id="remove-file-<?php echo esc_attr($form_id); ?>">
                                🗑️ Rimuovi allegato
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="validator-actions">
                    <button type="submit" class="btn btn-primary validate-btn" id="validate-btn-<?php echo esc_attr($form_id); ?>">
                        Valida documento
                    </button>
                </div>
            </form>
            
            <div class="validation-results" id="validation-results-<?php echo esc_attr($form_id); ?>" style="display: none;">
                <h3>Risultati della validazione</h3>
                <div class="results-content"></div>
            </div>
        </div>
    </div>
    
    <style>
    .metadata-validator-container {
        width: 100%;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    .validator-form h2 {
        color: #333;
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 600;
    }
    
    .validator-form p {
        color: #666;
        margin-bottom: 30px;
        font-size: 16px;
    }
    
    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: #fff;
        min-width: 200px;
    }
    
    .radio-option:hover {
        border-color: #007cba;
        box-shadow: 0 2px 4px rgba(0,124,186,0.1);
    }
    
    .radio-option input[type="radio"] {
        margin: 0;
        width: 18px;
        height: 18px;
    }
    
    .radio-option input[type="radio"]:checked + .radio-label {
        color: #007cba;
        font-weight: 600;
    }
    
    .radio-option:has(input[type="radio"]:checked) {
        border-color: #007cba;
        background-color: #f0f8ff;
        box-shadow: 0 2px 8px rgba(0,124,186,0.15);
    }
    
    .info-icon {
        color: #999;
        font-size: 14px;
        cursor: help;
    }
    
    .file-upload-section h3 {
        color: #333;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .file-upload-area {
        position: relative;
        margin-bottom: 30px;
    }
    
    .file-upload-area input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .upload-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 30px;
        background: #007cba !important;
        color: white !important;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }
    
    .upload-button:hover {
        background: #005a87 !important;
    }
    
    .upload-button:focus {
        background: #007cba !important;
        outline: 2px solid #007cba;
        outline-offset: 2px;
    }
    
    .upload-icon {
        font-size: 18px;
    }
    
    .file-info {
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        display: none;
    }
    
    .file-details {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .file-name {
        font-weight: 500;
        color: #333;
    }
    
    .file-size {
        color: #666;
    }
    
    .file-status {
        color: #28a745;
        font-weight: bold;
    }
    
    .remove-file-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    
    .remove-file-btn:hover {
        background: #c82333;
    }
    
    .validator-actions {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }
    
    .validate-btn {
        padding: 15px 40px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
        min-width: 200px;
    }
    
    .validate-btn:hover {
        background: #218838;
    }
    
    .validate-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }
    
    .validation-results {
        margin-top: 30px;
        padding: 0;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .validation-results h3 {
        margin: 0;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        color: #333;
        font-size: 18px;
        font-weight: 600;
    }
    
    .results-content {
        padding: 20px;
        background: #fff;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    
    .alert-icon {
        flex-shrink: 0;
        margin-top: 2px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .alert-icon i {
        font-size: 18px;
    }
    
    .alert-content {
        flex: 1;
        margin-left: 0;
    }
    
    .alert-content h4 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .alert-content p {
        margin: 0;
        font-size: 14px;
    }
    
    /* Nuovo layout pulito per risultati validazione */
    .validation-success {
        text-align: center;
        padding: 40px 20px;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .success-icon {
        font-size: 48px;
        color: #28a745;
        margin-bottom: 20px;
    }
    
    .validation-success h2 {
        color: #155724;
        margin-bottom: 10px;
        font-size: 24px;
    }
    
    .validation-success p {
        color: #155724;
        font-size: 16px;
    }
    
    /* Alert informativi */
    .info-alert {
        margin: 20px 0;
        padding: 0 20px;
    }
    
    .info-alert .alert {
        border-radius: 4px;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .warning-alert .alert {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }
    
    .error-alert .alert {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding-left: 70px;
        position: relative;
    }
    
    .alert-icon-left {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #721c24;
    }
    
    /* Messaggio principale di fallimento */
    .main-error-message {
        margin: 40px 0;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        text-align: center;
    }
    
    .main-error-message h1 {
        color: #333;
        font-size: 28px;
        font-weight: 600;
        margin: 0;
        line-height: 1.2;
    }
    
    /* Sezioni errori e warning */
    .errors-section, .warnings-section {
        margin: 30px 0;
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }
    
    .errors-section h3, .warnings-section h3 {
        color: #333;
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 20px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .errors-list, .warnings-list {
        margin: 0;
        padding: 0;
    }
    
    .error-item, .warning-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 15px;
        padding: 8px 13px !important;
        transition: background-color 0.2s ease;
    }
    
    .error-item {
        padding: 8px 13px !important;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .error-item:hover, .warning-item:hover {
        background: #e9ecef;
    }
    
    .error-header, .warning-header {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        color: #007bff;
    }
    
    .error-content, .warning-content {
        color: #333;
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }
    
    .error-item {
        border-left: 4px solid #dc3545;
    }
    
    .warning-item {
        border-left: 4px solid #ffc107;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    
    .alert-warning {
        color: #856404;
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }
    
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    
    .error-list, .warning-list {
        margin-top: 15px;
        padding-left: 20px;
    }
    
    .error-list li, .warning-list li {
        margin-bottom: 8px;
        padding: 5px 0;
    }
    
    .loading {
        text-align: center;
        padding: 40px 20px;
        background: #f8f9fa;
    }
    
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007cba;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .error-details {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .error-details h5 {
        margin: 0 0 10px 0;
        color: #dc3545;
        font-size: 16px;
    }
    
    .error-item {
        padding: 8px 0;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .error-item:last-child {
        border-bottom: none;
    }
    
    .error-line {
        font-family: monospace;
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
        color: #6c757d;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        const formId = '<?php echo esc_js($form_id); ?>';
        const form = $('#validator-form-' + formId);
        const fileInput = $('#turtle-file-' + formId);
        const fileInfo = $('#file-info-' + formId);
        const validateBtn = $('#validate-btn-' + formId);
        const resultsDiv = $('#validation-results-' + formId);
        const resultsContent = resultsDiv.find('.results-content');
        const removeFileBtn = $('#remove-file-' + formId);
        
        console.log('Validator initialized for form:', formId);
        
        // Gestione selezione file
        fileInput.on('change', function() {
            console.log('File selected');
            const file = this.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024).toFixed(2) + ' KB';
                
                fileInfo.find('.file-name').text(fileName);
                fileInfo.find('.file-size').text(fileSize);
                fileInfo.show();
                
                console.log('File info displayed:', fileName, fileSize);
            } else {
                fileInfo.hide();
                console.log('File info hidden');
            }
        });
        
        // Gestione rimozione file
        removeFileBtn.on('click', function() {
            console.log('Remove file clicked');
            fileInput.val('');
            fileInfo.hide();
        });
        
        // Gestione submit form
        form.on('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const file = fileInput[0].files[0];
            const validationType = $('input[name="validation_type"]:checked').val();
            
            console.log('Validation type:', validationType);
            console.log('File:', file ? file.name : 'No file');
            
            if (!file) {
                showAlert('danger', 'Seleziona un file da validare');
                return;
            }
            
            // Mostra loading
            validateBtn.prop('disabled', true).text('Validazione in corso...');
            resultsDiv.show();
            resultsContent.html('<div class="loading"><div class="spinner"></div><p>Validazione in corso...</p></div>');
            
            console.log('Starting validation...');
            
            // Prepara FormData
            const formData = new FormData();
            formData.append('action', 'validate_metadata');
            formData.append('turtle_file', file);
            formData.append('validation_type', validationType);
            formData.append('nonce', '<?php echo wp_create_nonce('validate_metadata_nonce'); ?>');
            
            // Chiamata AJAX con timeout
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 60000, // 60 secondi
                success: function(response) {
                    console.log('AJAX Success:', response);
                    if (response.success) {
                        displayValidationResults(response.data);
                    } else {
                        displayError(response.data || 'Errore durante la validazione');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr, status, error);
                    let errorMessage = 'Errore di connessione';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Timeout: La validazione sta impiegando troppo tempo. Riprova con un file più piccolo.';
                    } else if (xhr.status === 413) {
                        errorMessage = 'File troppo grande. Dimensione massima: 10MB';
                    } else if (xhr.status === 400) {
                        errorMessage = 'File non valido. Verifica che sia un file Turtle (.ttl o .turtle)';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Errore interno del server. Riprova più tardi.';
                    }
                    
                    displayError(errorMessage);
                },
                complete: function() {
                    console.log('AJAX Complete');
                    validateBtn.prop('disabled', false).text('Valida documento');
                }
            });
        });
        
        function displayValidationResults(data) {
            console.log('Displaying validation results:', data);
            let html = '';
            
            if (data.valid) {
                // Successo - layout semplice
                html = '<div class="validation-success">';
                html += '<div class="success-icon"><i class="fas fa-check-circle"></i></div>';
                html += '<h2>Validazione completata con successo!</h2>';
                html += '<p>Il file è valido per il tipo selezionato.</p>';
                if (data.metadata) {
                    html += '<div class="metadata-info">';
                    html += '<h5>Informazioni del file:</h5>';
                    html += '<ul>';
                    if (data.metadata.title) html += '<li><strong>Titolo:</strong> ' + escapeHtml(data.metadata.title) + '</li>';
                    if (data.metadata.description) html += '<li><strong>Descrizione:</strong> ' + escapeHtml(data.metadata.description) + '</li>';
                    if (data.metadata.version) html += '<li><strong>Versione:</strong> ' + escapeHtml(data.metadata.version) + '</li>';
                    html += '</ul>';
                    html += '</div>';
                }
                html += '</div>';
            } else {
                // Fallimento - layout completo come nella seconda immagine
                
                // 1. Alert informativi in base al tipo di problemi
                if (data.warnings && data.warnings.length > 0 && (!data.errors || data.errors.length === 0)) {
                    // Solo warning
                    html += '<div class="info-alert warning-alert">';
                    html += '<div class="alert alert-warning m-0 w-100" role="alert">';
                    html += '<strong><span>In caso di soli messaggi di avvertimento (WARNING) il processo di harvester sarà in grado di acquisire i metadati. E\' consigliabile, ma non obbligatorio, procedere con la risoluzione degli avvertimenti segnalati.</span></strong>';
                    html += '</div>';
                    html += '</div>';
                }
                
                if (data.errors && data.errors.length > 0) {
                    // Con errori
                    html += '<div class="info-alert error-alert">';
                    html += '<div class="alert alert-danger m-0 w-100" role="alert">';
                    html += '<i class="fas fa-exclamation-triangle alert-icon-left"></i>';
                    html += '<strong><span>In caso di segnalazione di errori (ERROR) il processo di harvesting NON potrà acquisire i metadati, in questo caso è NECESSARIO procedere con la correzione di tutti gli errori segnalati prima di poter sottomettere il file Turtle al processo di harvesting.</span></strong>';
                    html += '</div>';
                    html += '</div>';
                }
                
                // 2. Messaggio principale di fallimento
                html += '<div class="main-error-message">';
                html += '<h1>Ci dispiace, il tuo file non risulta essere idoneo</h1>';
                html += '</div>';
                
                // 3. Lista errori
                if (data.errors && data.errors.length > 0) {
                    html += '<div class="errors-section">';
                    html += '<h3>LISTA ERRORI</h3>';
                    html += '<div class="errors-list">';
                    data.errors.forEach(function(error, index) {
                        html += '<div class="error-item">';
                        html += '<div class="error-header">Errore ' + (index + 1) + '</div>';
                        html += '<div class="error-content">' + escapeHtml(error.message || error) + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                    html += '</div>';
                }
                
                // 4. Lista warning
                if (data.warnings && data.warnings.length > 0) {
                    html += '<div class="warnings-section">';
                    html += '<h3>LISTA WARNING</h3>';
                    html += '<div class="warnings-list">';
                    data.warnings.forEach(function(warning, index) {
                        html += '<div class="warning-item">';
                        html += '<div class="warning-header">Warning ' + (index + 1) + '</div>';
                        html += '<div class="warning-content">' + escapeHtml(warning.message || warning) + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                    html += '</div>';
                }
            }
            
            resultsContent.html(html);
        }
        
        function displayError(message) {
            console.error('Displaying error:', message);
            const html = '<div class="alert alert-danger">';
            html += '<div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>';
            html += '<div class="alert-content">';
            html += '<h4>Errore durante la validazione</h4>';
            html += '<p>' + escapeHtml(message) + '</p>';
            html += '</div>';
            html += '</div>';
            resultsContent.html(html);
        }
        
        function showAlert(type, message) {
            const alertClass = 'alert-' + type;
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
            const html = '<div class="alert ' + alertClass + '">';
            html += '<div class="alert-icon"><i class="fas ' + iconClass + '"></i></div>';
            html += '<div class="alert-content">' + escapeHtml(message) + '</div>';
            html += '</div>';
            resultsDiv.show();
            resultsContent.html(html);
            
            // Auto-hide dopo 5 secondi per alert di successo
            if (type === 'success') {
                setTimeout(function() {
                    resultsDiv.hide();
                }, 5000);
            }
        }
        
        function escapeHtml(text) {
            if (typeof text !== 'string') return text;
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

// AJAX handler per la validazione
add_action('wp_ajax_validate_metadata', 'handle_validate_metadata');
add_action('wp_ajax_nopriv_validate_metadata', 'handle_validate_metadata');

function handle_validate_metadata() {
    // Verifica nonce
    if (!wp_verify_nonce($_POST['nonce'], 'validate_metadata_nonce')) {
        wp_send_json_error('Nonce verification failed');
        return;
    }
    
    // Verifica che sia una richiesta AJAX
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        wp_send_json_error('Invalid request');
        return;
    }
    
    // Verifica che sia stato caricato un file
    if (!isset($_FILES['turtle_file']) || $_FILES['turtle_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('Nessun file caricato o errore nel caricamento');
        return;
    }
    
    // Verifica il tipo di validazione
    $validation_type = sanitize_text_field($_POST['validation_type']);
    if (!in_array($validation_type, ['ontology', 'controlled vocabulary', 'schema'])) {
        wp_send_json_error('Tipo di validazione non valido');
        return;
    }
    
    // Verifica estensione file
    $file_info = pathinfo($_FILES['turtle_file']['name']);
    if (!in_array(strtolower($file_info['extension']), ['ttl', 'turtle'])) {
        wp_send_json_error('Il file deve essere un file Turtle (.ttl o .turtle)');
        return;
    }
    
    // Verifica dimensione file (max 10MB)
    if ($_FILES['turtle_file']['size'] > 10 * 1024 * 1024) {
        wp_send_json_error('Il file è troppo grande. Dimensione massima: 10MB');
        return;
    }
    
    try {
        // Chiama l'API di validazione
        $result = validate_turtle_file($_FILES['turtle_file'], $validation_type);
        
        if ($result === false) {
            wp_send_json_error('Errore durante la chiamata all\'API di validazione');
            return;
        }
        
        wp_send_json_success($result);
        
    } catch (Exception $e) {
        wp_send_json_error('Errore interno: ' . $e->getMessage());
    }
}

function validate_turtle_file($file, $validation_type) {
        // Mappa i tipi di validazione ai valori accettati dall'API
        // Dalla collection Postman, i tipi corretti sono: ontology, controlled vocabulary, schema
        $type_mapping = array(
            'ontology' => 'ontology',
            'controlled vocabulary' => 'controlled vocabulary',  // Usa spazio per l'API
            'schema' => 'schema'
        );

        $api_type = isset($type_mapping[$validation_type]) ? $type_mapping[$validation_type] : $validation_type;

        // URL dell'API di validazione
        $api_url = WP_SCHEMA_API_BASE_URL . 'validate?type=' . urlencode($api_type);

        error_log('Validating file: ' . $file['name'] . ' with type: ' . $validation_type . ' -> ' . $api_type);
        error_log('API URL: ' . $api_url);
        error_log('Encoded type: ' . urlencode($api_type));
        error_log('File size: ' . $file['size'] . ' bytes');
        error_log('File type: ' . $file['type']);
    
    // Prepara i dati per la chiamata API con multipart/form-data
    $boundary = wp_generate_password(16, false);
    $body = '';
    
    // Aggiungi il file
    $body .= '--' . $boundary . "\r\n";
    $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($file['name']) . '"' . "\r\n";
    $body .= 'Content-Type: text/turtle' . "\r\n\r\n";
    $body .= file_get_contents($file['tmp_name']) . "\r\n";
    $body .= '--' . $boundary . '--' . "\r\n";
    
    error_log('Request body size: ' . strlen($body) . ' bytes');
    error_log('Boundary: ' . $boundary);
    error_log('File content preview: ' . substr(file_get_contents($file['tmp_name']), 0, 200) . '...');
    
    // Headers per la richiesta
    $headers = array(
        'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
        'Accept' => 'application/json',
        'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
    );
    
    // Opzioni per wp_remote_post
    $args = array(
        'method' => 'POST',
        'headers' => $headers,
        'body' => $body,
        'timeout' => 60, // 60 secondi di timeout
        'sslverify' => true
    );
    
    // Esegui la chiamata API
    $response = wp_remote_post($api_url, $args);
    
    // Gestisci errori di connessione
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log('Errore API validazione: ' . $error_message);
        throw new Exception('Errore di connessione all\'API: ' . $error_message);
    }
    
    // Check response code
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_headers = wp_remote_retrieve_headers($response);
    
    error_log('API Response Code: ' . $response_code);
    error_log('API Response Headers: ' . print_r($response_headers, true));
    error_log('API Response Body: ' . $response_body);
    
    if ($response_code !== 200) {
        error_log('Errore API validazione - Codice: ' . $response_code . ' - Body: ' . $response_body);
        
        // Try to decode response to get error details
        $error_data = json_decode($response_body, true);
        $error_message = 'Errore del server (codice: ' . $response_code . ')';
        
        if ($error_data && isset($error_data['message'])) {
            $error_message = $error_data['message'];
        } elseif ($error_data && isset($error_data['title'])) {
            $error_message = $error_data['title'];
        } elseif ($response_code === 400) {
            $error_message = 'File non valido. Verifica che sia un file Turtle corretto.';
        } elseif ($response_code === 413) {
            $error_message = 'File troppo grande. Dimensione massima: 10MB';
        } elseif ($response_code === 500) {
            $error_message = 'Errore interno del server di validazione. Riprova più tardi.';
        }
        
        throw new Exception($error_message);
    }
    
    // Check if response is JSON or HTML
    $content_type = wp_remote_retrieve_header($response, 'content-type');
    error_log('Response Content-Type: ' . $content_type);
    
    if (strpos($content_type, 'application/json') !== false) {
        // JSON response
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Errore decodifica JSON: ' . json_last_error_msg() . ' - Body: ' . $response_body);
            throw new Exception('Risposta non valida dal server di validazione');
        }
        
        // Format response for frontend
        $formatted_result = array(
            'valid' => isset($data['valid']) ? (bool) $data['valid'] : false,
            'errors' => isset($data['errors']) && is_array($data['errors']) ? $data['errors'] : array(),
            'warnings' => isset($data['warnings']) && is_array($data['warnings']) ? $data['warnings'] : array(),
            'metadata' => isset($data['metadata']) ? $data['metadata'] : null
        );
    } else {
        // HTML response - parse errors and warnings
        $formatted_result = parse_html_validation_response($response_body);
    }
    
    error_log('Formatted result: ' . print_r($formatted_result, true));
    
    return $formatted_result;
}

function parse_html_validation_response($html) {
    error_log('Parsing HTML response: ' . substr($html, 0, 500) . '...');
    
    // Check if file is valid (no errors)
    $is_valid = strpos($html, 'Ci dispiace, il tuo file non risulta essere idoneo') === false;
    
    $errors = array();
    $warnings = array();
    
    if (!$is_valid) {
        // Extract errors
        if (preg_match_all('/<div class="h6[^"]*"[^>]*>Errore (\d+)<\/div><span[^>]*>([^<]+)<\/span>/', $html, $error_matches, PREG_SET_ORDER)) {
            foreach ($error_matches as $match) {
                $errors[] = array(
                    'message' => trim($match[2]),
                    'line' => null,
                    'column' => null
                );
            }
        }
        
        // Extract warnings
        if (preg_match_all('/<div class="h6[^"]*"[^>]*>Warning (\d+)<\/div><span[^>]*>([^<]+)<\/span>/', $html, $warning_matches, PREG_SET_ORDER)) {
            foreach ($warning_matches as $match) {
                $warnings[] = array(
                    'message' => trim($match[2]),
                    'line' => null,
                    'column' => null
                );
            }
        }
    }
    
    $result = array(
        'valid' => $is_valid,
        'errors' => $errors,
        'warnings' => $warnings,
        'metadata' => null
    );
    
    error_log('Parsed HTML result: ' . print_r($result, true));
    
    return $result;
}

// Function to register necessary scripts
function enqueue_validator_scripts() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_validator_scripts');