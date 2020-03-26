Feature: Importing a simple catalog of product

  Background:
    Given I am logged in as an administrator

  Scenario: As a shop administrator I can upload a file to import my catalog
    Given I am on "/admin/quick-import"
    When I attach the file "catalog.csv" to "import_file"
    And I press "Import"
    Then I should see "Your catalog has been imported with success."

  Scenario: As a shop administrator I have meaningful error if I submit the form without file
    Given I am on "/admin/quick-import"
    When I press "Import"
    Then I should see "No file sent. Please provide a file. Use the examples provided above."

  Scenario: As a shop administrator I have meaningful error if I submit the form with an invalid file extension
    Given I am on "/admin/quick-import"
    When I attach the file "catalog.png" to "import_file"
    And I press "Import"
    Then I should see "Invalid file extension. You have to provide a file with extension XLSX, ODS ou CSV. Use the examples provided above."

  Scenario: As a shop administrator I have meaningful error if I submit the form with an invalid file format
    Given I am on "/admin/quick-import"
    When I attach the file "invalid_catalog.csv" to "import_file"
    And I press "Import"
    Then I should see "The file sent does not respect the good format. Use the examples provided above."

  Scenario: As a shop administrator I have meaningful error if I submit the form with an invalid file format
    Given I am on "/admin/quick-import"
    When I attach the file "empty_catalog.csv" to "import_file"
    And I press "Import"
    Then I should see "The file sent seems empty. Use the examples provided above."
