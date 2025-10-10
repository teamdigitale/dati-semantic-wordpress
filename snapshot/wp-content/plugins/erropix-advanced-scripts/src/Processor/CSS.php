<?php

namespace ERROPiX\AdvancedScripts\Processor;

class CSS extends Processor
{
    public function init()
    {
        if ($this->url) {
            array_walk($this->hooks, function (&$hook) {
                switch ($hook) {
                    case 'wp_head':
                        $hook = 'wp_enqueue_scripts';
                        break;

                    case 'admin_head':
                        $hook = 'admin_enqueue_scripts';
                        break;

                    case 'login_head':
                        $hook = 'login_enqueue_scripts';
                        break;
                }
            });
        }
    }

    public function preprocess(array $colors)
    {
        $callback = function ($matches) use ($colors) {
            $id = $matches[1];
            $color = $colors[$id] ?? "";

            return $color;
        };

        $this->code = preg_replace_callback('/oxycolor\((\d+)\)/i', $callback, $this->code);
    }

    public function execute()
    {
        if ($this->url) {
            wp_enqueue_style($this->slug, $this->url, [], null);
        }

        if ($this->code) {
            $colors = cpas_scripts_manager()->get_colors('ids');
            if (count($colors)) {
                $this->preprocess($colors);
            }

            if ($this->url) {
                wp_add_inline_style($this->slug, $this->code);
            } else {
                printf("<style id='%s-css' type='text/css'>\n%s\n</style>\n", $this->slug, $this->code);
            }
        }
    }
}
