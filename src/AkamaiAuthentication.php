<?php

namespace Drupal\akamai;

use Drupal\Core\Config\ConfigFactoryInterface;
use Akamai\Open\EdgeGrid\Authentication;
use Drupal\akamai\CredentialsInterface;

/**
 * Connects to the Akamai EdgeGrid.
 *
 * Akamai's PHP Client library expects an authentication object which it then
 * integrates with a Guzzle client to create signed requests. This class
 * integrates Drupal configuration with that Authentication class, so that
 * standard Drupal config patterns can be used.
 */
class AkamaiAuthentication extends Authentication {

  /**
   * AkamaiAuthentication factory method, following superclass patterns.
   *
   * @param ConfigFactoryInterface $config
   *   A config factory, for getting module config.
   * @param CredentialsInterface $credentials
   *   A set of credentials for authenticating with Akamai.
   *
   * @return \Drupal\akamai\AkamaiAuthentication
   *   An authentication object.
   */
  public static function create(ConfigFactoryInterface $config, CredentialsInterface $credentials) {
    // Following the pattern in the superclass.
    $auth = new static();

    $config = $config->get('akamai.settings');
    // @todo Maybe make the devel mode check a library function?
    if ($config->get('devel_mode') == TRUE) {
      $auth->setHost($config->get('mock_endpoint'));
    }
    else {
      $auth->setHost($credentials->getRestApiUrl());
      // Set the auth credentials up.
      // @see Authentication::createFromEdgeRcFile()
      $auth->setAuth(
        $credentials->getClientToken(),
        $credentials->getClientSecret(),
        $credentials->getAccessToken()
      );
    }

    return $auth;
  }

  /**
   * Returns the auth config.
   *
   * @return string[]
   *   An array with keys client_token, client_secret, access_token.
   */
  public function getAuth() {
    return $this->auth;
  }

}
