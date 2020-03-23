<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Service;


use CoopTilleuls\SyliusQuickImportPlugin\Exception\ImporterException;
use CoopTilleuls\SyliusQuickImportPlugin\Form\ImportType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Importer
{
    public function import(UploadedFile $file = null): array
    {
        if (null === $file) {
            throw new ImporterException('File is missing');
        }

        $extension = array_slice(explode('.', $file->getClientOriginalName()), -1)[0];
        if (!in_array(\sprintf('.%s', $extension), ImportType::ALLOWED_EXTENSIONS, true)) {
            throw new ImporterException('Invalid file extension');
        }

        return [];
    }
}
