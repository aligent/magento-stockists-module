<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */

namespace Aligent\Stockists\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
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
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $stockistId = $this->getRequest()->getPost(\Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID);
        if ($stockistId === null) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $stockistId = (int)$stockistId;
            $this->stockistRepository->deleteById($stockistId);
            $this->messageManager->addSuccessMessage(__('The Stockist has been deleted.'));
            $resultRedirect->setPath('*/*');
        } catch (\Magento\Framework\Exception\CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*/edit', [
                \Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID => $stockistId,
                '_current' => true,
            ]);
        }

        return $resultRedirect;
    }

}



