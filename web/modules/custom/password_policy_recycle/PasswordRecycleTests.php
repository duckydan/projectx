<?php

namespace Drupal\password_policy_history\Plugin\PasswordConstraint;

use Drupal\Tests\UnitTestCase;

/**
 * Class PasswordReuseTest.
 *
 * @coversDefaultClass Drupal\govcms_password_policy\Plugin\PasswordConstraint\PasswordReuse
 * @group govcms
 */
class PasswordRecycleTests extends UnitTestCase {

  public $passwordRecycleMock;

  /**
   * Set up the test mock.
   */
  public function setup() {
    $password_reuse = $this->getMockBuilder('\Drupal\password_policy_recycle\Plugin\PasswordConstraint\PasswordRecycle')
      ->setMethods(['getHashes', 'getPasswordService', 't'])
      ->disableOriginalConstructor()
      ->getMock();

    $password_reuse
      ->expects($this->once())
      ->method('getHashes')
      ->willReturn([(object) ['pass_hash' => 'fake_password']]);

    $this->passwordRecycleMock = $password_reuse;
  }

  /**
   * Ensure that a password check success results in the correct output.
   *
   * @dataProvider userContextProvider
   */
  public function testPasswordReuseValid($password, $user_context) {
    $passwordService = $this->getPasswordService(FALSE);

    $this->passwordRecycleMock->expects($this->once())
      ->method('getPasswordService')
      ->willReturn($passwordService);

    $this->assertEquals($this->passwordRecycleMock->validate($password, $user_context)->isValid(), TRUE);
  }

  /**
   * Ensure that a password check failure results in the correct output.
   *
   * @dataProvider userContextProvider
   */
  public function testPasswordReuseInvalid($password, $user_context) {
    $passwordService = $this->getPasswordService(TRUE);

    $this->passwordRecycleMock->expects($this->once())
      ->method('getPasswordService')
      ->willReturn($passwordService);

    $this->passwordRecycleMock->expects($this->once())
      ->method('t')
      ->willReturn('Invalid password');

    $this->assertEquals($this->passwordRecycleMock->validate($password, $user_context)->isValid(), FALSE);
  }

  /**
   * Return a password interface mock object.
   *
   * @return \Drupal\Core\Password\PasswordInterface
   *   The password interface mock.
   */
  public function getPasswordService($return) {
    $password_service = $this->getMockBuilder('Drupal\Core\Password\PasswordInterface')
      ->disableOriginalConstructor()
      ->getMock();

    $password_service->method('check')->willReturn($return);

    return $password_service;
  }

  /**
   * Data provider for the user context.
   *
   * @return array
   *   The user context array.
   */
  public function userContextProvider() {
    $user_context = [
      'mail' => 'test@example.com',
      'name' => 'username',
      'uid' => 10,
    ];

    return [['password', $user_context]];
  }

}
