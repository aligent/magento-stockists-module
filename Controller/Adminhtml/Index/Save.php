<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;


/**
 * Save Controller
 */
class Save extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    private $stockistHydrator;

    private $stockistRepository;

    private $stockistFactory;

    public function __construct(
        \Aligent\Stockists\Api\Data\StockistInterfaceFactory $stockistFactory,
        \Aligent\Stockists\Model\Stockist\Hydrator $stockistHydrator,
        \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->stockistHydrator = $stockistHydrator;
        $this->stockistRepository = $stockistRepository;
        $this->stockistFactory = $stockistFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $requestData = $this->getRequestData();
            $stockist = $this->stockistFactory->create();
            $this->processSave($stockist, $requestData);
            $this->messageManager->addSuccessMessage('Stockist has been saved');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', [
                    \Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID => $stockist->getStockistId(),
                    '_current' => true,
                ]);
            } elseif ($this->getRequest()->getParam('redirect_to_new')) {
                $resultRedirect->setPath('*/*/new', [
                    '_current' => true,
                ]);
            } else {
                $resultRedirect->setPath('*/*/edit');
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*/edit');
        }


        return $resultRedirect;
    }

    /**
     * @param \Aligent\Stockists\Api\Data\StockistInterface $stockist
     * @param array $requestData
     */
    private function processSave(\Aligent\Stockists\Api\Data\StockistInterface $stockist, array $requestData) {
        $stockist = $this->stockistHydrator->hydrate($stockist, $requestData);
        return $this->stockistRepository->save($stockist);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\SecurityViolationException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    private function getRequestData(): array {
        $request = $this->getRequest();
        if (!$request->isPost() || !$request->isSecure()) {
            throw new \Magento\Framework\Exception\SecurityViolationException(__('Must be a secured POST request'));
        }

        $requestData = $request->getPost()->toArray();
        if (empty($requestData['general'])) {
            throw new \Magento\Framework\Validation\ValidationException(__('Invalid data'));
        }
        return $requestData['general'];
    }


}
