<?php

namespace ERROPiX\AdvancedScripts;

use ERROPiX\AdvancedScripts\Processor\CSS;
use ERROPiX\AdvancedScripts\Processor\HTML;
use ERROPiX\AdvancedScripts\Processor\JavaScript;
use ERROPiX\AdvancedScripts\Processor\LESS;
use ERROPiX\AdvancedScripts\Processor\PHP;
use ERROPiX\AdvancedScripts\Processor\SCSS;

/**
 * Class ScriptsManager
 * @package ERROPiX\AdvancedScripts
 */
class ScriptsManager
{
    use Utils;
    use HtmlUtils;
    use Migrations;

    private $fs;
    private $safe_mode;
    private $safe_mode_option = "advanced-scripts-safemode";
    private $safe_mode_enable_url = "";
    private $safe_mode_disable_url = "";
    private $admin_url;
    private $menu_hookname;
    private $menu_slug = "advanced-scripts";
    private $taxonomy = "erropix_scripts";
    private $error_code = "epxscrptmgr";
    private $capability = "manage_options";

    public $action_save = "advanced_scripts_save";
    public $action_order = "advanced_scripts_order";
    public $action_move = "advanced_scripts_move";
    public $action_delete = "advanced_scripts_delete";
    public $action_export = "advanced_scripts_export";
    public $action_import = "advanced_scripts_import";
    public $action_status = "advanced_scripts_status";

    /**
     * @var array[]
     */
    public $scripts;

    /**
     * @var array[]
     */
    public $scripts_tree;

    /**
     * @var array[]
     */
    public $types;

    /**
     * @var array
     */
    public $locations;

    /**
     * @var string[]
     */
    public $hooks;

    public function __construct()
    {
        $this->fs = erropix_advanced_scripts_fs();

        $this->admin_url = admin_url("tools.php?page={$this->menu_slug}");
        $this->safe_mode_disable_url = add_query_arg("safemode", 0, $this->admin_url);
        $this->safe_mode_enable_url = add_query_arg("safemode", 1, $this->admin_url);

        $this->types = [
            "CSS" => [
                "text/css" => "Custom Code",
                "url/css" => "Load from URL",
            ],
            "SCSS" => [
                "text/x-scss" => "Custom Code",
                "text/x-scss-partial" => "Partial",
            ],
            "JavaScript" => [
                "text/javascript" => "Custom Code",
                "url/javascript" => "Load from URL",
            ],
            "application/x-httpd-php" => "PHP",
            "text/html" => "HTML",
            "text/x-less" => "LESS",
        ];

        $this->locations = [
            "all" => "Everywhere",
            "front" => "Front-end",
            "admin" => "Administration area",
            "shortcode" => "Shortcode",
        ];

        $this->hooks = [
            "init",
            "plugins_loaded",
            "wp_head",
            "wp_body_open",
            "wp_footer",
            "admin_menu",
            "admin_init",
            "admin_head",
            "admin_footer",
            "login_head",
            "login_footer",
        ];

        add_action("admin_menu", [$this, "admin_menu"]);

        // Polyfill hook wp_body_open for Oxygen Builder
        add_action("ct_before_builder", [$this, "oxy_wp_body_open"], 0);

        // Handle fatal errors
        add_filter('wp_php_error_message', [$this, 'wp_php_error_message'], 10, 2);

        if ($this->fs->can_use_premium_code()) {
            add_filter("plugin_action_links", [$this, "plugin_action_links"], 10, 2);

            add_action("admin_init", [$this, "allow_scripts_upload"]);
            add_action("admin_init", [$this, "setup_freemius_tweaker"]);
            add_action("admin_head", [$this, "remove_notices"]);
            add_action("admin_enqueue_scripts", [$this, "admin_enqueue_scripts"]);

            add_action("wp_ajax_{$this->action_save}", [$this, "ajax_save_script"]);
            add_action("wp_ajax_{$this->action_order}", [$this, "ajax_order"]);
            add_action("wp_ajax_{$this->action_move}", [$this, "ajax_move"]);
            add_action("wp_ajax_{$this->action_status}", [$this, "ajax_switch_status"]);
            add_action("wp_ajax_{$this->action_delete}", [$this, "ajax_delete_script"]);

            add_action("wp_ajax_{$this->action_export}", [$this, "ajax_export"]);
            add_action("wp_ajax_{$this->action_import}", [$this, "ajax_import"]);
        }
    }

    public function init()
    {
        // Check safemode status
        $this->set_safe_mode();

        // Register scripts taxonomy
        $this->register_taxonomy();

        // Migrate scripts
        $this->do_migrations();

        // Load scripts
        $this->load_scripts();

        // Process scripts
        $this->process_scripts();

        // Add site health tests
        cpas_site_health();
    }

    /**
     * Register taxonomy
     */
    public function register_taxonomy()
    {
        $args = [
            "labels" => [
                "name" => "Advanced Scripts",
                "singular_name" => "Advanced Script",
            ],
            "hierarchical" => false,
            "public" => false,
            "show_ui" => false,
            "show_admin_column" => false,
            "show_in_nav_menus" => false,
            "show_tagcloud" => false,
            "rewrite" => false,
            "query_var" => false,
            "show_in_rest" => false,
        ];

        register_taxonomy($this->taxonomy, [], $args);
    }

    /**
     * Process single script
     * 
     * @param array $script 
     * @return void 
     */
    private function process_script(array $script)
    {
        $type = $script["type"] ?? "";

        switch ($type) {
            case "application/x-httpd-php":
                if ($this->can_execute_php()) {
                    new PHP($script);
                }
                break;

            case "url/javascript":
            case "text/javascript":
                new JavaScript($script);
                break;

            case "url/css":
            case "text/css":
                new CSS($script);
                break;

            case "text/x-scss":
                new SCSS($script);
                break;

            case "text/x-less":
                new LESS($script);
                break;

            case "text/html":
                new HTML($script);
                break;
        }
    }

    /**
     * Process script folders
     * 
     * @param array $branch 
     * @return void 
     */
    private function process_branch(array $branch)
    {
        foreach ($branch as $item) {
            if (empty($item["status"])) continue;

            if ($item["type"] == "folder") {
                $children = $item["children"] ?? null;

                if (is_array($children) && count($children)) {
                    $this->process_branch($children);
                }
            } else {
                $this->process_script($item);
            }
        }
    }

    public function process_scripts()
    {
        $this->process_branch($this->scripts_tree);
    }

    /**
     * Store a php file
     * 
     * @param int $term_id
     * @param string|null $title
     * @param string|null $code
     * 
     * @return string|false
     */
    public function save_php_file(int $term_id, string $title = null, string $code = null)
    {
        $title = $title ?: get_term_meta($term_id, "script_title", true);
        $code = $code ?: get_term_meta($term_id, "script_code", true);

        // generate a unique filename
        $title = preg_replace('/_/', '-', $title);
        $basename = sanitize_title($title);
        $filename = "php/{$term_id}-{$basename}.php";

        // analyze php code tokens
        $tokens = token_get_all($code);

        // check if code have namespace
        $has_namespace = false;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $has_namespace = true;
                break;
            }
        }

        // prepend direct access protection to code
        if (!$has_namespace) {
            $code = "<?php defined('WPINC') or die ?>" . $code;
        }

        // save code to file, return false on failure
        if (!cpas_storage()->write($filename, $code)) {
            return false;
        }

        // update filename meta if valid term_id is given
        if ($term_id > 0) {
            update_term_meta($term_id, "script_filename", $filename);
        }

        return $filename;
    }

    /**
     * Store scss partial file
     * 
     * @param int $term_id
     * @param string|null $title
     * @param string|null $code
     * 
     * @return string|false
     */
    public function save_scss_file(int $term_id, string $title = null, string $code = null)
    {
        $title = $title ?: get_term_meta($term_id, "script_title", true);
        $code = $code ?: get_term_meta($term_id, "script_code", true);

        // generate a unique filename
        $title = preg_replace('/_/', '-', $title);
        $basename = sanitize_title($title);
        $filename = "scss/_{$basename}.scss";

        // save code to file, return false on failure
        if (!cpas_storage()->write($filename, $code)) {
            return false;
        }

        // update filename meta if valid term_id is given
        if ($term_id > 0) {
            update_term_meta($term_id, "script_filename", $filename);
        }

        return $filename;
    }

    /**
     * Save script data
     */
    public function ajax_save_script()
    {
        // Security checks
        check_ajax_referer($this->action_save, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to save scripts");
        }

        // Prepare data
        $term_id = intval($_POST["id"] ?? 0);

        $script_parent = intval($_POST["parent"] ?? 0);
        $script_title = $_POST["title"] ?? null;
        $script_description = $_POST["description"] ?? null;
        $script_type = $_POST["type"] ?? null;
        $script_code = $_POST["content"] ?? null; // Used "content" instead of "code" in the form to bypass security plugins
        $script_url = $_POST["url"] ?? null;
        $script_location = $_POST["location"] ?? null;
        $script_priority = $_POST["priority"] ?? null;
        $script_hook = $_POST["hook"] ?? null;
        $script_shortcode = $_POST["shortcode"] ?? null;
        $script_conditions = $_POST["conditions"] ?? null;
        $script_status = boolval($_POST["status"] ?? false);

        // Create new term or load existing
        $is_new = false;
        if ($term_id > 0) {
            $result = term_exists($term_id, $this->taxonomy);
        } else {
            $script_slug = uniqid("script-");
            $result = wp_insert_term($script_slug, $this->taxonomy, [
                "parent" => $script_parent,
            ]);

            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            }
            $is_new = true;
        }

        $term_id = $result["term_id"] ?? 0;
        if ($term_id) {
            if ($script_type == "folder") {
                $metadata = compact(
                    "script_title",
                    "script_type",
                    "script_status"
                );

                foreach ($metadata as $meta_key => $meta_value) {
                    update_term_meta($term_id, $meta_key, $meta_value);
                }

                if ($is_new) {
                    do_action("advanced_scripts_folder_added", $term_id, $metadata);
                } else {
                    do_action("advanced_scripts_folder_updated", $term_id, $metadata);
                }

                do_action("advanced_scripts_folder_saved", $term_id, $metadata);

                wp_send_json_success([
                    "new" => $is_new,
                ]);
            } else {
                $metadata = compact(
                    "script_title",
                    "script_description",
                    "script_type",
                    "script_code",
                    "script_url",
                    "script_location",
                    "script_priority",
                    "script_hook",
                    "script_shortcode",
                    "script_conditions",
                    "script_status"
                );

                $changed = [];
                foreach ($metadata as $meta_key => $meta_value) {
                    if (update_term_meta($term_id, $meta_key, $meta_value)) {
                        $changed[] = $meta_key;
                    }
                }

                // Check if script data has been changed
                $changed_title = in_array("script_title", $changed);
                $changed_code = in_array("script_code", $changed);

                // If script data has been changed, save the code to file
                if ($changed_title || $changed_code) {
                    // Handle PHP scripts
                    if ($script_type === "application/x-httpd-php") {
                        $this->save_php_file($term_id, $script_title, wp_unslash($script_code));
                    }

                    // Handle SCSS partials
                    if ($script_type === "text/x-scss-partial") {
                        $this->save_scss_file($term_id, $script_title, wp_unslash($script_code));
                    }
                }

                if ($is_new) {
                    do_action("advanced_scripts_script_added", $term_id, $metadata);
                } else {
                    do_action("advanced_scripts_script_updated", $term_id, $metadata);
                }

                do_action("advanced_scripts_script_saved", $term_id, $metadata);

                $redirect = $is_new ? add_query_arg("edit", $term_id, $this->admin_url) : false;
                wp_send_json_success([
                    "new" => $is_new,
                    "term_id" => $term_id,
                    "redirect" => $redirect,
                ]);
            }
        } else {
            wp_send_json_error("An error occured while handling the request data");
        }
    }

    private function _delete_script_tree(array $script)
    {
        $children = $script["children"] ?? null;
        if (is_array($children)) {
            foreach ($children as $child) {
                $this->_delete_script_tree($child);
            }
        }

        wp_delete_term($script["term_id"], $this->taxonomy);
    }

    /**
     * Delete script
     */
    public function ajax_delete_script()
    {
        check_ajax_referer($this->action_delete, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to delete scripts");
        }

        $selected_ids = $_POST["selected_ids"] ?? [];

        if (is_array($selected_ids) && count($selected_ids)) {
            $term_ids = array_map("intval", $selected_ids);

            foreach ($term_ids as $term_id) {
                $script = $this->find_in_tree($this->scripts_tree, "term_id", $term_id);
                $this->_delete_script_tree($script);

                // if ($deleted !== true) {
                //     wp_send_json_error("An error occured while deleting the scripts");
                // }
            }

            wp_send_json_success("Script data deleted successfully");
        }

        wp_send_json_error("Invalid scripts IDs");
    }

    /**
     * Switch script status
     */
    public function ajax_switch_status()
    {
        check_ajax_referer($this->action_status, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to change scripts status");
        }

        $term_id = intval($_POST["id"] ?? 0);

        if ($term_id) {
            $script_status = !get_term_meta($term_id, "script_status", true);

            $result = update_term_meta($term_id, "script_status", $script_status);

            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            }

            if ($result !== false) {
                $action = $script_status ? "activated" : "deactivated";
                wp_send_json_success("Your script has been $action");
            }
        }

        wp_send_json_error("Your request couldn't be processed");
    }

    /**
     * Prepare scripts fields to be included in the exported data
     * 
     * @param array $scripts
     * 
     * @return void
     */
    private function _prepare_scripts_for_export(array &$scripts)
    {
        foreach ($scripts as &$script) {
            unset($script["slug"]);
            unset($script["term_id"]);

            $script = array_filter($script, function ($value) {
                return $value !== "";
            });

            if (isset($script["code"])) {
                $script["code"] = base64_encode($script["code"]);
            }

            if (!empty($script["children"]) && is_array($script["children"])) {
                $this->_prepare_scripts_for_export($script["children"]);
            }
        }
    }

    /**
     * Export scripts to JSON
     * 
     * @return void 
     */
    public function ajax_export()
    {
        check_ajax_referer($this->action_export, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to export scripts");
        }

        $selected_ids = $_POST["selected_ids"] ?? [];

        if (is_array($selected_ids) && count($selected_ids)) {
            $term_ids = array_map("intval", $selected_ids);

            $scripts = [];
            foreach ($term_ids as $term_id) {
                $script = $this->find_in_tree($this->scripts_tree, "term_id", $term_id);

                if ($script) {
                    $scripts[] = $script;
                }
            }

            $this->_prepare_scripts_for_export($scripts);

            $export = [
                "generator" => "Advanced Scripts",
                "version" => EPXADVSC_VER,
                "date" => date("Y-m-d H:i:s"),
                "scripts" => $scripts,
            ];

            wp_send_json_success($export);
        }
    }

    /**
     * Shared logic for script import
     * 
     * @param array $metadata 
     * @param int $parent
     * 
     * @return int 
     */
    private function _import_script(array $metadata, int $parent = 0)
    {
        // $term_id = rand(1000, 9999);
        // return $term_id;

        $term_id = 0;

        $metadata = array_filter($metadata, function ($value) {
            return $value !== "";
        });

        $script_slug = uniqid("script-");
        $result = wp_insert_term($script_slug, $this->taxonomy, [
            "parent" => $parent,
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        $term_id = $result["term_id"];
        foreach ($metadata as $meta_key => $meta_value) {
            update_term_meta($term_id, $meta_key, wp_slash($meta_value));
        }

        do_action("advanced_scripts_script_imported", $term_id, $metadata);

        return $term_id;
    }

    /**
     * Handle import of Code Snippets data
     * 
     * @param array $snippets   JSON data
     * @param int $parent       Parent folder ID
     * @param int $status_flag  Status override flag
     * 
     * @return array 
     */
    private function _import_from_code_snippets_json(array $snippets, int $parent = 0, int $status_flag = 0)
    {
        $imported = [];

        foreach ($snippets as $snippet) {
            $title = $snippet->name ?? "";

            $scope = $snippet->scope ?? "";
            if ($scope == "single-use") {
                continue;
            }

            $location = "all";

            if ($scope == "front-end") {
                $location = "front";
            } else if ($scope == "admin") {
                $location = "admin";
            }

            $description = $snippet->desc ?? "";
            if ($description) {
                $description = strip_tags($description);
                $description = str_replace("&nbsp;", " ", $description);
                $description = preg_replace("/ +/", " ", $description);
                $description = trim($description);
            }

            $code = "<?php\n\n" . trim($snippet->code ?? "");

            $metadata = [
                "script_title" => $title,
                "script_description" => $description,
                "script_type" => "application/x-httpd-php",
                "script_location" => $location,
                "script_hook" => "plugins_loaded",
                "script_priority" => "1",
                "script_code" => $code,
                "script_status" => $status_flag === 1,
            ];

            $term_id = $this->_import_script($metadata, $parent);

            $link = add_query_arg("edit", $term_id, $this->admin_url);

            $imported[] = [
                "id" => $term_id,
                "link" => $link,
                "title" => $title,
            ];
        }

        return $imported;
    }

    /**
     * Handle import of Advanced Scripts data
     * 
     * @param array $scripts    JSON data
     * @param int $parent       Parent folder ID
     * @param int $status_flag  Status override flag
     * 
     * @return array 
     */
    private function _import_from_advanced_scripts_json(array $scripts, int $parent = 0, int $status_flag = 0)
    {
        $imported = [];

        foreach ($scripts as $script) {
            $title = $script->title ?? "";

            $type = $script->type ?? "";
            if (!$type) continue;

            $code = isset($script->code) ? base64_decode($script->code) : "";

            $location = $script->location ?? "";
            if (isset($location) && $location == "manual") {
                $location = "shortcode";
            }

            $status = $script->status ?? false;

            if ($status_flag == 0) {
                $status = false;
            } else
            if ($status_flag == 1) {
                $status = true;
            }

            $metadata = [
                "script_title" => $title,
                "script_description" => $script->description ?? "",
                "script_type" => $type,
                "script_location" => $location,
                "script_hook" => $script->hook ?? "",
                "script_shortcode" => $script->shortcode ?? "",
                "script_priority" => $script->priority ?? "",
                "script_url" => $script->url ?? "",
                "script_code" => $code,
                "script_conditions" => $script->conditions ?? "",
                "script_status" => $status,
            ];

            $term_id = $this->_import_script($metadata, $parent);

            $arg_name = $type == "folder" ? "parent" : "edit";
            $link = add_query_arg($arg_name, $term_id, $this->admin_url);

            $imported[] = [
                "id" => $term_id,
                "link" => $link,
                "title" => $title,
            ];

            $children = $script->children ?? null;
            if (is_array($children) && count($children)) {
                $imported_children = $this->_import_from_advanced_scripts_json($children, $term_id, $status_flag);
                $imported = array_merge($imported, $imported_children);
            }
        }

        return $imported;
    }

    /**
     * Export scripts to JSON
     * 
     * @return void 
     */
    public function ajax_import()
    {
        check_ajax_referer($this->action_import, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to import scripts");
        }

        $scripts = [];

        $parent = intval($_POST["parent"] ?? 0);
        $status = intval($_POST["status"] ?? 0);

        foreach ($_FILES as $file) {
            if (!isset($file["error"]) || $file["error"]) continue;

            $path = $file["tmp_name"];
            $json = file_get_contents($path);
            $data = json_decode($json);

            if (json_last_error()) continue;

            $generator = $data->generator ?? null;

            if (!$generator) continue;

            if ($generator == "Advanced Scripts") {
                $file_scripts = $this->_import_from_advanced_scripts_json($data->scripts, $parent, $status);
                $scripts = array_merge($scripts, $file_scripts);
                continue;
            }

            if (strpos($generator, "Code Snippets") === 0) {
                $file_scripts = $this->_import_from_code_snippets_json($data->snippets, $parent, $status);
                $scripts = array_merge($scripts, $file_scripts);
                continue;
            }
        }

        wp_send_json_success([
            "scripts" => $scripts
        ]);
    }

    /**
     * Reorder scripts
     * @return void 
     */
    public function ajax_order()
    {
        check_ajax_referer($this->action_order, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to update scripts order");
        }

        $order_ids = $_POST["order"] ?? [];
        $order_ids = array_reverse($order_ids);

        if (is_array($order_ids) && count($order_ids)) {
            foreach ($order_ids as $order => $term_id) {
                update_term_meta($term_id, "script_order", $order + 1);
            }

            wp_send_json_success("Your scripts order has been updated");
        }

        wp_send_json_error("Your request couldn't be processed");
    }

    /**
     * Move scripts
     * @return void 
     */
    public function ajax_move()
    {
        check_ajax_referer($this->action_move, "token");

        if (!current_user_can($this->capability)) {
            wp_send_json_error("Your are not allowed to do this!");
        }

        $term_id = intval($_POST["id"] ?? 0);
        $parent = intval($_POST["parent"] ?? 0);

        if ($term_id) {
            $result = wp_update_term($term_id, $this->taxonomy, [
                "parent" => $parent,
            ]);

            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
            }

            wp_send_json_success("Item moved successfully");
        }

        wp_send_json_error("Your request couldn't be processed");
    }

    /**
     * Get scripts list
     *
     * @return array[]|null
     */
    private function load_scripts()
    {
        $this->scripts = [];

        $terms = get_terms([
            "taxonomy" => $this->taxonomy,
            "hide_empty" => false,
            "orderby" => "id",
        ]);

        $storage = cpas_storage();

        if (is_array($terms)) {
            foreach ($terms as $term) {
                $term_id = $term->term_id;

                $script_slug = $term->name;
                $script_parent = $term->parent;
                $script_title = get_term_meta($term_id, "script_title", true);
                $script_description = get_term_meta($term_id, "script_description", true);
                $script_type = get_term_meta($term_id, "script_type", true);
                $script_code = get_term_meta($term_id, "script_code", true);
                $script_url = get_term_meta($term_id, "script_url", true);
                $script_filename = get_term_meta($term_id, "script_filename", true);
                $script_location = get_term_meta($term_id, "script_location", true);
                $script_priority = get_term_meta($term_id, "script_priority", true);
                $script_hook = get_term_meta($term_id, "script_hook", true);
                $script_shortcode = get_term_meta($term_id, "script_shortcode", true);
                $script_conditions = get_term_meta($term_id, "script_conditions", true);
                $script_status = (bool) get_term_meta($term_id, "script_status", true);
                $script_order = (int) get_term_meta($term_id, "script_order", true);

                // Check if the script filename is not set or if the file does not exist in storage
                if (!$script_filename || !$storage->has($script_filename)) {
                    // Handle PHP scripts
                    if ($script_type === "application/x-httpd-php") {
                        $script_filename = $this->save_php_file($term_id, $script_title, $script_code);
                    }

                    // Handle SCSS partials
                    if ($script_type === "text/x-scss-partial") {
                        $script_filename = $this->save_scss_file($term_id, $script_title, $script_code);
                    }
                }

                $this->scripts[] = [
                    "slug" => $script_slug,
                    "parent" => $script_parent,
                    "term_id" => $term_id,
                    "title" => $script_title,
                    "description" => $script_description,
                    "type" => $script_type,
                    "code" => $script_code,
                    "url" => $script_url,
                    "filename" => $script_filename,
                    "location" => $script_location,
                    "priority" => $script_priority,
                    "hook" => $script_hook,
                    "shortcode" => $script_shortcode,
                    "conditions" => $script_conditions,
                    "status" => $script_status,
                    "order" => $script_order,
                ];
            }

            uasort($this->scripts, function (array $a, array $b) {
                $a_order = $a["order"] ?? 0;
                $b_order = $b["order"] ?? 0;

                $result = $b_order <=> $a_order;

                if ($result == 0) {
                    $result = $a["term_id"] <=> $b["term_id"];
                }

                return $result;
            });

            $this->scripts_tree = $this->build_scripts_tree($this->scripts);
        }
    }

    /**
     * Get scripts list
     * 
     * @param string $format
     *
     * @return array[]
     */
    public function get_colors($format = "sets")
    {
        global $oxygen_vsb_global_colors;
        $colors = [];

        if (!empty($oxygen_vsb_global_colors)) {
            if ($format == "sets") {
                foreach ($oxygen_vsb_global_colors["sets"] as $colors_set) {
                    list($colors_set_id, $colors_set_name) = array_values($colors_set);

                    $colors[$colors_set_name] = [];

                    foreach ($oxygen_vsb_global_colors["colors"] as $color) {
                        if (isset($color["set"]) && $color["set"] == $colors_set_id) {
                            $colors[$colors_set_name][] = $color;
                        }
                    }
                }
            }

            if ($format == "ids") {
                foreach ($oxygen_vsb_global_colors["colors"] as $color) {
                    $id = $color["id"] ?? null;
                    $value = $color["value"] ?? null;

                    if ($id && $value) {
                        $colors[$id] = $value;
                    }
                }
            }
        }

        return $colors;
    }

    /**
     * Get Current path
     * 
     * @param int $term_id 
     * @return array 
     */
    public function get_path_items($term_id)
    {
        $script = $this->find($this->scripts, "term_id", $term_id);

        $path = [];
        if ($script["parent"]) {
            $path = $this->get_path_items($script["parent"]);
        }

        if ($script) {
            $path[] = [
                "id" => $term_id,
                "title" => $script["title"],
                "link" => add_query_arg("parent", $term_id, $this->admin_url)
            ];
        }

        return $path;
    }

    /**
     * Render admin menu page
     */
    public function render_admin_page()
    {
        $colors = $this->get_colors();

        $edit_id = intval($_GET["edit"] ?? 0);
        $parent = intval($_GET["parent"] ?? 0);

        if ($edit_id) {
            $script = $this->find($this->scripts, "term_id", $edit_id);

            if ($script) {
                $parent = intval($script["parent"]);
            }
        }

        $base_url = $this->admin_url;
        if ($parent) {
            $base_url = add_query_arg("parent", $parent, $base_url);
            $path_items = $this->get_path_items($parent);
        }

        $scripts = array_filter($this->scripts, function (array $script) use ($parent) {
            return $script["parent"] == $parent;
        });

        // Prepare scripts colection for display
        $scripts = array_map(function ($script) {
            // Map script type to icon
            $icon = "";
            switch ($script["type"]) {
                case "folder":
                    $icon = "folder";
                    break;

                case "application/x-httpd-php":
                    $icon = "php";
                    break;

                case "url/javascript":
                case "text/javascript":
                    $icon = "javascript";
                    break;

                case "url/css":
                case "text/css":
                    $icon = "css";
                    break;

                case "text/x-scss":
                case "text/x-scss-partial":
                    $icon = "sass";
                    break;

                case "text/x-less":
                    $icon = "less";
                    break;

                case "text/html":
                    $icon = "html";
                    break;
            }
            $script["icon"] = $icon;

            // Initialize fallback shortcode
            if (empty($script["shortcode"])) {
                $script["shortcode"] = $script["slug"];
            }

            return $script;
        }, $scripts);

        $edit_script = $this->find($scripts, "term_id", $edit_id);

        if ($this->safe_mode_disable_url && $edit_id) {
            $this->safe_mode_disable_url = add_query_arg("edit", $edit_id, $this->safe_mode_disable_url);
        }

        if (is_array($edit_script)) {
            $edit_script["status"] = intval($edit_script["status"]);

            $edit_script["partial_import"] = "";
            if ($edit_script["type"] == "text/x-scss-partial") {
                $filename = basename($edit_script["filename"]);
                $partialname = str_replace(["_", ".scss"], "", $filename);
                $edit_script["partial_import"] = "@import '$partialname';";
            }
        } else {
            $edit_id = null;

            $edit_script = [
                "slug" => "",
                "parent" => $parent,
                "term_id" => 0,
                "title" => "",
                "description" => "",
                "type" => "application/x-httpd-php",
                "code" => "",
                "url" => "",
                "location" => "all",
                "hook" => "plugins_loaded",
                "partial_import" => "",
                "priority" => 10,
                "conditions" => "",
                "shortcode" => "",
                "status" => 0,
            ];
        }

        $json = json_encode([
            "base_url" => $base_url,
            "script" => [
                "id" => $edit_script["term_id"],
                "parent" => $parent,
                "is_new" => !$edit_id
            ]
        ]);

        wp_add_inline_script("cpas-script", "_.extend(AdvancedScripts, $json)", "before");

        include $this->path("templates/main.php");
    }

    /**
     * Set safe mode status
     */
    public function set_safe_mode()
    {
        if (defined("AS_SAFE_MODE")) {
            $this->safe_mode = AS_SAFE_MODE;
            return;
        }

        $this->safe_mode = get_option($this->safe_mode_option);

        if (isset($_GET["safemode"])) {
            $safe_mode = (bool) $_GET["safemode"];

            if ($this->safe_mode != $safe_mode) {
                $this->safe_mode = $safe_mode;
                update_option($this->safe_mode_option, $safe_mode);
            }
        }
    }

    /**
     * Check if we can execute PHP code in the current request
     * 
     * @return bool
     */
    public function can_execute_php()
    {
        // bypass safemode when checking errors
        if (!empty($_GET["advanced_scripts_check"])) {
            return true;
        }

        if (is_admin()) {
            // Protect the admin area
            if ($this->safe_mode) {
                return false;
            } else {
                $action = $_POST["action"] ?? null;

                $protected_actions = [
                    $this->action_save,
                    $this->action_status
                ];

                if (in_array($action, $protected_actions)) {
                    return false;
                }
            }
        } else {
            // Protect the login page
            global $pagenow;
            if ($this->safe_mode && $pagenow && $pagenow == "wp-login.php") {
                return false;
            }
        }

        return true;
    }

    /**
     * Allow CSS and JS files to be uploaded
     */
    public function allow_scripts_upload()
    {
        if (current_user_can($this->capability)) {
            add_filter("wp_check_filetype_and_ext", [$this, "wp_check_filetype_and_ext"], 10, 4);
        }
    }

    /**
     * Allow scripts upload to bypass false security checks due php fileinfo issues
     */
    public function wp_check_filetype_and_ext($result, $file, $filename, $mimes)
    {
        if (!$result["ext"] || !$result["type"]) {

            $filetype = wp_check_filetype($filename, $mimes);
            $ext = $filetype["ext"];
            $type = $filetype["type"];

            $allowed_types = [
                "css" => [
                    "text/css"
                ],
                "js" => [
                    "application/javascript"
                ],
            ];

            if (isset($allowed_types[$ext]) && in_array($type, $allowed_types[$ext])) {
                $result = [
                    "ext" => $ext,
                    "type" => $type,
                    "proper_filename" => $filename
                ];
            }
        }

        return $result;
    }

    /**
     * Register admin menu page
     */
    public function admin_menu()
    {
        $parent_slug = "tools.php";
        $page_title = "Advanced Scripts";
        $menu_title = "Advanced Scripts";
        $capability = $this->capability;
        $menu_slug = $this->menu_slug;
        $function = [$this, "render_admin_page"];

        $this->menu_hookname = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
    }

    /**
     * Enqueue CSS and JS files
     */
    public function admin_enqueue_scripts()
    {
        global $current_screen;

        if ($current_screen->id == $this->menu_hookname) {
            $version = EPXADVSC_VER;
            $condition_manager = cpas_condition_manager();

            if (!did_action("wp_enqueue_media")) {
                wp_enqueue_media();
            }
            wp_enqueue_style("cpas-font", "https://fonts.googleapis.com/css2?family=Fira+Code:wght@500;600;700&display=swap", [], $version);
            wp_enqueue_style("cpas-style", $this->url("assets/css/style.min.css"), [], $version);

            wp_enqueue_script("cpas-ace", $this->url("assets/js/ace/dist/ace.js"), [], $version, true);
            wp_enqueue_script("cpas-ace-emmet", $this->url("assets/js/ace/dist/emmet.js"), [], $version, true);
            wp_enqueue_script("cpas-ace-ext-emmet", $this->url("assets/js/ace/dist/ext-emmet.js"), [], $version, true);
            wp_enqueue_script("cpas-ace-ext-language_tools", $this->url("assets/js/ace/dist/ext-language_tools.js"), [], $version, true);
            wp_enqueue_script("cpas-ace-theme-dark", $this->url("assets/js/ace/dist/theme-dark.js"), [], $version, true);

            wp_enqueue_script("cpas-script", $this->url("assets/js/script.min.js"), ["wp-api", "wp-util", "jquery-ui-sortable"], $version, true);
            wp_localize_script("cpas-script", "AdvancedScripts", [
                "version" => EPXADVSC_VER,
                "check_url" => admin_url("?advanced_scripts_check=true"),
                "ajax" => [
                    "save" => [
                        "action" => $this->action_save,
                        "token" => wp_create_nonce($this->action_save),
                    ],
                    "move" => [
                        "action" => $this->action_move,
                        "token" => wp_create_nonce($this->action_move),
                    ],
                    "delete" => [
                        "action" => $this->action_delete,
                        "token" => wp_create_nonce($this->action_delete),
                    ],
                    "export" => [
                        "action" => $this->action_export,
                        "token" => wp_create_nonce($this->action_export),
                    ],
                    "import" => [
                        "action" => $this->action_import,
                        "token" => wp_create_nonce($this->action_import),
                    ],
                    "status" => [
                        "action" => $this->action_status,
                        "token" => wp_create_nonce($this->action_status),
                    ],
                    "order" => [
                        "action" => $this->action_order,
                        "token" => wp_create_nonce($this->action_order),
                    ],
                ],
                "hooks" => $this->hooks,
                "conditions" => [
                    "filters" => $condition_manager->get_builder_filters(),
                    "operators" => $condition_manager->get_builder_operators(),
                ]
            ]);

            // Get rid of the jQuery migrate warnings
            wp_add_inline_script("jquery-migrate", "jQuery.migrateMute = true;", "before");
        }
    }

    public function remove_notices()
    {
        global $current_screen;

        if ($current_screen->id == $this->menu_hookname) {
            remove_all_actions("admin_notices");
        }
    }

    /**
     * Append safemode notice to error message when it's related to Advanced Scripts
     * 
     * @param string $message HTML error message to display.
     * @param array  $error   Error information retrieved from `error_get_last()`.
     */
    public function wp_php_error_message($message, $error)
    {
        $script = cpas_current_script();

        if ($script && current_user_can('manage_options')) {
            $message .= '<style>.wp-die-message {background-color:#fff; color:#000;}</style>';

            $message .= '<p style="background:#fff9c4; padding:10px 15px">';
            $message .= sprintf('There has been a critical error on your %s script: <b>%s</b>.', $script['type'], $script['title']);
            $message .= "<br>";

            if ($this->safe_mode) {
                $edit_url = add_query_arg("edit", $script['id'], $this->admin_url);
                $message .= sprintf('The safe mode is already enabled, you can <a href="%s">click here</a> to fix the error.', $edit_url);
            } else if (defined('AS_SAFE_MODE')) {
                $message .= 'The safe mode was disabled in the <i>wp-config.php</i> file.';
            } else {
                $safemode_url = add_query_arg("edit", $script['id'], $this->safe_mode_enable_url);
                $message .= sprintf('You can <a href="%s">click here</a> to enable the safe mode and fix it.', $safemode_url);
            }

            $message .= "</p>";

            $message .= '<pre style="background:#f5f5f5; padding:10px 15px; overflow: auto">' . $error['message'] . '</pre>';
        }

        return $message;
    }

    /**
     * scripts getter
     * 
     * @return array
     */
    public function get_scripts()
    {
        return $this->scripts;
    }

    /**
     * Scripts tree getter
     * 
     * @return array
     */
    public function get_scripts_tree()
    {
        return $this->scripts_tree;
    }

    /**
     * Add tweaks to the Freemius account page
     */
    public function setup_freemius_tweaker()
    {
        new FreemiusTweaker($this->fs, $this->menu_hookname, "Advanced Scripts Account");
    }

    /**
     * @param string[] $actions
     * @param string   $file
     *
     * @return array
     */
    public function plugin_action_links($actions, $file)
    {
        if ($file == EPXADVSC_BASE) {
            $new_actions = [
                "manage" => sprintf("<a href=\"%s\">Manage</a>", $this->admin_url),
                "account" => sprintf("<a href=\"%s\">Account</a>", $this->fs->get_account_url()),
            ];

            $actions = array_merge($new_actions, $actions);
        }
        return $actions;
    }

    /**
     * wp_body_open polyfill for oxygen builder
     */
    public function oxy_wp_body_open()
    {
        if (!did_action("wp_body_open")) {
            do_action("wp_body_open");
        }
    }
}
