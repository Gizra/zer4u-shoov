Feature: Add to cart
  In order to be able to view a blog post
  As an anonymous user
  We need to be able to have access to a blog post page

  @javascript
  Scenario: Visit blog post page
    Given I am an anonymous user
    When  I visit "sporty-style-pink-purple.html"
    And   I select size and add to cart
    Then  I should see the item added to the cart
