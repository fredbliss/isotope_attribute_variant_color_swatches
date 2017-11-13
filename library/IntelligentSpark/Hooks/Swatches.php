<?php

namespace IntelligentSpark\Hooks;

class Swatches extends \Frontend {

    public function injectScripts($buffer)
    {
        return str_replace('</body>', '<script>
    IsotopeSwatches.attachSwatch('.json_encode($GLOBALS['AJAX_PRODUCTS']).');
</script></body>', $buffer);

    }
}