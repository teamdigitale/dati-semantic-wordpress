<?php

namespace ERROPiX\AdvancedScripts;

use DateTime;

trait Utils
{
    /**
     * @param array|object $data
     *
     * @return string
     */
    public function encode($data)
    {
        if (!empty($data) && (is_array($data) || is_object($data))) {
            $json = json_encode($data);
            return base64_encode($json);
        }
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function decode($string)
    {
        $json = base64_decode($string);
        return json_decode($json);
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public function url($path = null)
    {
        $url = EPXADVSC_URL . $path;
        // if ($path) {
        //     $file = $this->path($path);
        //     if (is_file($file)) {
        //         $mtime = filemtime($file);
        //         $url = add_query_arg("mt", $mtime, $url);
        //     }
        // }

        return $url;
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public function path($path = null)
    {
        $base = EPXADVSC_DIR;
        if (DIRECTORY_SEPARATOR !== "/") {
            $base = str_replace("\\", "/", $base);
        }
        return $base . trim($path);
    }

    /**
     * @param array          $array
     * @param string|integer $key
     * @param mixed|null     $alt
     *
     * @return mixed|null
     */
    public function array_get(array $array, $key, $alt = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        } else {
            if (strpos($key, ".")) {
                $keys = explode(".", $key);
                foreach ($keys as $k) {
                    if (is_array($array) && key_exists($k, $array)) {
                        $array = $array[$k];
                    } else {
                        return $alt;
                    }
                }
                return $array;
            }
        }

        return $alt;
    }

    /**
     * @param array $collection 
     * @param string $property 
     * @param mixed $value 
     * 
     * @return array|false 
     */
    public function find(array $collection, string $property, $value)
    {
        foreach ($collection as $item) {
            if (is_array($item) && isset($item[$property]) && $item[$property] == $value) {
                return $item;
            }
        }

        return false;
    }

    /**
     * 
     * @param array $tree 
     * @param string $key 
     * @param mixed $value
     * 
     * @return array|null 
     */
    public function find_in_tree(array $tree, string $key, $value)
    {
        $found = null;

        foreach ($tree as $item) {
            if (array_key_exists($key, $item) && $item[$key] === $value) {
                $found = $item;
            } else {
                $children = $item["children"] ?? null;
                if (is_array($children) && count($children)) {
                    $found = $this->find_in_tree($children, $key, $value);
                }
            }

            if ($found) break;
        }

        return $found;
    }

    /**
     * Build nest scripts array from flat collection
     * 
     * @param array $scripts 
     * @param int $branch_parent 
     * @return array 
     */
    public function build_scripts_tree(array $scripts, int $branch_parent = 0)
    {
        $branch = [];

        foreach ($scripts as $script) {
            if ($script["parent"] != $branch_parent) continue;

            if ($script["type"] == "folder") {
                $script["children"] = $this->build_scripts_tree($scripts, $script["term_id"]);
            }

            unset($script["parent"]);
            unset($script["order"]);

            $branch[] = $script;
        }

        return $branch;
    }

    /**
     * @param array  $posts
     * @param array  $values
     * @param int    $parent
     * @param string $prefix
     *
     * @return array
     */
    public function build_hierarchical_posts_options($posts, $values = [], $parent = 0, $prefix = "")
    {
        foreach ($posts as $post) {
            if ($post->post_parent == $parent) {
                $value = $post->ID;
                $label = $prefix . $post->post_title;
                $values[] = [
                    "value" => $value,
                    "text" => $label,
                ];

                $next_prefix = $label . " / ";
                $values = $this->build_hierarchical_posts_options($posts, $values, $post->ID, $next_prefix);
            }
        }

        return $values;
    }

    /**
     * Convert array to options collection
     * 
     * @param mixed $array 
     * @return array 
     */
    public function array_to_options(array $array)
    {
        $options = [];
        foreach ($array as $key => $value) {
            $options[] = [
                "value" => $key,
                "text" => $value,
            ];
        }
        return $options;
    }

    /**
     * Check if given time is with a time interval
     * 
     * @param string $time  Time string in format "HH:MM"
     * @param string $start Time string in format "HH:MM"
     * @param string $end   Time string in format "HH:MM"
     * @return bool 
     */
    public function time_in_interval(string $time, string $start, string $end)
    {
        $time = DateTime::createFromFormat('!H:i:s', $time);
        $from = DateTime::createFromFormat('!H:i:s', $start);
        $end = DateTime::createFromFormat('!H:i:s', $end);

        if ($from > $end) {
            $end->modify('+1 day');
        }

        if ($from <= $time && $time <= $end) {
            return true;
        }

        $time->modify('+1 day');

        if ($from <= $time && $time <= $end) {
            return true;
        }

        return false;
    }
}
