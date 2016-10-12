<?php
/**
 * a simple preset file for the tt_content.php override config
 */

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

### CFSC TOKEN START ###
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Boxes/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Bulletpoints/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Button/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Example/TCA/Example.php"); # Example
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Header/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Slider/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Teaser/TCA/TCA.php"); # TCA
include_once("/app/web/typo3conf/ext/CustomFluidStyledContent/Resources/Private/ContentElements/Testimonial/TCA/TCA.php"); # TCA
### CFSC TOKEN END ###