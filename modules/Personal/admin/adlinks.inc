<?php
if (!defined('CPG_NUKE')) { exit; }
if (can_admin($module))
{
    $menuitems['_AMENU9']['Personal Pages']['URL'] = adminlink($module);
    $menuitems['_AMENU9']['Personal Pages']['IMG'] = $module;
    $menuitems['_AMENU9']['Personal Pages']['MOD'] = $module;
}
?>