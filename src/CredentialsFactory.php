<?php

namespace Drupal\akamai;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * A CredentialsFactory.
 */
class CredentialsFactory extends DefaultFactory {

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    $plugin_definition = $this->discovery->getDefinition($plugin_id);
    $plugin_class = static::getPluginClass($plugin_id, $plugin_definition, $this->interface);
    return new $plugin_class(\Drupal::configFactory());
  }

}
