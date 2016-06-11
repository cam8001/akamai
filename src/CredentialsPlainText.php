<?php

namespace Drupal\akamai;

use Drupal\Core\Config\ConfigFactory;

/**
 * A plaintext credentials provider, used only for testing.
 */
class CredentialsPlainText implements CredentialsInterface {


  protected $config;

  /**
   * Constructs a CredentialsPlainText instance.
   *
   * @param ConfigFactory $configFactory
   *   A config factory, for getting Akamai settings.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->config = $configFactory->getEditable('akamai.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getRestApiUrl() {
    return $this->config->get('rest_api_url');
  }

  /**
   * {@inheritdoc}
   */
  public function getClientToken() {
    return $this->config->get('client_token');
  }

  /**
   * {@inheritdoc}
   */
  public function getClientSecret() {
    return $this->config->get('client_secret');
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessToken() {
    return $this->config->get('access_token');
  }

  /**
   * {@inheritdoc}
   */
  public function saveRestApiUrl($rest_api_url) {
    $this->config->set('rest_api_url', $rest_api_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveClientToken($client_token) {
    $this->config->set('client_token', $client_token);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveClientSecret($client_secret) {
    $this->config->set('client_secret', $client_secret);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveAccessToken($access_token) {
    $this->config->set('access_token', $access_token);
    return $this;
  }

}
