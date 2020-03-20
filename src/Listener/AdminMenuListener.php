<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusQuickImportPlugin\Listener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class AdminMenuListener
{
    public function addAdminMenuItem(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $catalog = $menu->getChild('catalog');

        $catalog
            ->addChild('quick-import', ['route' => 'coop_tilleuls_sylius_quick_import'])
            ->setLabel('coop_tilleuls_quick_import_plugin.ui.title')
            ->setLabelAttribute('icon', 'sign in alternate')
        ;
    }
}
