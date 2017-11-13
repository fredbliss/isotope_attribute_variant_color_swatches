<?php


/**
 * Register PSR-0 namespace
 *
 */


if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('IntelligentSpark', 'system/modules/isotope_attribute_variant_color_swatches/library');
}

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Src
	'IntelligentSpark\Module\VariantColorSwatches' => 'system/modules/isotope_attribute_variant_color_swatches/library/IntelligentSpark/Module/VariantColorSwatches.php',
));
