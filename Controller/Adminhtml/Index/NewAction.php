<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */

namespace Aligent\Stockists\Controller\Adminhtml\Index;

class NewAction extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        \Magento\Backend\App\Action::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /* @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Aligent_Stockists::stockists_add");
        $resultPage->getConfig()->getTitle()->prepend("New Stockist");
        return $resultPage;
    }

}