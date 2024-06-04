<?php

class HtmlParser
{
    var $html;

    public function __construct($html) {
        $this->html = $html;
    }

    public function parse() {
        $file = sprintf('%s.html', $this->html);
        $fh_skin = fopen($file, 'r');
        $skin = @fread($fh_skin, filesize($file));
        fclose($fh_skin);
        return $this->setHtml($skin);
    }

    private function setHtml($skin) {
        global $TMPL, $LNG;

        $skin = preg_replace_callback('/{\$lng->(.+?)}/i', create_function('$matches', 'global $LNG; return $LNG[$matches[1]];'), $skin);
        return preg_replace_callback('/{\$([a-zA-Z0-9_]+)}/', create_function('$matches', 'global $TMPL; return (isset($TMPL[$matches[1]])?$TMPL[$matches[1]]:"");'), $skin);
    }

}