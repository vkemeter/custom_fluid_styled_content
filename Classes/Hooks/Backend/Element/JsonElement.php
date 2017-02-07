<?php
namespace VK\CustomFluidStyledContent\Hooks\Backend\Element;


use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\NodeInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class JsonElement extends AbstractNode implements NodeInterface
{

    protected $data;

    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        $this->data = $data;
        parent::__construct($nodeFactory, $data);
    }

    public function render()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/CustomFluidStyledContent/ResponsiveImages');
        $pageRenderer->addCssFile(ExtensionManagementUtility::extRelPath('custom_fluid_styled_content') . 'Resources/Public/Css/ResponsiveImages.css');

        $template = 'ResponsiveImages.html';
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:custom_fluid_styled_content/Resources/Private/ContentElements/TextMedia/Templates/Backend/'. $template));

        $resultArray = $this->initializeResultArray();

        // sollten aus der typoscript config des content
        // elements kommen, oder zumindest so, das es global
        // konfigurierbar ist.
        // derzeit wird es hier und in der typoscript datei des
        // CE konfiguriert. = nicht gut.
        $breakpoints = [
            'xs' => '320px',
            'sm' => '544px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1260px',
        ];

        $view->assignMultiple([
            'breakpoints' => $breakpoints,
            'breakpointKeys' => array_flip($breakpoints),
            'params' => $this->data['parameterArray'],
        ]);

        $resultArray['html'] = $view->render();
        return $resultArray;
    }
}