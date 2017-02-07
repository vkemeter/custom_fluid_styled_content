<?php
namespace VK\CustomFluidStyledContent\Domain\Model;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {

    /**
     * responsive informations
     *
     * @var string
     */
    protected $tx_cfsc_responsive;

    /**
     * Returns the responsive informations
     *
     * @return string $tx_theme_responsive
     */
    public function getTxCfscResponsive() {
        return $this->tx_cfsc_responsive;
    }

    /**
     * Sets the responsive informations
     *
     * @param string $tx_cfsc_responsive
     * @return void
     */
    public function setTxCfscResponsive($tx_cfsc_responsive) {
        $this->tx_cfsc_responsive = $tx_cfsc_responsive;
    }
}