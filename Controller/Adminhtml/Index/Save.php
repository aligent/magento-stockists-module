<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\StockistInterfaceFactory;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\Stockist\Hydrator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\HttpRequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Validation\ValidationException;

/**
 * Save Controller
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aligent_Stockists::manage';

    /**
     * @var Hydrator
     */
    private $stockistHydrator;

    /**
     * @var StockistRepositoryInterface
     */
    protected $stockistRepository;

    /**
     * @var StockistInterfaceFactory
     */
    private $stockistFactory;

    /**
     * @param StockistInterfaceFactory $stockistFactory
     * @param Hydrator $stockistHydrator
     * @param StockistRepositoryInterface $stockistRepository
     * @param Context $context
     */
    public function __construct(
        StockistInterfaceFactory $stockistFactory,
        Hydrator $stockistHydrator,
        StockistRepositoryInterface $stockistRepository,
        Context $context
    ) {
        parent::__construct($context);
        $this->stockistHydrator = $stockistHydrator;
        $this->stockistRepository = $stockistRepository;
        $this->stockistFactory = $stockistFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $requestData = $this->getRequestData();
            $stockist = $this->stockistFactory->create();

            $stockistId = $requestData[StockistInterface::STOCKIST_ID];
            if (!empty($stockistId)) {
                $stockist = $this->stockistRepository->getById((int)$stockistId);
            }

            $this->processSave($stockist, $requestData);
            $this->messageManager->addSuccessMessage('Stockist has been saved');
            if ($this->getRequest()->getParam('redirect_to_new')) {
                $resultRedirect->setPath(
                    '*/*/new',
                    [
                        '_current' => true
                    ]
                );
            } else {
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        StockistInterface::STOCKIST_ID => $stockist->getStockistId(),
                        '_current' => true
                    ]
                );
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('*/*');
        }

        return $resultRedirect;
    }

    /**
     * @param StockistInterface $stockist
     * @param array $requestData
     * @return StockistInterface
     * @throws ValidationException
     */
    protected function processSave(StockistInterface $stockist, array $requestData): StockistInterface
    {
        $stockist = $this->stockistHydrator->hydrate($stockist, $requestData);
        return $this->stockistRepository->save($stockist);
    }

    /**
     * @return array
     * @throws SecurityViolationException
     * @throws ValidationException
     */
    private function getRequestData(): array
    {
        $request = $this->getRequest();
        if (!$request->isPost() || !$request->isSecure()) {
            throw new SecurityViolationException(__('Must be a secured POST request'));
        }

        $requestData = $request->getPost()->toArray();
        if (empty($requestData['general'])) {
            throw new ValidationException(__('Invalid data'));
        }
        return $requestData['general'];
    }
}
