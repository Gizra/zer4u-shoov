Feature: Add to cart
  In order to be able to purchase items
  As an anonymous user
  We need to be able to to add items to cart

  @javascript
  Scenario: Add item to cart
    Given I am an anonymous user
    When  I visit "page_18764"
    And   I select item and add to cart
    And   I add my personal info
    # Then  I should see the item added to the cart
