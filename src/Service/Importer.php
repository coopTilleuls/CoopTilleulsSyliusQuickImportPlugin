<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Service;

use CoopTilleuls\SyliusQuickImportPlugin\Exception\InvalidFileExtensionException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\InvalidFileFormatException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\MissingDataException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\MissingFileException;
use CoopTilleuls\SyliusQuickImportPlugin\Form\ImportType;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Importer
{
    /**
     * @var TaxonFactoryInterface
     */
    private $taxonFactory;
    /**
     * @var ProductFactoryInterface
     */
    private $productFactory;
    /**
     * @var FactoryInterface
     */
    private $channelPricingFactory;
    /**
     * @var FactoryInterface
     */
    private $productTaxonFactory;
    /**
     * @var TaxonRepository
     */
    private $taxonRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;
    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var string
     */
    private $currentLocale;

    public function __construct(
        TaxonFactoryInterface $taxonFactory,
        ProductFactoryInterface $productFactory,
        FactoryInterface $channelPricingFactory,
        FactoryInterface $productTaxonFactory,
        TaxonRepository $taxonRepository,
        ProductRepository $productRepository,
        ChannelContextInterface $channelContext,
        SlugGeneratorInterface $slugGenerator,
        EntityManager $em,
        string $currentLocale
    )
    {
        $this->taxonFactory = $taxonFactory;
        $this->productFactory = $productFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->taxonRepository = $taxonRepository;
        $this->productRepository = $productRepository;
        $this->channelContext = $channelContext;
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        $this->currentLocale = $currentLocale;
    }

    /**
     * @throws InvalidFileExtensionException
     * @throws InvalidFileFormatException
     * @throws MissingDataException
     * @throws MissingFileException
     */
    public function import(UploadedFile $file = null): array
    {
        if (null === $file || false === $realPath = $file->getRealPath()) {
            throw new MissingFileException('File is missing.');
        }

        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        if (!in_array(\sprintf('.%s', $extension), ImportType::ALLOWED_EXTENSIONS, true)) {
            throw new InvalidFileExtensionException('Invalid file extension.');
        }

        $data = $this->extractData($realPath);
        $formattedData = $this->doImport($data);

        if (!\count($formattedData)) {
            throw new MissingDataException('File is empty.');
        }

        return $formattedData;
    }

    protected function extractData(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $max = $sheet->getHighestRowAndColumn();

        if ('F' !== $max['column']) {
            throw new InvalidFileFormatException('Wrong file format.');
        }

        return $sheet->rangeToArray(
            \sprintf('A1:%s%s', $max['column'], $max['row']),
            null,
            true,
            true
        );
    }

    protected function doImport(array $data): array
    {
        if (!\count($data)) {
            return [];
        }

        $list = [];

        foreach ($data as $row) {
            [$reference, , $category] = $row;

            if ('reference' === $reference) {
                continue;
            }

            if (!array_key_exists($category, $list)) {
                $taxon = $this->addTaxon($category);
                $list[$category] = ['taxon' => $taxon, 'products' => []];
            } else {
                /** @var TaxonInterface $taxon */
                $taxon = $list[$category]['taxon'];
            }

            $list[$category]['products'][] = $this->addOrGetProduct($row, $taxon);
        }

        if (\count($list)) {
            $this->em->flush();
        }

        return $list;
    }

    protected function addTaxon(string $name): TaxonInterface
    {
        /** @var null|TaxonInterface $taxon */
        $taxon = $this->taxonRepository->findOneByCode($name);

        if (null !== $taxon) {
            return $taxon;
        }

        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCurrentLocale($this->currentLocale);
        $taxon->setCode($name);
        $taxon->setName($name);
        $taxon->setSlug($this->slugGenerator->generate($name));

        $this->em->persist($taxon);

        return $taxon;
    }

    protected function applyTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $product->setMainTaxon($taxon);

        /** @var ProductTaxon $productTaxon */
        $productTaxon = $this->productTaxonFactory->createNew();
        $productTaxon->setTaxon($taxon);
        $productTaxon->setProduct($product);

        $product->addProductTaxon($productTaxon);
    }

    protected function addOrGetProduct(array $row, TaxonInterface $taxon): ProductInterface
    {
        [$reference, $name, , $price, $stock, $description] = $row;
        // $reference should always be a string but if the reference used by the shop owner is a integer (an ISBN book for example)  it will throw an error
        $reference = (string) $reference;

        $product = $this->productRepository->findOneByCode($reference);

        if (null !== $product) {
            return $product;
        }

        $channel = $this->channelContext->getChannel();

        // - product
        /** @var ProductInterface $product */
        $product = $this->productFactory->createWithVariant();
        $product->setCurrentLocale($this->currentLocale);
        $product->setCode($reference);
        $product->setDescription($description);
        $product->setName($name);
        $product->setSlug($this->slugGenerator->generate(\sprintf('%s-%s', $name, $reference)));
        $product->addChannel($channel);

        // - taxon
        $this->applyTaxon($product, $taxon);

        // - price
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setChannelCode($channel->getCode());
        $channelPricing->setPrice($price * 100);

        // - variant
        /** @var ProductVariant $variant */
        $variant = $product->getVariants()[0];
        $variant->setCode($reference);
        $variant->setName($name);
        $variant->setOnHand($stock);
        $variant->addChannelPricing($channelPricing);

        $this->em->persist($product);

        return $product;
    }
}
