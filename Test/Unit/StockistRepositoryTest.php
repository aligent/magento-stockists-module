<?php

namespace Aligent\Stockists\Test\Unit;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\StockistSearchResultsInterfaceFactory;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\SearchCriteria\DistanceProcessor;
use Aligent\Stockists\Model\Stockist;
use Aligent\Stockists\Model\StockistFactory;
use Aligent\Stockists\Model\StockistRepository;
use Aligent\Stockists\Model\StockistValidation;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use PHPUnit\Framework\TestCase;

class StockistRepositoryTest extends TestCase
{
    private $stockistFactory;
    private $stockistResource;
    private $stockistCollectionFactory;
    private $searchResultsFactory;
    private $collectionProcessor;
    private $distanceProcessor;
    private $stockistValidation;

    protected function setUp(): void
    {
        $this->stockistFactory = $this->createMock(StockistFactory::class);
        $this->stockistResource = $this->createMock(StockistResource::class);
        $this->stockistCollectionFactory = $this->createMock(StockistResource\CollectionFactory::class);
        $this->searchResultsFactory = $this->createMock(StockistSearchResultsInterfaceFactory::class);
        $this->collectionProcessor = $this->createMock(CollectionProcessorInterface::class);
        $this->distanceProcessor = $this->createMock(DistanceProcessor::class);
        $this->stockistValidation = $this->createMock(StockistValidation::class);
    }

    private function createStockistRepository(): StockistRepository
    {
        return new StockistRepository(
            $this->stockistFactory,
            $this->stockistResource,
            $this->stockistCollectionFactory,
            $this->searchResultsFactory,
            $this->collectionProcessor,
            $this->distanceProcessor,
            $this->stockistValidation
        );
    }

    /**
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function testStockistIsDeleted()
    {
        $stockist = $this->createMock(Stockist::class);

        $this->stockistResource
            ->expects($this->once())
            ->method('delete')
            ->with($stockist);

        $stockistRepository = $this->createStockistRepository();

        $result = $stockistRepository->delete($stockist);

        $this->assertTrue($result);
    }

    /**
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function testDeleteMethodDoesNotFailWhenGivenStockistInterface()
    {
        $stockist = $this->createMock(StockistInterface::class);

        $this->stockistResource
            ->expects($this->never())
            ->method('delete');

        $stockistRepository = $this->createStockistRepository();

        $result = $stockistRepository->delete($stockist);

        $this->assertFalse($result);
    }

    /**
     * @throws NoSuchEntityException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function testStockistIsDeletedByIdentifier()
    {
        $identifier = 'TEST_IDENTIFIER';
        $stockist = $this->createMock(Stockist::class);
        $stockist
            ->method('getId')
            ->willReturn($identifier);

        $this->stockistFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($stockist);

        $this->stockistResource
            ->expects($this->once())
            ->method('load')
            ->with($stockist, $identifier, 'identifier')
            ->willReturn($stockist);

        $this->stockistResource
            ->expects($this->once())
            ->method('delete')
            ->with($stockist);

        $stockistRepository = $this->createStockistRepository();

        $result = $stockistRepository->deleteByIdentifier($identifier);

        $this->assertTrue($result);
    }

    /**
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function testExceptionWhenDeletingStockistWithNoId()
    {
        $identifier = 'TEST_IDENTIFIER';
        $stockist = $this->createMock(Stockist::class);

        $this->stockistFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($stockist);

        $this->stockistResource
            ->expects($this->once())
            ->method('load')
            ->with($stockist, $identifier, 'identifier')
            ->willReturn($stockist);

        $this->stockistResource
            ->expects($this->never())
            ->method('delete');

        $stockistRepository = $this->createStockistRepository();

        $this->expectException(NoSuchEntityException::class);

        $stockistRepository->deleteByIdentifier($identifier);
    }
}
