<?php

namespace ERROPiX\AdvancedScripts\Processor;

use Exception;
use ERROPiX\AdvancedScripts\ScssCompiler\Compiler;
use ERROPiX\AdvancedScripts\ScssCompiler\OutputStyle;
use ERROPiX\AdvancedScripts\ScssCompiler\ValueConverter;

class SCSS extends Processor
{
    public function preprocess(array $colors)
    {
        $compiler = new Compiler();
        $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        $compiler->addImportPath(cpas_storage()->path('scss'));

        // Prepare variables
        $variables = [];

        try {
            // colors variables
            foreach ($colors as $id => $value) {
                $key = 'oxycolor' . $id;
                $variables[$key] = ValueConverter::parseValue($value);
            }

            // Apply variables
            $compiler->addVariables($variables);

            $result = $compiler->compileString($this->code, __FILE__);
            $this->code = $result->getCss();
        } catch (Exception $e) {
            $this->code = "/* Error: " . $e->getMessage() . " */";
        }
    }

    public function execute()
    {
        if ($this->code) {
            $colors = cpas_scripts_manager()->get_colors('ids');
            $this->preprocess($colors);

            printf("<style id='%s-sass-css' type='text/css'>\n%s\n</style>\n", $this->slug, $this->code);
        }
    }
}
