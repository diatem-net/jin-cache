<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Jin2\Cache;

/**
 * Gestion du cache via le système de fichiers
 */
class FileCache implements CacheInterface
{

  /**
   * Dossier où stocker les fichers en cache
   *
   * @var string
   */
  private $cachePath;

  /**
   * Constructeur
   *
   * @param  array $params  Peut contenir la clé cache_path pour choisir le dossier de stockage du cache
   * @throws \Exception
   */
  public function __construct($params)
  {
    if (!isset($params['cache_path'])) {
      $params['cache_path'] = __DIR__
        . DIRECTORY_SEPARATOR .'..'
        . DIRECTORY_SEPARATOR .'..'
        . DIRECTORY_SEPARATOR .'cache';
    }
    if (file_exists($params['cache_path']) && is_dir($params['cache_path'])) {
      $this->cachePath = rtrim($params['cache_path'], '/') . '/';
    } else {
      throw new \Exception('Le paramètre cache_path transmis n\'est pas un dossier : '.$params['cache_path']);
    }
  }

  /**
   * Permet de savoir si une clé donnée est définie dans le cache
   *
   * @param  string $key  Clé à rechercher
   * @return boolean      TRUE si définie dans le cache
   */
  public function isInCache($key)
  {
    $file = $this->cachePath . $this->getEncodedKey($key);
    return file_exists($file);
  }

  /**
   * Permet de retourner une valeur du cache à partir de sa clé.
   *
   * @param  string $key  Clé à rechercher
   * @return mixed   Valeur trouvée ou null si aucune valeur n'est trouvée
   */
  public function getFromCache($key)
  {
    $file = $this->cachePath . $this->getEncodedKey($key);
    if (file_exists($file)) {
      return unserialize(file_get_contents($file));
    }
    return null;
  }

  /**
   * Supprime une valeur du cache
   *
   * @param  String $key  Clé à supprimer
   */
  public function deleteFromCache($key)
  {
    $file = $this->cachePath . $this->getEncodedKey($key);
    if (file_exists($file)) {
      unlink($file);
    }
  }

  /**
   * Sauvegarde une valeur dans le cache
   *
   * @param string $key  Clé à sauvegarder
   * @param mixed $value Valeur à sauvegarder
   */
  public function saveInCache($key, $value)
  {
    $this->deleteFromCache($key);
    $file = $this->cachePath . $this->getEncodedKey($key);
    file_put_contents($file, serialize($value), LOCK_EX);
  }

  /**
   * Supprime tout le contenu du cache
   */
  public function clearCache()
  {
    $files = glob($this->cachePath);
    foreach($files as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }
  }

  /**
   * Retourne une clé encodée à partir d'une clé en clair
   *
   * @param  string  $key  Clé à encoder
   * @return string        Clé encodée
   */
  private function getEncodedKey($key)
  {
    return hash('md5', $key);
  }

}
