<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportType extends AbstractType
{
    const ALLOWED_EXTENSIONS = ['.xlsx', '.ods', '.csv'];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'required' => true,
                'label' => 'coop_tilleuls_quick_import_plugin.form.catalog',
                'attr' => ['accept' => implode(', ',self::ALLOWED_EXTENSIONS)],
            ])
        ;
    }
}
