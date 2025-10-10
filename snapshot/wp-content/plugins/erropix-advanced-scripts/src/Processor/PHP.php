<?php

namespace ERROPiX\AdvancedScripts\Processor;

class PHP extends Processor
{
    protected $file = null;

    public function init()
    {
        if ($this->filename) {
            $this->file = cpas_storage()->path($this->filename);
        }
    }

    public function execute()
    {
        if ($this->file) {
            // Open the file for reading
            $handle = fopen($this->file, 'r');

            if (!$handle) {
                return;
            }

            cpas_current_script([
                'id' => $this->term_id,
                'title' => $this->title,
                'type' => 'PHP',
            ]);

            if ($this->location == "shortcode") {
                $atts = $this->shortcode_atts;
                $content = $this->shortcode_content;
                $tag = $this->shortcode;
            }

            // Lock the file in shared mode
            if (flock($handle, LOCK_SH)) {
                include $this->file;

                // Release the lock and close the file
                flock($handle, LOCK_UN);
            }

            fclose($handle);

            cpas_current_script(false);
        }
    }
}
