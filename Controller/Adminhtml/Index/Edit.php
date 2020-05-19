<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */

namespace Aligent\Stockists\Controller\Adminhtml\Index;

class Edit extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var  \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Aligent\Stockists\Api\StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->stockistRepository = $stockistRepository;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $stockistId = $this->getRequest()->getParam(\Aligent\Stockists\Model\Stockist::STOCKIST_ID);
        try {
            $stockist = $this->stockistRepository->getById($stockistId);
            /* @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $result = $this->resultPageFactory->create();
            $result->setActiveMenu("Aligent_Stockists::stockists_manage");
            $result->getConfig()->getTitle()->prepend(__("Edit Stockist %identifier", ['identifier' => $stockist->getIdentifier()]));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(\__('Stockist with id "%value" does not exist', ['value' => $stockistId]));
            $result->setPath('*/*');
        }
        return $result;
    }

}