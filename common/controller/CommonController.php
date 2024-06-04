<?php

require_once 'utils/trait/GetSetGenerator.php';
require_once 'utils/trait/RepositoryTrait.php';
require_once 'utils/view/HtmlParser.php';

class CommonController
{
    public function __construct()
    {
    }

    public function getCommonElement()
    {
        global $TMPL, $V, $my, $m, $bot;
        $viewHtml = 'view/common/commonElement';

        $TMPL['botType_value'] = $_SESSION['bottype'];
        $TMPL['vendor_value'] = $V['uid'];
        $TMPL['module_value'] = $m;
        $TMPL['myBot_value'] = $my['mybot'];
        $TMPL['botUid_value'] = $bot ? $bot : '';
        $TMPL['isSuper'] = $my['super'];
        $TMPL['isManager'] = $my['manager'];
        $TMPL['isCloud'] = $GLOBALS['_cloud_'] == '' ? 'false' : $GLOBALS['_cloud_'];

        $html = new HtmlParser($viewHtml);
        return $html->parse();
    }
}