<?php

namespace ERROPiX\AdvancedScripts\Processor;

abstract class Processor
{
    protected $slug;
    protected $term_id;
    protected $title;
    protected $description;
    protected $type;
    protected $code;
    protected $url;
    protected $filename;
    protected $location;
    protected $hooks;
    protected $shortcode;
    protected $shortcode_atts;
    protected $shortcode_content;
    protected $priority;
    protected $conditions;

    public function __construct(array $script)
    {
        $this->location = $script["location"] ?? null;

        if ($this->location == "front" && is_admin()) return;
        if ($this->location == "admin" && !is_admin()) return;

        $hooks = $script["hook"] ?? "";
        $this->hooks = explode(",", $hooks);

        $this->slug = $script["slug"] ?? null;
        $this->term_id = $script["term_id"] ?? null;
        $this->title = $script["title"] ?? null;
        $this->type = $script["type"] ?? null;
        $this->code = $script["code"] ?? null;
        $this->url = $script["url"] ?? null;
        $this->filename = $script["filename"] ?? null;
        $this->shortcode = $script["shortcode"] ?? null;
        $this->priority = $script["priority"] ?? 10;
        $this->conditions = $script["conditions"] ?? null;

        $this->init();

        if ($this->location == "shortcode") {
            if ($this->shortcode) {
                add_shortcode($this->shortcode, [$this, "do_shortcode"]);
            }
        } else {
            foreach ($this->hooks as $hook) {
                add_filter($hook, [$this, "hook_callback"], $this->priority, 5);
            }
        }
    }

    public function init()
    {
    }

    public function hook_callback()
    {
        if ($this->conditions) {
            cpas_condition_manager()->validate($this->conditions, [$this, "execute"]);
        } else {
            $this->execute();
        }
    }

    public function do_shortcode($atts, $content)
    {
        $this->shortcode_atts = is_array($atts) ? $atts : [];
        $this->shortcode_content = $content;

        ob_start();
        $this->execute();
        return ob_get_clean();
    }

    abstract public function execute();
}
