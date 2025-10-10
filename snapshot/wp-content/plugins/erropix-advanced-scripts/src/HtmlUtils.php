<?php

namespace ERROPiX\AdvancedScripts;

trait HtmlUtils
{
    /**
     * @param array      $choices
     * @param mixed|null $value
     * @param bool       $echo
     *
     * @return string
     */
    public function html_options($choices, $value = null)
    {
        $html = '';
        if (is_array($choices)) {
            foreach ($choices as $key => $item) {
                if (is_array($item)) {
                    $html .= '<optgroup label="' . esc_attr($key) . '">';
                    $html .= $this->html_options($item, $value);
                    $html .= '</optgroup>';
                } else {
                    if (is_array($value)) {
                        $selected = in_array($key, $value) ? " selected" : "";
                    } else {
                        $selected = $key === $value ? " selected" : "";
                    }
                    $html .= '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($item) . '</option>';
                }
            }
        }

        return $html;
    }

    /**
     * @param $name
     * @param $value
     */
    public function html_checkbox($name, $value, $checked = false, $label = "")
    {
        $checked = $checked ? "checked" : "";

        $html = "";
        $html .= '<label class="checkbox">';
        $html .= '<input type="checkbox" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" ' . $checked . '>';
        $html .= $this->icon("checkbox", 18, 18);
        if ($label) {
            $html .= '<span>' . $label . '</span>';
        }
        $html .= '</label>';

        return $html;
    }

    /**
     * @param $name
     * @param $value
     */
    public function html_radiobox($name, $value, $checked = false, $label = "")
    {
        $checked = $checked ? "checked" : "";

        $html = "";
        $html .= '<label class="radiobox">';
        $html .= '<input type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" ' . $checked . '>';
        $html .= $this->icon("radiobox", 18, 18);
        if ($label) {
            $html .= '<span>' . $label . '</span>';
        }
        $html .= '</label>';

        return $html;
    }

    /**
     * @param $name
     * @param $value
     */
    public function html_switchbox($name, $value = "1", $checked = false, $label = "")
    {
        $checked = $checked ? "checked" : "";

        $html = "";
        $html .= '<label class="switchbox">';
        $html .= '<input type="checkbox" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" ' . $checked . '>';
        $html .= $this->icon("switch", 24, 14);
        if ($label) {
            $html .= '<span>' . $label . '</span>';
        }
        $html .= '</label>';

        return $html;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function icon(string $id, int $width = null, int $height = null, $class = null)
    {
        static $icons = [];

        if (empty($icons)) {
            $file = $this->path("assets/images/icons.svg");

            /** @var \SimpleXMLElement $xml */
            $xml = simplexml_load_file($file, "SimpleXMLElement", LIBXML_NOBLANKS);
            foreach ($xml->children() as $icon) {
                $icon_id = (string) $icon["id"];
                unset($icon["id"]);

                $icon["xmlns"] = "http://www.w3.org/2000/svg";
                $icon["fill"] = "none";

                $icons[$icon_id] = $icon;
            }
        }

        $svg = "";
        if (isset($icons[$id])) {
            $icon = $icons[$id];

            // Set icon class
            $icon["class"] = esc_attr($class ?? "as-icon-{$id}");

            // Set icon width
            if ($width) {
                $icon["width"] = $width;
            }

            // Set icon height
            if ($height) {
                $icon["height"] = $height;
            }

            // Render icon as XML text
            $svg = $icon->asXML();
        }

        return $svg;
    }
}
