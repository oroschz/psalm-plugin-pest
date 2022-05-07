Feature: $this in closure scope
  In order to have typed Assertions
  As a Psalm user
  I need Psalm to typecheck all uses of $this

  Background:
    Given I have the following config
      """
      <?xml version="1.0"?>
      <psalm errorLevel="1">
        <projectFiles>
          <directory name="."/>
        </projectFiles>
        <plugins>
          <pluginClass class="Oroschz\PsalmPluginPest\Plugin" />
        </plugins>
      </psalm>
      """
  Scenario: run without issues
    Given I have the following code 
      """
      <?php
      test('Assertion', function () {
          $this->assertTrue(true);
      });
      """
    When I run Psalm
    Then I see no errors

  Scenario: results in InvalidScope outside the closure
    Given I have the following code 
      """
      <?php
      $this->assertTrue(true);
      """
    When I run Psalm
    Then I see these errors
      | Type         | Message                                           |
      | InvalidScope | Invalid reference to $this in a non-class context |
    And I see no other errors
