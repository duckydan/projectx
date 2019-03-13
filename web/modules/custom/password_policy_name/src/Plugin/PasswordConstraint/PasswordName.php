<?php

namespace Drupal\password_policy_name\Plugin\PasswordConstraint;

use Drupal\Core\Form\FormStateInterface;
use Drupal\password_policy\PasswordConstraintBase;
use Drupal\password_policy\PasswordPolicyValidation;

/**
 * Ensures the password doesn't contain either the first or last name.
 *
 * @PasswordConstraint(
 *   id = "password_name",
 *   title = @Translation("Password name"),
 *   description = @Translation("Password must not contain either their first or last name"),
 *   error_message = @Translation("Your password contains your first or last name.")
 * )
 */
class PasswordName extends PasswordConstraintBase {

  /**
   * {@inheritdoc}
   */
  public function validate($password, $user_context) {
    $config = $this->getConfiguration();
    $validation = new PasswordPolicyValidation();
    if ($config['disallow_name'] && (stripos($password, $user_context['field_last_name']) || stripos($password, $user_context['field_first_name'])) !== FALSE) {
      $validation->setErrorMessage($this->t('Password must not contain the either the first or last name.'));
    }

    return $validation;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'disallow_name' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->getConfiguration();

    $form['disallow_name'] = [
      '#type' => 'hidden',
      '#value' => $config['disallow_name'],
    ];

    $form['disallow_name_message'] = [
      '#type' => 'description',
      '#markup' => $this->t('Prevent user from having a password containing either their first or last name.'),
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
    $this->configuration['disallow_name'] = $form_state->getValue('disallow_name');
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->t("Password must not contain either the user's first or last name.");
  }

}
