<?php

namespace CoopTilleuls\SyliusQuickImportPlugin\Factory;

use CoopTilleuls\SyliusQuickImportPlugin\Exception\ImporterException;
use Symfony\Component\Form\FormError;

interface FormErrorFactoryInterface
{
    public function buildFormErrorByException(ImporterException $exception): FormError;
}
