<?php 

add_shortcode('swagger-ui', 'render_swagger_ui');
add_shortcode('swagger-ui-api', 'render_swagger_ui_api');

function render_swagger_ui($atts) {
    // URL del file YAML locale
    $plugin_url = plugin_dir_url(dirname(__FILE__));
    $local_yaml_url = $plugin_url . 'assets/yaml/openapi.yaml';
    
    $atts = shortcode_atts(array(
        'url' => $local_yaml_url,
        'height' => '600px',
        'layout' => 'BaseLayout'
    ), $atts);
    
    // Genera un ID univoco una sola volta
    $container_id = 'swagger-ui-' . uniqid();
    
    ob_start();
    ?>
    <div id="<?php echo $container_id; ?>" class="swagger-container" style="height: <?php echo esc_attr($atts['height']); ?>; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    </div>
    
    <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/css/swagger-ui.css'; ?>" />
    <script src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/js/swagger-ui-bundle.js'; ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const containerId = '<?php echo $container_id; ?>';
        const container = document.getElementById(containerId);
        
        if (container) {
            SwaggerUIBundle({
                url: '<?php echo esc_url($atts['url']); ?>',
                dom_id: '#' + containerId,
                layout: "<?php echo esc_js($atts['layout']); ?>",
                deepLinking: true,
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                docExpansion: "list",
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

function render_swagger_ui_api() {
    global $current_api_data;
    
    // URL del file YAML locale
    $plugin_url = plugin_dir_url(dirname(__FILE__));
    $swagger_url = $plugin_url . 'assets/yaml/openapi.yaml';
    
    // Genera un ID univoco una sola volta
    $container_id = 'swagger-ui-api-' . uniqid();
    
    ob_start();
    ?>
    <?php if ($current_api_data === null): ?>
        <div style="background: #ffebee; padding: 15px; margin: 15px 0; border: 1px solid #f44336; border-radius: 4px; color: #d32f2f;">
            <strong>Attenzione:</strong> Nessun dato API disponibile. Usa prima [resource-get-API-details].
        </div>
    <?php endif; ?>
    
    <div id="<?php echo $container_id; ?>" class="swagger-container" style="min-height: 400px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    </div>
    
    <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/css/swagger-ui.css'; ?>" />
    <script src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'assets/js/swagger-ui-bundle.js'; ?>"></script>
    
    <!-- CSS per rimuovere lo scroll interno di Swagger UI -->
    <style>
    .swagger-container .swagger-ui {
        height: auto !important;
        overflow-y: visible !important;
        overflow: visible !important;
        padding: 5px 0 !important;
    }
    
    .swagger-container .swagger-ui .wrapper {
        height: auto !important;
        overflow-y: visible !important;
        overflow: visible !important;
    }
    
    /* Nascondi l'URL del file YAML */
    .swagger-ui .info .link {
        display: none !important;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const containerId = '<?php echo $container_id; ?>';
        const container = document.getElementById(containerId);
        
        if (container) {
            SwaggerUIBundle({
                url: '<?php echo esc_url($swagger_url); ?>',
                dom_id: '#' + containerId,
                layout: "BaseLayout",
                deepLinking: true,
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                docExpansion: "list",
                defaultModelsExpandDepth: 1,
                defaultModelExpandDepth: 1,
                onComplete: function() {
                    // Rimuovi completamente lo scroll e le limitazioni di altezza
                    setTimeout(function() {
                        // Rimuovi limitazioni dal container principale
                        container.style.maxHeight = 'none';
                        container.style.overflowY = 'visible';
                        container.style.height = 'auto';
                        
                        // Rimuovi limitazioni dal contenuto Swagger
                        const swaggerContent = container.querySelector('.swagger-ui');
                        if (swaggerContent) {
                            swaggerContent.style.height = 'auto';
                            swaggerContent.style.overflowY = 'visible';
                            swaggerContent.style.overflow = 'visible';
                            swaggerContent.style.maxHeight = 'none';
                        }
                        
                        // Rimuovi limitazioni dal wrapper
                        const wrapper = container.querySelector('.swagger-ui .wrapper');
                        if (wrapper) {
                            wrapper.style.height = 'auto';
                            wrapper.style.overflowY = 'visible';
                            wrapper.style.overflow = 'visible';
                            wrapper.style.maxHeight = 'none';
                        }
                    }, 1000);
                }
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}