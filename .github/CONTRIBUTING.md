# Contributing to Sylius Quick Import Plugin

First of all, thank you for contributing, you're awesome!

To have your code integrated in the project, there are some rules to follow, but don't panic, it's easy!

## Reporting Bugs

If you happen to find a bug, you may report it using Github by following these 3 points:

  * Check if the bug is not already reported!
  * A clear title to resume the issue
  * A description of the workflow needed to reproduce the bug,

> _NOTE:_ Don’t hesitate to give as much information as you can (OS, PHP version extensions...)

## Install the project locally

1. Run `git clone git@github.com:coopTilleuls/CoopTilleulsSyliusQuickImportPlugin.git`.

2. From `CoopTilleulsSyliusQuickImportPlugin` directory run the following commands:

    ```bash
    $ composer install
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn build)
    $ (cd tests/Application && bin/console assets:install public -e test)

    $ (cd tests/Application && bin/console doctrine:database:create -e test)
    $ (cd tests/Application && bin/console doctrine:schema:create -e test)
    ```

To be able to setup a plugin's database, remember to configure you database credentials in `tests/Application/.env`.

### Opening tests application in dev environment.

    ```bash
    $ (cd tests/Application && bin/console sylius:fixtures:load -e dev)
    $ (cd tests/Application && bin/console server:run -d public -e dev)
    ```
