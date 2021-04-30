<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */

namespace Aligent\Stockists\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
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
     * @var \Aligent\Stockists\Ui\Component\MassAction\Filter
     */
    private $massActionFilter;

    /**
     * MassDelete constructor.
     * @param \Aligent\Stockists\Ui\Component\MassAction\Filter $massActionFilter
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Aligent\Stockists\Ui\Component\MassAction\Filter $massActionFilter,
        \Magento\Backend\App\Action\Context $context,
        \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->stockistRepository = $stockistRepository;
        $this->massActionFilter = $massActionFilter;
    }

    /**
     * @inheritdoc
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
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
