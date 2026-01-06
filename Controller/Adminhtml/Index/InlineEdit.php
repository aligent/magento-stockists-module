<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;

use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Validation\ValidationException;

class InlineEdit extends Save implements HttpPostActionInterface
{

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {

        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);
        try {
            foreach ($requestData as $stockistData) {
                $stockistId = (int)$stockistData[StockistInterface::STOCKIST_ID];
                $stockist = $this->stockistRepository->getById($stockistId);
                $this->processSave($stockist, $stockistData);
            }
        } catch (\Exception $e) {
            $errorMessages[] = __('[ID: %value] %message', [
                'value' => $stockistId,
                'message' => $e->getMessage()
            ]);
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);

        return $resultJson;
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
