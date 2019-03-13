<?php

namespace Drupal\Tests\password_policy_password\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the password password constraint.
 *
 * @group password_policy_password
 */
class PasswordPasswordTest extends UnitTestCase {

  /**
   * Tests the password to make sure it doesn't contain the phrase of password.
   *
   * @dataProvider passwordPasswordDataProvider
   */
  public function testPasswordPassword($disallow_password, $user_context, $password, $result) {
    $password_test = $this->getMockBuilder('Drupal\password_policy_password\Plugin\PasswordConstraint\PasswordPassword')
      ->disableOriginalConstructor()
      ->setMethods(['getConfiguration', 't'])
      ->getMock();

    $password_test
      ->method('getConfiguration')
      ->willReturn(['disallow_password' => $disallow_password]);

    $this->assertEquals($password_test->validate($password, $user_context)->isValid(), $result);
  }

  /**
   * Provides data for the testPasswordPassword method.
   */
  public function passwordPasswordDataProvider() {
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
        'password',
        FALSE,
      ],
      [
        TRUE,
        $user_context,
        'my_password',
        FALSE,
      ],
    ];
  }

}
