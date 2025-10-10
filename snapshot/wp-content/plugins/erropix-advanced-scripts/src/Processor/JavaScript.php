<?php

namespace ERROPiX\AdvancedScripts\Processor;

class JavaScript extends Processor
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

    public function execute()
    {
        if ($this->url) {
            wp_enqueue_script($this->slug, $this->url, [], null);
        }

        if ($this->code) {
            if ($this->url) {
                wp_add_inline_script($this->slug, $this->code);
            } else {
                printf("<script id='%s-js' type='text/javascript'>\n%s\n</script>\n", $this->slug, $this->code);
            }
        }
    }
}
