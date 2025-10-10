<?php

namespace ERROPiX\AdvancedScripts;

/**
 * Scripts Migrations
 * @package ERROPiX\AdvancedScripts
 */
trait Migrations
{
    private function do_migrations()
    {
        // Migrate from v1 to v2
        $this->migrate_v1_to_v2();

        // Migrate to v2.2.0
        $this->migrate_to_v2_2_0();

        // Migrate to v2.5.1
        $this->migrate_to_v2_5_1();
    }

    private function migrate_to_v2_5_1()
    {
        if (!get_option("advanced_scripts_migrated_to_v2_5_1")) {
            $terms = get_terms([
                "taxonomy" => $this->taxonomy,
                "hide_empty" => false,
                "fields" => "ids",
                "meta_key" => "script_type",
                "meta_value" => "application/x-httpd-php"
            ]);

            foreach ($terms as $term_id) {
                cpas_scripts_manager()->save_php_file($term_id);
            }

            update_option("advanced_scripts_migrated_to_v2_5_1", true);
        }
    }

    private function migrate_to_v2_2_0()
    {
        if (!get_option("advanced_scripts_migrated_to_v2_2_0")) {
            $terms = get_terms([
                "taxonomy" => $this->taxonomy,
                "hide_empty" => false,
                "fields" => "id=>name",
                "meta_key" => "script_location",
                "meta_value" => "manual"
            ]);

            foreach ($terms as $term_id => $script_id) {
                $update_meta = [
                    "script_location" => "shortcode",
                    "script_shortcode" => $script_id,
                ];

                if (!empty($update_meta)) {
                    $update_meta = wp_slash($update_meta);

                    foreach ($update_meta as $meta_key => $meta_value) {
                        update_term_meta($term_id, $meta_key, $meta_value);
                    }
                }
            }

            update_option("advanced_scripts_migrated_to_v2_2_0", true);
        }
    }

    private function migrate_v1_to_v2()
    {
        if (!get_option("advanced_scripts_migrated_v1_to_v2")) {
            $terms = get_terms([
                "taxonomy" => $this->taxonomy,
                "hide_empty" => false,
                "fields" => "ids"
            ]);

            foreach ($terms as $term_id) {
                $update_meta = [];

                $script_type = get_term_meta($term_id, "script_type", true);
                $script_location = get_term_meta($term_id, "script_location", true);
                $script_hook = get_term_meta($term_id, "script_hook", true);
                $script_priority = get_term_meta($term_id, "script_priority", true);

                // Migrate location and hooks
                if (in_array($script_location, $this->hooks)) {
                    $script_hook = $script_location;

                    switch ($script_location) {
                        case "wp_head":
                        case "wp_footer":
                            $script_location = "front";
                            break;

                        case "admin_head":
                        case "admin_footer":
                            $script_location = "admin";
                            break;

                        default:
                            $script_location = "all";
                            break;
                    }

                    $update_meta["script_location"] = $script_location;
                    $update_meta["script_hook"] = $script_hook;
                } else 
                if ($script_location == "custom") {
                    $script_location = "all";
                    $update_meta["script_location"] = $script_location;
                } else 
                if ($script_location == "manual" && $script_hook) {
                    $script_hook = "";
                    $update_meta["script_hook"] = $script_hook;
                }

                // Migrate "custom code" to standard PHP code
                if ($script_type == "application/x-httpd-php-open") {
                    $script_code = get_term_meta($term_id, "script_code", true);

                    $script_code = "<?php\n\n" . $script_code;
                    $script_type = "application/x-httpd-php";

                    $update_meta["script_type"] = $script_type;
                    $update_meta["script_code"] = $script_code;
                }

                // Migrate "filter callback" to standard PHP code
                if ($script_type == "application/x-httpd-php-function") {
                    $func_name = get_term_meta($term_id, "script_func_name", true);
                    $func_args = get_term_meta($term_id, "script_func_args", true);
                    $func_code = get_term_meta($term_id, "script_code", true);

                    $script_type = "application/x-httpd-php";

                    $func_args = trim($func_args);
                    $func_args_count = 0;
                    if ($func_args) {
                        $func_args = explode(",", $func_args);
                        $func_args_count = count($func_args);
                        if ($func_args_count) {
                            $func_args = array_map(function ($arg) {
                                if ($arg) {
                                    return '$' . $arg;
                                }
                            }, $func_args);
                        }
                        $func_args = implode(", ", $func_args);
                    }

                    $script_code = "<?php\n\nfunction $func_name ($func_args)\n{\n" . $func_code . "\n}\n";

                    $filter_tags = explode(",", $script_hook);
                    foreach ($filter_tags as $tag) {
                        $script_code .= "\nadd_filter('$tag', '$func_name', $script_priority, $func_args_count);";
                    }

                    $script_hook = "plugins_loaded";
                    $script_priority = 10;

                    $update_meta["script_type"] = $script_type;
                    $update_meta["script_code"] = $script_code;
                    $update_meta["script_hook"] = $script_hook;
                    $update_meta["script_priority"] = $script_priority;
                }

                if (!empty($update_meta)) {
                    $update_meta = wp_slash($update_meta);

                    foreach ($update_meta as $meta_key => $meta_value) {
                        update_term_meta($term_id, $meta_key, $meta_value);
                    }
                }
            }

            update_option("advanced_scripts_migrated_v1_to_v2", true);
        }
    }
}
