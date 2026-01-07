<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Ui\Component\Control\Stockist;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SaveButton constructor.
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        return [
            'label'     => __('Save Stockist'),
            'class'     =>  'save primary'
        ];
    }
}
