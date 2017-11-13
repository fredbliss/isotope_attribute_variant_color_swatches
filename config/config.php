<?php

$GLOBALS['ISO_HOOKS']['generateProduct'][]          = array('IntelligentSpark\Module\VariantColorSwatches','generateProduct');
$GLOBALS['TL_HOOKS']['modifyFrontendPage'][]            = array('IntelligentSpark\Hooks\Swatches', 'injectScripts');
