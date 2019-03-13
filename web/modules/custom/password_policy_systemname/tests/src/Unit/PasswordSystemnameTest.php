<?php

namespace Drupal\Tests\password_policy_systemname\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests the password systemname constraint.
 *
 * @group password_policy_systemname
 */
class PasswordSystemnameTest extends UnitTestCase {

  /**
   * Tests the password to make sure it doesn't contain the systemname.
   *
   * @dataProvider passwordSystemnameDataProvider
   */
  public function testPasswordSystemname($disallow_systemname, $user_context, $password, $result) {
    $password_test = $this->getMockBuilder('Drupal\password_policy_systemname\Plugin\PasswordConstraint\PasswordSystemname')
      ->disableOriginalConstructor()
      ->setMethods(['getConfiguration', 't'])
      ->getMock();

    $password_test
      ->method('getConfiguration')
      ->willReturn(['disallow_systemname' => $disallow_systemname]);

    $this->assertEquals($password_test->validate($password, $user_context)->isValid(), $result);
  }

  /**
   * Provides data for the testPasswordPassword method.
   */
  public function passwordSystemnameDataProvider() {
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
        'systemname',
        FALSE,
      ],
      [
        TRUE,
        $user_context,
        'my_systemname',
        FALSE,
      ],
    ];
  }

}
