<?php

namespace Drupal\akamai;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\akamai\CredentialsFactory;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a plugin manager for Akamai credentials.
 *
 * Credentials must implement Drupal\akamai\CredentialsInterface.
 *
 * @see Drupal\akamai\CredentialsInterface
 */
class CredentialsPluginManager extends DefaultPluginManager {

  /**
   * A config object containing akamai.settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, ConfigFactory $configFactory) {
    parent::__construct(
      'Plugin/Akamai/Credentials',
      $namespaces,
      $module_handler,
      'Drupal\akamai\CredentialsInterface',
      'Drupal\akamai\Annotation\AkamaiCredentials'
    );
    $this->setCacheBackend($cache_backend, 'akamai_credentials');

    $this->factory = new CredentialsFactory($this->getDiscovery());
    $this->config = $configFactory->get('akamai.settings');
  }

  /**
   * Returns an instance of the currently enabled Credentials plugin.
   *
   * @return CredentialsInterface
   *   The Credentials provider.
   */
  public function getPlugin() {
    // Default to the encrypted plugin. Config doesn't seem to yet exist during
    // install which causes errors when enabling the akamai.edgegridclient
    // service.
    $active_plugin = $this->config->get('credentials_plugin') ?: 'encrypted';
    return $this->createInstance($active_plugin);
  }

}
