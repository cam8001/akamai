<?php

namespace Drupal\akamai;

/**
 * A CredentialsInterface.
 *
 * Akamai clients require a credentials interface to provide the four necessary
 * credentials to connect to their API:
 *  - REST API URL
 *  - Client Token
 *  - CLient Secret
 *  - Access Token.
 */
interface CredentialsInterface {

  /**
   * Gets the REST API URL.
   *
   * @return string
   *   The REST API URL.
   */
  public function getRestApiUrl();

  /**
   * Gets the client token.
   *
   * @return string
   *   The Client Token.
   */
  public function getClientToken();

  /**
   * Gets the client secret.
   *
   * @return string
   *   The Client Secret.
   */
  public function getClientSecret();

  /**
   * Gets the access token.
   *
   * @return string
   *   The Access Token.
   */
  public function getAccessToken();

  /**
   * Saves the REST API URL.
   *
   * @param string $rest_api_url
   *   The REST API URL.
   *
   * @return CredentialsInterface $this
   *   This object instance (implementing a fluent interface).
   */
  public function saveRestApiUrl($rest_api_url);

  /**
   * Saves the client token.
   *
   * @param string $client_token
   *   The client token.
   *
   * @return CredentialsInterface $this
   *   This object instance (implementing a fluent interface).
   */
  public function saveClientToken($client_token);

  /**
   * Saves the client secret.
   *
   * @param string $client_secret
   *   The client secret.
   *
   * @return CredentialsInterface $this
   *   This object instance (implementing a fluent interface).
   */
  public function saveClientSecret($client_secret);

  /**
   * Saves the access token.
   *
   * @param string $access_token
   *   The access token.
   *
   * @return CredentialsInterface $this
   *   This object instance (implementing a fluent interface).
   */
  public function saveAccessToken($access_token);

}
