Feature: Importing a simple catalog of product

  Background:
    Given I am logged in as an administrator

  Scenario:
    Given I am on "/admin/quick-import"
    When I attach the file "catalog.csv" to "import_file"
    And I press "Import"
    Then I should see "Your catalog has been imported with success."
