<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Controller;

use CoopTilleuls\SyliusQuickImportPlugin\Exception\ImporterException;
use CoopTilleuls\SyliusQuickImportPlugin\Factory\FormErrorFactoryInterface;
use CoopTilleuls\SyliusQuickImportPlugin\Form\ImportType;
use CoopTilleuls\SyliusQuickImportPlugin\Service\Importer;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ImportAction
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Importer
     */
    private $importer;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var FormErrorFactoryInterface
     */
    private $formErrorFactory;

    public function __construct(Environment $twig, FormFactoryInterface $formFactory, TranslatorInterface $translator, FlashBagInterface $flashBag, Importer $importer, FormErrorFactoryInterface $formErrorFactory)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->flashBag = $flashBag;
        $this->importer = $importer;
        $this->formErrorFactory = $formErrorFactory;
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(ImportType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $report = $this->importer->import($form->get('file')->getData());
                $this->flashBag->add('success', $this->translator->trans('coop_tilleuls_quick_import_plugin.form.success'));
            } catch (ImporterException $exception) {
                $form->get('file')->addError($this->formErrorFactory->buildFormErrorByException($exception));
            }
        }

        $content = $this->twig->render('CoopTilleulsSyliusQuickImportPlugin::import.html.twig', [
            'form' => $form->createView(),
            'report' => $report ?? [],
        ]);

        return new Response($content);
    }
}
