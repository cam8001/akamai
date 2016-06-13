<?php

namespace Drupal\akamai\Plugin\Akamai\Credentials;

use Exception;
use Drupal\akamai\Annotation\AkamaiCredentials;
use Drupal\akamai\CredentialsInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Config\ConfigFactory;

/**
 * @AkamaiCredentials(
 *   id = "encrypted"
 * )
 */
class Encrypted implements CredentialsInterface {

  /**
   * A string used for signing encryption.
   *
   * @var string
   */
  protected $key;

  /**
   * Encryption method to use with OpenSSL.
   *
   * AES-256-OFB is available on Mac OS and most Linuxes, and should be secure
   * enough for our obfuscation purposes.
   *
   * @var string
   */
  protected $encryptionMethod = 'AES-256-OFB';

  /**
   * The config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Contructs a CredentialsEncrypted object.
   *
   * - Uses the global hash_salt (stored in settings.php) as encryption key
   * - Uses a initialization vector [IV] stored in Config. If there is no
   *   IV stored, nothing has yet been encrypted, so create one and store it.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->config = $configFactory->getEditable('akamai.settings');
    $this->setKey();
    // Create an initialization vector if none exists.
    if ($this->getInitializationVector() === NULL) {
      $this->setInitializationVector();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRestApiUrl() {
    return $this->configDecrypt('rest_api_url');
  }

  /**
   * {@inheritdoc}
   */
  public function getClientToken() {
    return $this->configDecrypt('client_token');
  }

  /**
   * {@inheritdoc}
   */
  public function getClientSecret() {
    return $this->configDecrypt('client_secret');
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessToken() {
    return $this->configDecrypt('access_token');
  }

  /**
   * {@inheritdoc}
   */
  public function saveRestApiUrl($rest_api_url) {
    $this->configSaveEncrypted('rest_api_url', $rest_api_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveClientToken($client_token) {
    $this->configSaveEncrypted('client_token', $client_token);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveClientSecret($client_secret) {
    $this->configSaveEncrypted('client_secret', $client_secret);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveAccessToken($access_token) {
    $this->configSaveEncrypted('access_token', $access_token);
    return $this;
  }

  /**
   * Saves a value in configuration, encrypting it first.
   *
   * @param string $config_key
   *   The key that you want to save the value against.
   * @param string $value
   *   Value to save.
   */
  protected function configSaveEncrypted($config_key, $value) {
    $this->config
      ->set($config_key, $this->encrypt($value))
      ->save();
  }

  /**
   * Returns a decrypted value that is stored in config.
   *
   * @param string $key
   *   The decryption key.
   *
   * @return string
   *   The decrypted value.
   */
  protected function configDecrypt($key) {
    return $this->decrypt($this->config->get($key));
  }

  /**
   * Gets the encryption key.
   *
   * @return string
   *   The encryption key.
   */
  protected function getKey() {
    return $this->key;
  }

  /**
   * Sets the key to use as to encrypt the credentials.
   *
   * @param string $key
   *   Key to use for encryption.
   */
  protected function setKey($key = NULL) {
    if ($key === NULL) {
      $this->key = Settings::getHashSalt();
    }
  }

  /**
   * Generates and saves a secure initialization vector.
   *
   * @throws \Drupal\akamai\AkamaiEncryptionException
   */
  protected function setInitializationVector() {
    $cryptographically_strong = FALSE;
    $iv = openssl_random_pseudo_bytes(16, $cryptographically_strong);
    if ($cryptographically_strong) {
      // Convert from binary to hex so we can store as a string.
      $iv = bin2hex($iv);
      $this->config->set('encryption_iv', $iv)->save();
    }
    else {
      throw new AkamaiEncryptionException('OpenSSL is not using a cryptographically secure algorithm to generate Initialization Vectors. Consider upgrading your system openssl library.');
    }
  }

  /**
   * Gets a secure initialization vector from config.
   *
   * @return string|null
   *   The initialization vector, or NULL if none set.
   */
  protected function getInitializationVector() {
    return $this->config->get('encryption_iv');
  }

  /**
   * Encrypts a value.
   *
   * @param string $value
   *   Value to encrypt.
   *
   * @return string
   *   Encrypted value.
   */
  protected function encrypt($value) {
    $cipher_value = openssl_encrypt(
      $value,
      $this->encryptionMethod,
      $this->getKey(),
      OPENSSL_RAW_DATA,
      $this->getInitializationVector()
    );
    return $cipher_value;
  }

  /**
   * Decrypts a value.
   *
   * @param string $cipher_value
   *   Value to decrypt.
   *
   * @return string
   *   Decrypted value.
   */
  protected function decrypt($cipher_value) {
    $value = openssl_decrypt(
      $cipher_value,
      $this->encryptionMethod,
      $this->getKey(),
      OPENSSL_RAW_DATA,
      $this->getInitializationVector()
    );
    return $value;
  }

}

/**
 * A custom exception type for encryption issues.
 */
class AkamaiEncryptionException extends Exception {}
