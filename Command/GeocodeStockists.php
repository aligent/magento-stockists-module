<?php

declare(strict_types=1);

namespace Aligent\Stockists\Command;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Service\GeocodeStockist as GeocodeStockistService;
use Aligent\Stockists\Model\GeoSearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeocodeStockists extends Command
{
    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * @var GeoSearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GeocodeStockistService
     */
    private $geocodeStockist;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    public function __construct(
        StockistRepositoryInterface $stockistRepository,
        GeoSearchCriteriaBuilder $searchCriteriaBuilder,
        GeocodeStockistService $geocodeStockist,
        FilterBuilder $filterBuilder
    ) {
        parent::__construct();
        $this->stockistRepository = $stockistRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->geocodeStockist = $geocodeStockist;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('aligent:stockists:geocode')
            ->setDescription('Geocode stockists which are missing co-ordinate information');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $geocodeFailed = 0;
        $geocodeSuccess = 0;

        /** @var StockistInterface $stockist */
        foreach ($this->getNonGeocodedStockists() as $stockist) {
            $this->geocodeStockist->execute($stockist, true);
            if ($stockist->getLat() !== null) {
                $output->write('.');
                $this->stockistRepository->save($stockist);
                $geocodeSuccess++;
            } else {
                $output->write('E');
                $geocodeFailed++;
            }
        }

        $output->writeln(sprintf('Geocoding complete. Updated: %d, failed: %d', $geocodeSuccess, $geocodeFailed));
        return 0;
    }

    /**
     * @return array
     */
    private function getNonGeocodedStockists(): array
    {
        $filter = $this->filterBuilder->setField('lat')->setConditionType('null')->create();
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($filter)->create();
        return $this->stockistRepository->getList($searchCriteria)->getItems();
    }
}
