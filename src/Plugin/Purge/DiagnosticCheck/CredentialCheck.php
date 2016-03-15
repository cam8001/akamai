<?php

/**
 * @file
 * Contains Drupal\akamai\Plugin\Purge\DiagnosticCheck.
 */

namespace Drupal\akamai\Plugin\Purge\DiagnosticCheck;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\purge\Plugin\Purge\DiagnosticCheck\DiagnosticCheckBase;
use Drupal\purge\Plugin\Purge\DiagnosticCheck\DiagnosticCheckInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks if valid Api credentials have been entered for CloudFlare.
 *
 * @PurgeDiagnosticCheck(
 *   id = "akamai_creds",
 *   title = @Translation("Akamai - Credentials"),
 *   description = @Translation("Checks to see if the credentials for Akamai are valid."),
 *   dependent_queue_plugins = {},
 *   dependent_purger_plugins = {"akamai"}
 * )
 */
class CredentialCheck extends DiagnosticCheckBase implements DiagnosticCheckInterface {
  /**
   * The settings configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructs a Akamai CredentialCheck object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config->get('akamai.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run() {

    if ($this->config->get('devel_mode') == TRUE) {
      $this->recommendation = $this->t('Running in development mode, no credentials required.');
      return SELF::SEVERITY_OK;
    }

    // @todo Getting this globally feels gross. Is there a better way?
    if (\Drupal::state()->get('akamai.valid_credentials') == FALSE) {
      $this->recommendation = $this->t("Invalid API credentials.");
      return SELF::SEVERITY_ERROR;
    }

    $this->recommendation = $this->t('Valid API credentials detected.');
    return SELF::SEVERITY_OK;
  }

}
