<?php

namespace Drupal\akamai;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * A CredentialsFactory.
 */
class CredentialsFactory {

  /**
   * A configuration object, holding akamai.settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * A config factory (for loading config).
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a CredentialsFactory object.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
    $this->config = $configFactory->get('akamai.settings');
  }

  /**
   * Returns a CredentialsInterface, which depends on config.
   */
  public function getCredentials() {
    $config_storage = $this->config->get('credentials_storage');

    switch ($config_storage) {
      // If credentials are stored in settings.php, use plain text.
      case 'settings':
        return new CredentialsPlainText($this->configFactory);

      break;
      // Otherwise, use encrypted credentials.
      default:
        return new CredentialsEncrypted($this->configFactory);
    }
  }

}
