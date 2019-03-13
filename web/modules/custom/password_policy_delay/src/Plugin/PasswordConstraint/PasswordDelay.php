<?php

namespace Drupal\password_policy_delay\Plugin\PasswordConstraint;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\password_policy\PasswordConstraintBase;
use Drupal\password_policy\PasswordPolicyValidation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Enforces delay how long before passwords can be changed.
 *
 * @PasswordConstraint(
 *   id = "password_policy_delay_constraint",
 *   title = @Translation("Password Delay"),
 *   description = @Translation("Provide restrictions delaying how long before password can be changed."),
 *   error_message = @Translation("You cannot change your password yet.")
 * )
 */
class PasswordDelay extends PasswordConstraintBase implements ContainerFactoryPluginInterface {

  /**
   * The password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $passwordService;

  /**
   * A database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $db;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('password'),
      \Drupal::database()
    );
  }

  /**
   * Accessor for the password service.
   *
   * @return \Drupal\Core\Password\PasswordInterface
   *   The password interface service.
   */
  public function getPasswordService() {
    return $this->passwordService;
  }

  /**
   * Accessor for the database connection.
   *
   * @return \Drupal\Core\Password\PasswordInterface
   *   The password interface service.
   */
  public function getDatabase() {
    return $this->db;
  }

  /**
   * Constructs a new PasswordDelay constraint.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Password\PasswordInterface $password_service
   *   The password service.
   * @param \Drupal\Core\Database\Connection $connection
   *   The Connection service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PasswordInterface $password_service, Connection $connection) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->passwordService = $password_service;
    $this->db = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'delay_number' => 24,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['delay_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of hours before password can be changed'),
      '#description' => $this->t('Minimum number of hours between password changes.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('delay_number');
    if (!is_numeric($value) || $value < 1) {
      $form_state->setErrorByName('delay_number', $this->t('The number hours between passwords changes must be greater than zero.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['delay_number'] = $form_state->getValue('delay_number');
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->t('Number of hours between password changes: @number-passwords', [
      '@number-passwords' => $this->configuration['delay_number'],
    ]);
  }

  protected function getLastChange($uid) {
    return $this->getDatabase()->select('password_policy_history', 'pph')
     ->fields('pph', ['timestamp'])
     ->condition('uid', $uid)
     ->range(0, 1)
     ->orderBy('timestamp', 'desc')
     ->execute()
     ->fetchField(0);
     //->fetchAll();
  }

  /**
   * {@inheritdoc}
   */
  public function validate($password, $user_context) {
    $validation = new PasswordPolicyValidation();

    if (empty($user_context['uid'])) {
      return $validation;
    }

    $last_change = $this->getLastChange($user_context['uid']);
    if ($last_change) {
      $configuration = $this->getConfiguration();
      if (REQUEST_TIME - ($configuration['delay_number'] * 60 * 60) < $last_change) {
        $validation->setErrorMessage($this->t('Password cannot be changed yet'));
      }
    }

    return $validation;
  }

}
