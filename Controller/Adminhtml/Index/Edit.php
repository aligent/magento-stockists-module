<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var  PageFactory
     */
    private $resultPageFactory;

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * @param Context $context
     * @param StockistRepositoryInterface $stockistRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        StockistRepositoryInterface $stockistRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->stockistRepository = $stockistRepository;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $stockistId = $this->getRequest()->getParam(StockistInterface::STOCKIST_ID);
        try {
            $stockist = $this->stockistRepository->getById((int)$stockistId);
            /* @var Page $result */
            $result = $this->resultPageFactory->create();
            $result->setActiveMenu("Aligent_Stockists::stockists_manage");
            $result->getConfig()->getTitle()->prepend(
                __("Edit Stockist %identifier", ['identifier' => $stockist->getIdentifier()])
            );
        } catch (NoSuchEntityException $e) {
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                __('Stockist with id "%value" does not exist', ['value' => $stockistId])
            );
            $result->setPath('*/*');
        }
        return $result;
    }
}
