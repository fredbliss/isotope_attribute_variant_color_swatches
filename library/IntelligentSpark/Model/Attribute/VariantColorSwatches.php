<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2016 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @link       https://isotopeecommerce.org
 * @license    https://opensource.org/licenses/lgpl-3.0.html
 */

namespace IntelligentSpark\Model\Attribute;

use Isotope\Interfaces\IsotopeAttributeForVariants as IsotopeAttributeForVariants;
use Isotope\Model\Attribute\AbstractAttributeWithOptions as AbstractAttributeWithOptions;
use Isotope\Interfaces\IsotopeProduct as IsotopeProduct;
/**
 * Attribute to impelement SelectMenu widget
 *
 * @copyright  Isotope eCommerce Workgroup 2009-2012
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 */
class VariantColorSwatches extends AbstractAttributeWithOptions implements IsotopeAttributeForVariants
{
    /**
     * @inheritdoc
     */
    public function prepareOptionsWizard($objWidget, $arrColumns)
    {
        if ($this->isVariantOption()) {
            unset($arrColumns['default'], $arrColumns['group']);
        }

        return $arrColumns;
    }

    /**
     * @inheritdoc
     */
    public function saveToDCA(array &$arrData)
    {
        // Varian select menu cannot have multiple option
        if ($this->isVariantOption()) {
            $this->multiple           = false;
            $this->size               = 1;
        }

        parent::saveToDCA($arrData);

        if ($this->isVariantOption()) {
            $arrData['fields'][$this->field_name]['eval']['includeBlankOption'] = true;
        }

        if ('attribute' === $this->optionsSource) {
            $arrData['fields'][$this->field_name]['sql'] = "varchar(255) NOT NULL default ''";
        } else {
            $arrData['fields'][$this->field_name]['sql'] = "int(10) NOT NULL default '0'";
        }

        if ($this->fe_filter) {
            $arrData['config']['sql']['keys'][$this->field_name] = 'index';
        }
    }

    public function generate(IsotopeProduct $objProduct, array $arrOptions = array())
    {
        $varValue = parent::getValue($objProduct);

        var_dump($varValue);

        if (empty($varValue)) {

            return '';

        } else {

            return $this->generateSwatches($varValue);

        }

    }

    public function generateSwatches(array $arrValues)
    {
        $arrBuffer = array();

        foreach ($arrValues as $value) {

            $arrBuffer[] = "\n<div class=\"swatch\" styles=\"background-color:" .$value . "\"</div>";

        }

        $strBuffer = "<div class=\"swatches\">".implode(" ",$arrBuffer)."</div>";

        return $strBuffer;
    }
}