<?php

namespace IntelligentSpark\Module;

use Haste\Image\Image as Image;
use Isotope\Model\Product as Product;
use Isotope\Frontend as Frontend;

class VariantColorSwatches extends Frontend {

    protected $arrImages = array();

    public function generateProduct($objTemplate,$objProduct) {

        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/isotope_attribute_variant_color_swatches/assets/isotope-swatches.js';

        if($objProduct->getRelated('type')->name=='Variants') {
            //get all variants
            $objProducts = Product::findAvailableByIds($objProduct->getVariantIds());

            foreach($objProducts as $product) {
               $arrSwatches[$product->id] = array(
                  #'image'   => $this->getImageForType('default', current(deserialize($product->variant_image,true)),false),
                  'color_hex' => urldecode($product->color_hex_value),
                  'variant_id'  => $product->color
               );
            }

            $objTemplate->swatches = (count($arrSwatches) ? $arrSwatches : array());
        }
    }

    /**
     * Gets the image for a given file and given type and optionally adds a watermark to it
     *
     * @param   string $strType
     * @param   array $arrFile
     * @param   bool  $blnWatermark
     *
     * @return  array
     * @throws  \InvalidArgumentException
     */
    protected function getImageForType($strType, array $arrFile, $blnWatermark = true)
    {
        // Check cache
        $strCacheKey = md5($strType . '-' . json_encode($arrFile) . '-' . (int) $blnWatermark);

        if (isset($this->arrImages[$strCacheKey])) {
            return $this->arrImages[$strCacheKey];
        }

        $strFile = $arrFile['src'];

        // File without path must be located in the isotope root folder
        if (strpos($strFile, '/') === false) {
            $strFile = 'isotope/' . strtolower(substr($strFile, 0, 1)) . '/' . $strFile;
        }

        $objFile = new \File($strFile);

        if (!$objFile->exists()) {
            throw new \InvalidArgumentException('The file "' . $strFile . '" does not exist!');
        }

        $size = deserialize($this->{$strType . '_size'}, true);

        try {
            $strImage = \Image::create($strFile, $size)->executeResize()->getResizedPath();
            $picture = \Picture::create($strFile, $size)->getTemplateData();
        } catch (\Exception $e) {
            \System::log('Image "' . $strFile . '" could not be processed: ' . $e->getMessage(), __METHOD__, TL_ERROR);

            $strImage = '';
            $picture = array('img'=>array('src'=>'', 'srcset'=>''), 'sources'=>array());
        }

        // Watermark
        if ($blnWatermark
            && $this->{$strType . '_watermark_image'} != ''
            && ($objWatermark = \FilesModel::findByUuid($this->{$strType . '_watermark_image'})) !== null
        ) {
            $strImage = Image::addWatermark($strImage, $objWatermark->path, $this->{$strType . '_watermark_position'});

            // Apply watermark to the picture image source
            if ($picture['img']['src']) {
                $picture['img']['src'] = Image::addWatermark($picture['img']['src'], $objWatermark->path, $this->{$strType . '_watermark_position'});
            }

            // Apply watermark to the picture sources
            foreach ($picture['sources'] as $k => $v) {
                $picture['sources'][$k]['src'] = Image::addWatermark($v['src'], $objWatermark->path, $this->{$strType . '_watermark_position'});
            }
        }

        $arrSize = getimagesize(TL_ROOT . '/' . rawurldecode($strImage));

        if (is_array($arrSize) && $arrSize[3] !== '') {
            $arrFile[$strType . '_size']      = $arrSize[3];
            $arrFile[$strType . '_imageSize'] = $arrSize;
        }

        $arrFile['alt']     = specialchars($arrFile['alt'], true);
        $arrFile['desc']    = specialchars($arrFile['desc'], true);
        $arrFile['picture'] = $picture;

        $arrFile[$strType] = TL_ASSETS_URL . $strImage;

        $this->arrImages[$strCacheKey] = $arrFile;

        return $arrFile;
    }

}