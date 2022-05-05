Feature: expect return type
  In order to have typed Expectations
  As a Psalm user
  I need Psalm to typecheck my uses of expect

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
  Scenario: run without PossiblyUndefinedMethod
    Given I have the following code 
      """
      <?php
      test('Expectation', function () {
          expect(true)->toBeTrue();
      });
      """
    When I run Psalm
    Then I see these errors
      | Type                  | Message                                                                                  |
      | InternalMethod        | The method Pest\Expectation::toBeTrue is internal to Pest but called from root namespace |
    And I see no other errors
