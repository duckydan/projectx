<?php

namespace Drupal\password_policy_systemname\Plugin\PasswordConstraint;

use Drupal\Core\Form\FormStateInterface;
use Drupal\password_policy\PasswordConstraintBase;
use Drupal\password_policy\PasswordPolicyValidation;

/**
 * Ensures the password doesn't contain the systemname.
 *
 * @PasswordConstraint(
 *   id = "password_systemname",
 *   title = @Translation("Password systemname"),
 *   description = @Translation("Password must not contain the systemname"),
 *   error_message = @Translation("Your password contains the systemname.")
 * )
 */
class PasswordSystemname extends PasswordConstraintBase {

  /**
   * {@inheritdoc}
   */
  public function validate($password, $user_context) {
    $config = $this->getConfiguration();
    $validation = new PasswordPolicyValidation();

    if ($config['disallow_systemname'] && (stripos($password, 'fsa') !== FALSE || stripos($password, 'ifap') !== FALSE)) {
      $validation->setErrorMessage($this->t('Password must not contain the phrase fsa or ifap.'));
    }

    return $validation;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'disallow_systemname' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->getConfiguration();

    $form['disallow_systemname'] = [
      '#type' => 'hidden',
      '#value' => $config['disallow_systemname'],
    ];

    $form['disallow_systemname_message'] = [
      '#type' => 'description',
      '#markup' => $this->t('Prevent user from having a password containing the systemname.'),
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
    $this->configuration['disallow_systemname'] = $form_state->getValue('disallow_systemname');
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->t("Password must not contain the phrase of fsa or ifap.");
  }

}
