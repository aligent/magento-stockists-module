<?php

namespace Aligent\Stockists\Test\Unit;

use Aligent\Stockists\Api\Data\StockistSearchResultsInterfaceFactory;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\SearchCriteria\DistanceProcessor;
use Aligent\Stockists\Model\Stockist;
use Aligent\Stockists\Model\StockistFactory;
use Aligent\Stockists\Model\StockistRepository;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class StockistRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected $stockistRepository;

    public function testStockistIsDeleted()
    {
        $stockist = $this->createMock(Stockist::class);
        $stockistResource = $this->createMock(StockistResource::class);
        $stockistResource->expects($this->once())
            ->method('delete')
            ->with($stockist);

        $stockistFactory = $this->createMock(StockistFactory::class);
        $stockistCollectionFactory = $this->createMock(StockistResource\CollectionFactory::class);
        $searchResultsFactory = $this->createMock(StockistSearchResultsInterfaceFactory::class);
        $collectionProcessor = $this->createMock(CollectionProcessorInterface::class);
        $distanceProcessor = $this->createMock(DistanceProcessor::class);

        $this->stockistRepository = new StockistRepository(
            $stockistFactory,
            $stockistResource,
            $stockistCollectionFactory,
            $searchResultsFactory,
            $collectionProcessor,
            $distanceProcessor
        );

        $result = $this->stockistRepository->delete($stockist);

        $this->assertTrue($result);
    }
}
