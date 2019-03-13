<?php

namespace Drupal\password_policy_password\Plugin\PasswordConstraint;

use Drupal\Core\Form\FormStateInterface;
use Drupal\password_policy\PasswordConstraintBase;
use Drupal\password_policy\PasswordPolicyValidation;

/**
 * Ensures the password doesn't contain the phrase password.
 *
 * @PasswordConstraint(
 *   id = "password_password",
 *   title = @Translation("Password password"),
 *   description = @Translation("Password must not contain the phrase password"),
 *   error_message = @Translation("Your password contains the phrase password.")
 * )
 */
class PasswordPassword extends PasswordConstraintBase {

  /**
   * {@inheritdoc}
   */
  public function validate($password, $user_context) {
    $config = $this->getConfiguration();
    $validation = new PasswordPolicyValidation();

    if ($config['disallow_password'] && stripos($password, 'password') !== FALSE) {
      $validation->setErrorMessage($this->t('Password must not contain the phrase password.'));
    }

    return $validation;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'disallow_password' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->getConfiguration();

    $form['disallow_password'] = [
      '#type' => 'hidden',
      '#value' => $config['disallow_password'],
    ];

    $form['disallow_password_message'] = [
      '#type' => 'description',
      '#markup' => $this->t('Prevent user from having a password containing the phrase of password.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['disallow_password'] = $form_state->getValue('disallow_password');
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->t("Password must not contain the phrase of password.");
  }

}
