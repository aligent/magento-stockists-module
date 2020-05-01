<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Controller\Adminhtml\Index;

/**
 * Class InlineEdit
 */
class InlineEdit extends \Aligent\Stockists\Controller\Adminhtml\Index\Save implements \Magento\Framework\App\Action\HttpPostActionInterface
{

    /**
     * @inheritdoc
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {

        $errorMessages = [];
        $request = $this->getRequest();
        $requestData = $request->getParam('items', []);
        try {
            foreach ($requestData as $stockistData) {
                $stockistId = (int)$stockistData[\Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID];
                $stockist = $this->stockistRepository->getById($stockistId);
                $this->processSave($stockist, $stockistData);
            }
        } catch (\Exception $e) {
            $errorMessages[] = __('[ID: %value] %message', [
                'value' => $stockId,
                'message' => $e->getMessage()
            ]);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'messages' => $errorMessages,
            'error' => count($errorMessages),
        ]);

        return $resultJson;

    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\SecurityViolationException
     * @throws \Magento\Framework\Validation\ValidationException
     */
    protected function getRequestData(): array {
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
