<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Factory;

use CoopTilleuls\SyliusQuickImportPlugin\Exception\InvalidFileExtensionException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\ImporterException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\InvalidFileFormatException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\MissingDataException;
use CoopTilleuls\SyliusQuickImportPlugin\Exception\MissingFileException;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormErrorFactory implements FormErrorFactoryInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildFormErrorByException(ImporterException $exception): FormError
    {
        switch (get_class($exception)) {
            case InvalidFileExtensionException::class:
                $error = new FormError($this->translator->trans('coop_tilleuls_quick_import_plugin.form.invalid_file_extension'));
                break;
            case InvalidFileFormatException::class:
                $error = new FormError($this->translator->trans('coop_tilleuls_quick_import_plugin.form.invalid_file_format'));
                break;
            case MissingDataException::class:
                $error = new FormError($this->translator->trans('coop_tilleuls_quick_import_plugin.form.missing_data'));
                break;
            case MissingFileException::class:
                $error = new FormError($this->translator->trans('coop_tilleuls_quick_import_plugin.form.missing_file'));
                break;
            default:
                $error = new FormError($this->translator->trans('coop_tilleuls_quick_import_plugin.form.invalid_file'));
                break;
        }

        return $error;
    }
}
