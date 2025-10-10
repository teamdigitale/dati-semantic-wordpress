<?php

namespace ERROPiX\AdvancedScripts\Processor;

use Less_Parser;

class LESS extends Processor
{
    public function preprocess(array $colors)
    {
        $parser = new Less_Parser([
            'compress' => true,
        ]);
        $parser->parse($this->code);

        // Prepare variables
        $variables = [];

        // colors variables
        foreach ($colors as $id => $value) {
            $key = 'oxycolor' . $id;
            $variables[$key] = $value;
        }

        // Apply variables
        $parser->ModifyVars($variables);

        try {
            $this->code = $parser->getCss();
        } catch (\Exception $e) {
            $this->code = "/* Error: " . $e->getMessage() . " */";
        }
    }

    public function execute()
    {
        if ($this->code) {
            $colors = cpas_scripts_manager()->get_colors('ids');
            $this->preprocess($colors);

            printf("<style id='%s-less-css' type='text/css'>\n%s\n</style>\n", $this->slug, $this->code);
        }
    }
}
