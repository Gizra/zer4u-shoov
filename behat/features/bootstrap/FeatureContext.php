<?php

use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

class FeatureContext extends MinkContext implements SnippetAcceptingContext {

  /**
   * @Given I am an anonymous user
   */
  public function iAmAnAnonymousUser() {
    // Just let this passthru.
  }

  /**
   * @When I select item and add to cart
   */
  public function iSelectItemAndAddToCart() {
    // Wait for the "Add to cart" button to appear.
    $this->iWaitForCssElement('#addCart');

    // Add to cart.
    $element = $this->getSession()->getPage()->find('css', '#addCart img');
    $element->click();
  }

  /**
   * @When I add my personal info
   */
  public function iAddMyPersonalInfo() {
    // Wait for the "Personal info" overlay to appear.
    $this->iWaitForCssElement('#fbBoxLiner');

    $this->getSession()->switchToIFrame('fbContent');

    $element = $this->getSession()->getPage()->find('css', '#RadDatePicker1_popupButton');
    $element->click();

    // Go to next dates.
    $element = $this->getSession()->getPage()->find('css', '.rcFastNext');
    $element->click();
    $element->click();

    // Select first enabled date.
    $element = $this->getSession()->getPage()->find('css', 'td.rcOtherMonth');
    $element->click();

    $element = $this->getSession()->getPage()->find('css', '#searchbox2');
    $element->setValue('תל אביב-יפו');

    $element = $this->getSession()->getPage()->find('css', '#searchbox3_Input');
    $element->click();

    $this->iWaitForCssElement('#searchbox3_DropDown li.rcbItem');


    // Get the 3rd element, as clicking on the first one seems to click the
    // wrong element and cause an error.
    $element = $this->getSession()->getPage()->find('css', '#searchbox3_DropDown .rcbItem:nth-child(3)');
    $element->click();


    $element = $this->getSession()->getPage()->find('css', '#HOMENUMBER');
    $element->setValue('5');


    $element = $this->getSession()->getPage()->find('css', '#SetSessionButton');
    $element->click();

    // Wait for overlay with buttons.
    $this->iWaitForCssElement('.popupcart_button');

    $element = $this->getSession()->getPage()->find('css', '.popupcart_button');
    $element->click();
  }

  /**
   * @Then I should see the item added to the cart
   */
  public function iShouldSeeTheItemAddedToTheCart() {
    $this->getSession()->switchToIFrame('iframecart');
    $this->waitFor(function($context) {
      try {
        if (!$element = $context->getSession()->getPage()->find('css', '.cart_bg')) {
          return FALSE;
        }
        return $element->getText() == '1';
      }
      catch (WebDriver\Exception $e) {
        if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
          return FALSE;
        }
        throw $e;
      }
    });
  }

  /**
   * @Then I should wait for the text :arg1 to :arg2
   */
  public function iShouldWaitForTheTextTo($text, $appear) {
    $this->waitForXpathNode(".//*[contains(normalize-space(string(text())), \"$text\")]", $appear == 'appear');
  }


  /**
   * @Then /^I wait for css element "([^"]*)" to "([^"]*)"$/
   */
  public function iWaitForCssElement($element, $appear = 'appear') {
    $xpath = $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $element);
    $this->waitForXpathNode($xpath, $appear == 'appear');
  }


  /**
   * Helper function; Execute a function until it return TRUE or timeouts.
   *
   * @param $fn
   *   A callable to invoke.
   * @param int $timeout
   *   The timeout period. Defaults to 15 seconds.
   *
   * @throws Exception
   */
  private function waitFor($fn, $timeout = 15000) {
    $start = microtime(true);
    $end = $start + $timeout / 1000.0;
    while (microtime(true) < $end) {
      if ($fn($this)) {
        return;
      }
    }
    throw new \Exception('waitFor timed out.');
  }
  /**
   * Wait for an element by its XPath to appear or disappear.
   *
   * @param string $xpath
   *   The XPath string.
   * @param bool $appear
   *   Determine if element should appear. Defaults to TRUE.
   *
   * @throws Exception
   */
  private function waitForXpathNode($xpath, $appear = TRUE) {
    $this->waitFor(function($context) use ($xpath, $appear) {
        try {
          $nodes = $context->getSession()->getDriver()->find($xpath);
          if (count($nodes) > 0) {
            $visible = $nodes[0]->isVisible();
            return $appear ? $visible : !$visible;
          }
          return !$appear;
        }
        catch (WebDriver\Exception $e) {
          if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
            return !$appear;
          }
          throw $e;
        }
      });
  }
}
