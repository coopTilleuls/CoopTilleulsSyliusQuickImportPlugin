<?php

declare(strict_types=1);

namespace Tests\CoopTilleuls\SyliusQuickImportPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class StaticWelcomePage extends SymfonyPage implements WelcomePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGreeting(): string
    {
        return $this->getElement('greeting')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'coop_tilleuls_sylius_quick_import_static_welcome';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'greeting' => '#greeting',
        ]);
    }
}
