<?php

namespace Drupal\Tests\password_policy_name\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the password name constraint.
 *
 * @group password_policy_name
 */
class PasswordNameTest extends UnitTestCase {

  /**
   * Tests the password to make sure it doesn't contain either the user's first or last name.
   *
   * @dataProvider passwordNameDataProvider
   */
  public function testPasswordName($disallow_name, $user_context, $password, $result) {
    $name_test = $this->getMockBuilder('Drupal\password_policy_name\Plugin\PasswordConstraint\PasswordName')
      ->disableOriginalConstructor()
      ->setMethods(['getConfiguration', 't'])
      ->getMock();

    $name_test
      ->method('getConfiguration')
      ->willReturn(['disallow_name' => $disallow_name]);

    $this->assertEquals($name_test->validate($password, $user_context)->isValid(), $result);
  }

  /**
   * Provides data for the testPasswordName method.
   */
  public function passwordNameDataProvider() {
    $user_context = [
      'mail' => 'test@example.com',
      'name' => 'username',
      'uid' => 10,
    ];

    return [
      // Passing conditions.
      [
        TRUE,
        $user_context,
        'password',
        TRUE,
      ],
      [
        FALSE,
        $user_context,
        'username',
        TRUE,
      ],
      // Failing conditions.
      [
        TRUE,
        $user_context,
        'name',
        FALSE,
      ],
      [
        TRUE,
        $user_context,
        'my_name',
        FALSE,
      ],
    ];
  }

}
