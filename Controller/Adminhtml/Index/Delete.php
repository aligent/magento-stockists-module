<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */

namespace Aligent\Stockists\Controller\Adminhtml\Index;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

class Delete extends Action implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * @param Context $context
     * @param StockistRepositoryInterface $stockistRepository
     */
    public function __construct(
        Context $context,
        StockistRepositoryInterface $stockistRepository
    ) {
        parent::__construct($context);
        $this->stockistRepository = $stockistRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $stockistId = $this->getRequest()->getPost(StockistInterface::STOCKIST_ID);
        if ($stockistId === null) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $resultRedirect->setPath('*/*');
        }

        try {
            $stockistId = (int)$stockistId;
            $this->stockistRepository->deleteById($stockistId);
            $this->messageManager->addSuccessMessage(__('The Stockist has been deleted.'));
            $resultRedirect->setPath('*/*');
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath(
                '*/*/edit',
                [
                    StockistInterface::STOCKIST_ID => $stockistId,
                    '_current' => true
                ]
            );
        }

        return $resultRedirect;
    }
}
