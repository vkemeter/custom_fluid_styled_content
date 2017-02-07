<?php
namespace VK\CustomFluidStyledContent\DataProcessing;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class ResponsiveProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        // The field name to process
        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration);
        if (empty($fieldName)) {
            return $processedData;
        }

        $originalValue = $cObj->data[$fieldName];

        // Set the target variable
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, $fieldName);
        $singleImage = false;

        foreach(json_decode($originalValue) as $key => $value) {
            if ($value->value != '') {
                $responsive[$processorConfiguration['breakpoints.'][substr($value->name, -2)]]['maxwidth'] = (int)$value->value;
            }
                else {
                    $responsive[$processorConfiguration['breakpoints.'][substr($value->name, -2)]]['maxwidth'] = (int)$processorConfiguration['breakpoints.'][substr($value->name, -2)];
                }
        }

        foreach($processedData['files'] as $file) {
            if ($file->getProperty('tx_cfsc_responsive') != '') {
                $singleImage = true;
                $responsive[$file->getProperty('tx_cfsc_responsive')]['file'] = $file;
            }
        }

        if ($singleImage === true) {
            foreach($responsive as $key => $tmp) {
                if (!isset($tmp['file']) || $tmp['file'] INSTANCEOF \TYPO3\CMS\Core\Resource\FileReference === false) {
                    unset($responsive[$key]);
                }
            }

            $processedData['singleImage'] = array_values($responsive)[0];
        }

        if (count($responsive) > 0) {
            uksort($responsive, function ($a, $b) {
                return ((int)$a < (int)$b) ? 1 : 0;
            });
        }

        $processedData[$targetVariableName] = $responsive;

        return $processedData;
    }
}
