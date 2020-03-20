<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ImportAction
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        $content = $this->twig->render('CoopTilleulsSyliusQuickImportPlugin::import.html.twig');

        return new Response($content);
    }
}
