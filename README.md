<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Sylius Quick Import Plugin</h1>

<p align="center">Plugin to quicky import taxons and products in Sylius catalog.</p>

> :warning: This plugin is not a replacement for [SyliusImportExportPlugin][SyliusImportExportPlugin] which continues 
> to be the best solution to import/export full catalog for Sylius.

## Context

This plugin has been written during the COVID-19 pandemic. The goal was to allow shoppers to build quick website and 
to import catalog without effort.

## When using it?

- you need to import one level of taxons.
- you need to import simple product (code, name, description, main taxon, price, stock).
- that's it. If you need more, you should use [SyliusImportExportPlugin][SyliusImportExportPlugin].

## Installation

1. Run `composer require tilleuls/sylius-quick-import-plugin`.

2. Add plugin dependency to your `config/bundles.php`:

    ```php
    return [
        ...
        CoopTilleuls\SyliusQuickImportPlugin\CoopTilleulsSyliusQuickImportPlugin::class => ['all' => true],
    ];
    ```
    
3. Import the routing in your `config/routes.yaml`
    ```yaml
    coop_tilleuls_sylius_quick_import_admin:
        resource: "@CoopTilleulsSyliusQuickImportPlugin/Resources/config/admin_routing.yml"
        prefix: /admin
    ```

## Usage

You can find a new entry "Import catalog" in your admin menu. In these section you can download spreadsheet examples 
and import a catalog which respect this schema.

[SyliusImportExportPlugin]: https://github.com/FriendsOfSylius/SyliusImportExportPlugin
