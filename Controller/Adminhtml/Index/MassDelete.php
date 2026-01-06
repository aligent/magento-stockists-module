<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;

use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends Action implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * @var Filter
     */
    private $massActionFilter;

    /**
     * @param Filter $massActionFilter
     * @param Context $context
     * @param StockistRepositoryInterface $stockistRepository
     */
    public function __construct(
        Filter $massActionFilter,
        Context $context,
        StockistRepositoryInterface $stockistRepository
    ) {
        parent::__construct($context);
        $this->stockistRepository = $stockistRepository;
        $this->massActionFilter = $massActionFilter;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        if ($this->getRequest()->isPost() !== true) {
            $this->messageManager->addErrorMessage(__('Wrong request.'));
            return $this->resultRedirectFactory->create()->setPath('*/*');
        }

        $deletedItemsCount = 0;
        foreach ($this->massActionFilter->getIds() as $id) {
            try {
                $id = (int)$id;
                $this->stockistRepository->deleteById($id);
                $deletedItemsCount++;
            } catch (CouldNotDeleteException $e) {
                $errorMessage = __('[ID: %1] ', $id) . $e->getMessage();
                $this->messageManager->addErrorMessage($errorMessage);
            }
        }
        $this->messageManager->addSuccessMessage(__('You deleted %1 Stockist(s).', $deletedItemsCount));

        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
