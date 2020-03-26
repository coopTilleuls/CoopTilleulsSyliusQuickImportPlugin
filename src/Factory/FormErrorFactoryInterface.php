<?php

namespace CoopTilleuls\SyliusQuickImportPlugin\Factory;

use CoopTilleuls\SyliusQuickImportPlugin\Exception\ImporterException;

interface FormErrorFactoryInterface
{
    public function buildFormErrorByException(ImporterException $exception);
}