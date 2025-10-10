<?php

namespace ERROPiX\AdvancedScripts;

use FilesystemIterator;

class SiteHealth
{
    /**
     * The color of the test badge.
     * 
     * @var string
     */
    private $badge_color = "blue";

    /**
     * The label text displayed on the test badge.
     * 
     * @var string
     */
    private $badge_label = "Advanced Scripts";

    /**
     * Storage manager.
     * 
     * @var Storage
     */
    private $storage = null;

    /**
     * SiteHealth constructor.
     * 
     * Sets up hooks for site status tests and handling admin actions,
     * and initializes the storage property.
     */
    public function __construct()
    {
        // Add a filter to modify site status tests
        add_filter("site_status_tests", [$this, "site_status_tests"]);

        // Handle admin actions
        add_action("admin_action_advanced_scripts_add_directory_indexes", [$this, "do_add_directory_indexes"]);
        add_action("admin_action_advanced_scripts_delete_temporary_files", [$this, "do_delete_temporary_files"]);

        // Initialize the storage manager
        $this->storage = cpas_storage();
    }

    /**
     * Handle the addition of directory indexes.
     * 
     * This method is triggered via admin action and is responsible for
     * ensuring that directory indexes exist where necessary. It uses
     * the Storage manager to check and potentially add these indexes.
     * After the operation, it redirects back to the referring page.
     */
    public function do_add_directory_indexes()
    {
        // Verify the nonce for security
        check_admin_referer("advanced_scripts_add_directory_indexes");

        // Delegate the index checking to the storage manager
        $this->storage->check_indexes();

        // Redirect back to the referring admin page
        wp_redirect(wp_get_referer());
        exit;
    }

    /**
     * Delete temporary files created by Advanced Scripts.
     *
     * This method is triggered via admin action and is responsible for
     * deleting the old temporary files created by the plugin in the uploads
     * directory. It also attempts to remove the directory if it's empty after
     * deletion of the files.
     *
     * After the operation, it redirects back to the referring page.
     */
    public function do_delete_temporary_files()
    {
        // Verify the nonce for security
        check_admin_referer("advanced_scripts_delete_temporary_files");

        // Get the uploads directory information.
        $upload_dir = wp_get_upload_dir();

        // Define the path to the old temporary directory.
        $directory = $upload_dir["basedir"] . "/advanced-scripts";

        // Create a DirectoryIterator to traverse the temporary directory.
        $iterator = new FilesystemIterator($directory);

        // Initialize a counter for the number of failed file deletions.
        $failed = 0;

        // Iterate over each item in the directory.
        foreach ($iterator as $file) {
            // If the item is a file, attempt to delete it.
            if ($file->isFile() && !@unlink($file->getPathname())) {
                // Increment the failed counter if deletion was unsuccessful.
                $failed++;
            }
        }

        // If no files failed to delete, attempt to remove the directory.
        if (!$failed) {
            @rmdir($directory);
        }

        // Redirect back to the referring admin page.
        wp_redirect(wp_get_referer());
        exit;
    }

    /**
     * Adds Advanced Scripts specific tests to the site status tests array.
     * 
     * This method extends the array of site status tests by adding checks for:
     * - The correct setup of the storage directory.
     * - Server permissions for writing and including PHP files.
     * - Presence of old temporary files in the storage directory.
     * 
     * @param array $tests An associative array of direct and asynchronous tests.
     * 
     * @return array An updated associative array of tests including Advanced Scripts specific tests.
     */
    public function site_status_tests(array $tests)
    {
        // Check if the storage directory was setup correctly
        $tests["direct"]["cpas_test_storage_directory"] = [
            "label" => __("Advanced Scripts: Storage Directory"),
            "test"  => [$this, "test_storage_directory"],
        ];

        // Check if the server allow writing and including php files
        $tests["direct"]["cpas_test_php_scripts"] = [
            "label" => __("Advanced Scripts: PHP Scripts"),
            "test"  => [$this, "test_php_scripts"],
        ];

        // Check if the storage directory was setup correctly
        $tests["direct"]["cpas_test_old_temporary_files"] = [
            "label" => __("Advanced Scripts: Old Temporary Files"),
            "test"  => [$this, "test_old_temporary_files"],
        ];

        return $tests;
    }

    /**
     * Tests if the storage directory is correctly set up.
     * 
     * This method checks for the existence and writability of the storage directory, 
     * and verifies that index files are present to prevent directory listing.
     * 
     * @return array The test result.
     */
    public function test_storage_directory()
    {
        $path = $this->storage->path();

        $test = "cpas_" . __FUNCTION__;

        // Check if the storage directory exists
        if (!is_dir($path)) {
            return $this->result($test, [
                "status" => "critical",
                "label" => "Storage directory cannot be created",
                "description" => sprintf(
                    "<p>The <code>%s</code> directory used to store the snippets files does not exist.</p>",
                    $this->storage->relative_path()
                ),
            ]);
        }

        // Check if the storage directory is writable
        if (!is_writable($path)) {
            return $this->result($test, [
                "status" => "critical",
                "label" => "Storage directory is not writable",
                "description" => sprintf(
                    "<p>The <code>%s</code> directory exist but are not writable.</p>",
                    $this->storage->relative_path()
                ),
            ]);
        }

        // Check if the storage directory have an index file
        $missing_index = $this->get_missing_index_files();

        if ($missing_index !== false) {
            $action_url = $this->action_url("advanced_scripts_add_directory_indexes");

            return $this->result($test, [
                "status" => "critical",
                "label" => "Some storage directory index files are missing",
                "description" => sprintf(
                    "<p>The <code>index.php</code> file was not found in the <code>%s</code> directory.</p><p>This could lead to directory files listing.</p>",
                    $this->storage->relative_path($missing_index)
                ),
                "actions" => sprintf('<p><a href="%s" class="button button-primary">Protect the directory</a></p>', $action_url),
            ]);
        }

        // everything is ok
        return $this->result($test, [
            "status" => "good",
            "label" => "Storage directory was setup correctly",
            "description" => sprintf(
                "<p>The directory <code>%s</code> used to store the snippets files is writable and have all the required files.</p>",
                $this->storage->relative_path()
            ),
        ]);
    }

    /**
     * Tests if PHP scripts can be created, executed, and removed in the storage directory.
     * 
     * This method tests the whole lifecycle of a PHP script within the storage directory, including:
     * - Creation of a test PHP script file.
     * - Execution of the created test script.
     * - Deletion of the test script file.
     * If any step fails, a critical status is returned. If all steps pass, a good status is returned.
     * 
     * @return array The result array containing the status, label, and description.
     */
    public function test_php_scripts()
    {
        $test = "cpas_" . __FUNCTION__;

        // Attempt to save a simple test PHP file to the storage directory.
        $pathname = cpas_scripts_manager()->save_php_file(0, "Test Script", "<?php return 'OK'; ?>");

        // Check if the PHP file creation was successful.
        if ($pathname === false) {
            return $this->result($test, [
                "status" => "critical",
                "label" => "PHP script file creation failed",
                "description" => "<p>The test PHP file could not be created in the storage directory.</p>",
            ]);
        }

        // Execute the test PHP file and check the return value.
        $return = include $this->storage->path($pathname);
        if ($return !== 'OK') {
            return $this->result($test, [
                "status" => "critical",
                "label" => "PHP script execution failed",
                "description" => "<p>The test PHP file was created but failed to execute correctly.</p>",
            ]);
        }

        // Remove the test PHP file and check if the deletion was successful.
        if (!$this->storage->delete($pathname)) {
            return $this->result($test, [
                "status" => "critical",
                "label" => "PHP script file deletion failed",
                "description" => "<p>The test PHP file was created and executed but could not be removed from the storage directory.</p>",
            ]);
        }

        // All tests passed, PHP scripts are functioning as expected.
        return $this->result($test, [
            "status" => "good",
            "label" => "PHP scripts are functioning as expected",
            "description" => "<p>The test PHP file was successfully created, executed, and removed.</p>",
        ]);
    }

    /**
     * Test for old temporary files in the advanced-scripts directory.
     * 
     * This method checks for the existence of old temporary files in the
     * advanced-scripts directory within the WordPress uploads folder. It
     * returns a test result array with a status, label, and description
     * depending on whether old files or the directory itself exist, and
     * provides actions to clean them up if necessary.
     * 
     * @return array The result of the test with status, label, description, and actions.
     */
    public function test_old_temporary_files()
    {
        $test = "cpas_" . __FUNCTION__;

        // Get the uploads directory path.
        $upload_dir = wp_get_upload_dir();
        $directory = $upload_dir["basedir"] . "/advanced-scripts";

        // Check if the advanced-scripts directory exists.
        if (file_exists($directory) && is_dir($directory)) {
            // Get the number of files in the directory.
            $iterator = new FilesystemIterator($directory);
            $file_count = iterator_count($iterator);

            // Prepare the URL for the delete action.
            $action_url = $this->action_url("advanced_scripts_delete_temporary_files");

            // Calculate the relative path of the directory.
            $path = str_starts_with($directory, ABSPATH) ? substr($directory, strlen(ABSPATH)) : $directory;

            // If there are files in the directory, return a warning or critical status based on file count.
            if ($file_count) {
                return $this->result($test, [
                    "status" => $file_count > 1000 ? "critical" : "recommended",
                    "label" => "Old temporary files found",
                    "description" => sprintf("<p>There are <b>%d</b> files in the <code>%s</code> directory.</p><p>Deleting these files will free up storage space and enhance your website's efficiency.</p>", $file_count, $path),
                    "actions" => sprintf('<p><a href="%s" class="button button-primary">Delete All Files</a></p>', $action_url)
                ]);
            }

            // Return status for empty directory needing cleanup.
            return $this->result($test, [
                "status" => "recommended",
                "label" => "Old temporary directory found",
                "description" => sprintf("<p>The old temporary directory still exists in <code>%s</code>.</p>", $path),
                "actions" => sprintf('<p><a href="%s" class="button button-primary">Delete Directory</a></p>', $action_url)
            ]);
        }

        // Return status when the directory does not exist.
        return $this->result($test, [
            "status" => "good",
            "label" => "The old temporary directory does not exist",
            "description" => "<p>The old temporary directory does not exist in the uploads folder.</p>",
        ]);
    }

    /**
     * Generates a URL for performing a specific action in the WordPress admin area.
     *
     * This method constructs a URL with added query arguments that include an action identifier,
     * a referrer stripped of the '_wp_http_referer' query arg, and a nonce for security purposes.
     *
     * @param string $action The action to be performed, which is used to generate the nonce and as a query arg.
     * 
     * @return string The fully constructed action URL.
     */
    private function action_url(string $action)
    {
        return add_query_arg(
            [
                "action" => $action,
                "_wp_http_referer" => remove_query_arg('_wp_http_referer'),
                "_wpnonce" => wp_create_nonce($action),
            ],
            admin_url("admin.php")
        );
    }

    /**
     * Scaffolds a test result array with a default structure.
     * 
     * This method is used to construct a standardized result array for site health tests.
     * It ensures that all necessary keys are set and provides default values which can
     * be overridden by the passed $result array.
     * 
     * @param string $name   The unique identifier for the test.
     * @param array  $result The array of test result parameters to override defaults.
     * 
     * @return array The fully constructed test result array.
     */
    private function result($name, array $result)
    {
        // Define the base structure for a test result.
        $base = [
            "test" => $name,
            "label" => "",
            "status" => "good",
            "badge" => [
                "label" => $this->badge_label,
                "color" => $this->badge_color,
            ],
            "description" => "",
            "actions" => "",
        ];

        // Merge the default base structure with the provided result to override defaults.
        return array_replace_recursive($base, $result);
    }

    /**
     * Checks for missing index.php files in the storage directories.
     * 
     * This function iterates through the storage directory and its subdirectories
     * to ensure that each contains an index.php file. This file is typically used to
     * prevent directory listing on the server.
     * 
     * @return string|false Returns the relative path of the missing index.php file, otherwise it returns false.
     */
    public function get_missing_index_files()
    {
        // Check if index.php exists in the base storage directory
        if (!$this->storage->has("index.php")) {
            return "";
        }

        // Create an iterator to go through all files and directories within storage
        $iterator = $this->storage->iterator();

        // Loop over each item in the storage directory
        foreach ($iterator as $file) {
            // Check if the current item is a directory and if it lacks an index.php file
            if ($file->isDir() && !file_exists($file->getPathname() . "/index.php")) {
                // Return the relative path to the directory missing the index.php file
                return str_replace("\\", "/", $iterator->getSubPathname());
            }
        }

        // Return false if all directories contain an index.php file
        return false;
    }
}
