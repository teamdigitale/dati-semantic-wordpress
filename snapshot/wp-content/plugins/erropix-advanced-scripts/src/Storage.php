<?php

namespace ERROPiX\AdvancedScripts;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class Storage handles the storage of scripts and related data.
 * It provides methods for file operation, such as creating, reading,
 * and writing files within the plugin's designated storage directory.
 */
class Storage
{
    /**
     * The root directory path for storing advanced scripts files.
     *
     * @var string
     */
    protected $root;

    /**
     * Constructs the Storage object and initializes the root directory path.
     * It also performs a check to ensure the directory structure is correct and
     * the necessary indexes exist.
     */
    public function __construct()
    {
        // Convert the content directory path to a Unix-style path, and append the "advanced-scripts" subdirectory
        $this->root = str_replace("\\", "/", WP_CONTENT_DIR) . "/advanced-scripts";

        // Append the multisite blog-specific directory if running on a multisite installation
        if (is_multisite()) {
            $this->root .= "/sites/" . get_current_blog_id();
        }

        // Check if the directory structure is correct and perform check indexes if not done recently
        if ($this->check_directory() && !get_transient("advanced_scripts_check_indexes")) {
            // Ensure the required indexes are set up correctly
            $this->check_indexes();

            // Set a transient to avoid re-checking indexes for the next 24 hours
            set_transient("advanced_scripts_check_indexes", true, 24 * HOUR_IN_SECONDS);
        }
    }

    /**
     * Retrieves the absolute path to a given subpath within the storage root.
     * 
     * @param string $path The relative path to append to the root path.
     * 
     * @return string The full path to the specified subpath.
     */
    public function path(string $path = "")
    {
        // if the path is empty, return the root path
        if (!$path) {
            return $this->root;
        }

        // Normalize the provided path and remove leading and trailing slashes
        $path = str_replace("\\", "/", trim($path, "/\\ \n\r\t\v\0"));

        // If the normalized path is empty, return the root path
        if (!$path) {
            return $this->root;
        }

        // Prepend the root path
        $path = $this->root . "/" . $path;

        // Return the full path
        return $path;
    }

    /**
     * Get the relative path from the WordPress ABSPATH to a given subpath within the storage root.
     * 
     * @param string $path The relative path for which the relative path is desired.
     * 
     * @return string The relative path from the ABSPATH to the specified path.
     */
    public function relative_path(string $path = "")
    {
        // Get the full path
        $path = $this->path($path);

        // Return the relative path from the ABSPATH
        return substr($path, strlen(ABSPATH));
    }

    /**
     * Ensure a directory exists and is writable.
     * 
     * This method checks if the provided directory exists and is writable. If the directory
     * does not exist, it attempts to create it using the WordPress `wp_mkdir_p` function,
     * which creates the directory and any necessary parent directories. If the directory
     * creation is successful, the method returns true. It returns false if the path exists
     * but is not a directory or cannot be made writable.
     * 
     * @param string $directory The relative path to the directory to check or create.
     * 
     * @return bool True if the directory exists and is writable, false otherwise.
     */
    public function check_directory(string $directory = '')
    {
        // Get the full filesystem path to the directory
        $path = $this->path($directory);

        // Check if the directory exists and is writable
        if (is_dir($path)) {
            return is_writable($path);
        }

        // If the path exists but is not a directory, return false
        if (file_exists($path)) {
            return false;
        }

        // Attempt to create the directory and return the result
        return wp_mkdir_p($path);
    }

    /**
     * Check if an index.php file exists in the specified directory and create it if it does not exist.
     *
     * The index.php file is created with a PHP comment 'Silence is golden.' to prevent directory listing
     * while not showing any information to the user if accessed directly.
     * 
     * @param string $directory The relative path to the directory where the index.php file will be checked/created.
     */
    public function check_index(string $directory = '')
    {
        // Construct the path to the index.php file relative to the root directory
        $filename = $directory . "/index.php";

        // Check if the index.php file does not exists
        if (!$this->has($filename)) {
            // Create the index.php file with the standard 'Silence is golden.' comment
            $this->write($filename, "<?php\n// Silence is golden.\n");
        }
    }

    /**
     * Check and create index.php in all directories of the storage path.
     * 
     * This method will check the root directory and then iterate over all
     * subdirectories to ensure that an index.php file exists. If it doesn't,
     * the file is created to prevent directory listing.
     */
    public function check_indexes()
    {
        // Check index in the root directory
        $this->check_index();

        // Create an iterator for all files and directories within the storage
        $iterator = $this->iterator();

        // Iterate through each item in the storage directory
        foreach ($iterator as $file) {
            // If the current item is a directory, check/create index.php there
            if ($file->isDir()) {
                $this->check_index($iterator->getSubPathname());
            }
        }
    }

    /**
     * Create an iterator for the storage directory.
     * 
     * @param string $directory The directory to start the iterator from.
     * @param bool   $recursive Whether to include subdirectories.
     * 
     * @return FilesystemIterator|RecursiveIteratorIterator|false
     */
    public function iterator(string $directory = '', bool $recursive = true)
    {
        // Get the full path of the directory
        $path = $this->path($directory);

        // Check if the directory exists and is readable
        if (!is_dir($path) || !is_readable($path)) {
            return false;
        }

        // If recursive is true, create a recursive directory iterator
        if ($recursive) {
            // Create a directory iterator for the path, skipping dot files.
            $iterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);

            // Wrap the directory iterator in a RecursiveIteratorIterator to traverse the directory hierarchy.
            return new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        }

        // Otherwise, create a regular file iterator
        return new FilesystemIterator($path);
    }

    /**
     * Check if a file exists in the storage directory.
     *
     * This method checks for the existence of a file by constructing its full path
     * within the storage directory and then utilizing the file_exists() function
     * to determine if the file is present.
     *
     * @param string $filename The filename for which existence needs to be checked.
     *
     * @return bool True if the file exists, false otherwise.
     */
    public function has(string $filename)
    {
        // Construct the full path to the file within the storage directory
        $path = $this->path($filename);

        // Check and return the existence of the file
        return file_exists($path);
    }

    /**
     * Read data from the specified file.
     *
     * Opens the file for reading, acquires a shared lock to prevent writing during the read operation,
     * and reads the file content. It returns the file content on success or false on failure,
     * such as when the file is not a regular file, is not readable, cannot be opened, or cannot be locked.
     *
     * @param string $filename The name of the file to be read.
     *
     * @return string|false The file content or false on failure.
     */
    public function read(string $filename)
    {
        // Resolve the full path to the file
        $path = $this->path($filename);

        // Check if the file is a regular file and is readable
        if (!is_file($path) || !is_readable($path)) {
            return false;
        }

        // Attempt to open the file for reading
        $handle = fopen($path, 'r');
        if (!$handle) {
            return false;
        }

        // Attempt to acquire a shared lock on the file for reading
        if (flock($handle, LOCK_SH)) {
            return false;
        }

        // Read the file content if the lock is acquired
        $data = fread($handle, filesize($path));

        // Release the lock after reading
        flock($handle, LOCK_UN);

        // Close the file handle after reading
        fclose($handle);

        // Return the file content
        return $data;
    }

    /**
     * Writes data to the specified file.
     * 
     * Creates the file if it does not exist. If the file exists, the content will be overwritten.
     * Returns true on success, false on failure (e.g., if directory check fails, file cannot be opened for writing, or write operation fails).
     * 
     * @param string $filename Name of the file to write to.
     * @param string $data Data to write into the file.
     * 
     * @return bool True if the data was successfully written, false otherwise.
     */
    public function write(string $filename, string $data)
    {
        // Ensure the directory exists before attempting to write
        $directory = dirname($filename);
        if (!$this->check_directory($directory)) {
            return false;
        }

        // Get the full path of the file
        $path = $this->path($filename);

        // Check if the path is not a directory
        if (is_dir($path)) {
            return false;
        }

        // Attempt to open the file for writing
        $handle = fopen($path, 'w');
        if (!$handle) {
            return false;
        }

        // Attempt to write to the file if it is not empty and the file is locked for exclusive access
        $length = strlen($data);
        $bytes = 0;
        if ($length && flock($handle, LOCK_EX)) {
            $bytes = fwrite($handle, $data);
            flock($handle, LOCK_UN);
        }

        // Close the file handle and return the success status
        fclose($handle);

        // Check if the file was written successfully
        return $bytes === $length;
    }

    /**
     * Delete a file from the storage.
     * 
     * This method checks if the given filename exists and is writable, and then proceeds to delete it.
     * If the path is a directory or the path cannot be determined to be writable, the deletion will not occur.
     *
     * @param string $filename The name of the file to be deleted.
     * 
     * @return bool True if the file has been successfully deleted, false otherwise.
     */
    public function delete(string $filename)
    {
        // Resolve the full path of the file
        $path = $this->path($filename);

        // Check if the file exists and is writable before attempting deletion
        if (!$path || !is_writable($path)) {
            return false;
        }

        // If the path is a directory, do not proceed with deletion
        if (is_dir($path)) {
            return false;
        }

        // Use the unlink function to delete the file and suppress error messages with '@'
        return @unlink($path);
    }
}
