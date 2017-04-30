<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Jin2\Cache;

/**
 * Gestion du cache via memCache.
 * Nécessite que le serveur soit configuré et fonctionne nominativement.
 */
class MemcacheCache implements CacheInterface
{

  /**
   * Serveur MemCache
   */
  private $memcache = null;

  /**
   * Constructeur
   *
   * @param  array $params  Doit contenir au moins les clés cache_host et cache_port
   * @throws \Exception
   */
  public function __construct($params)
  {
    if (!isset($params['cache_host'])) {
      throw new \Exception('MemcacheCache a besoin du paramètre cache_host pour être instancié.');
    } elseif (!isset($params['cache_port'])) {
      throw new \Exception('MemcacheCache a besoin du paramètre cache_port pour être instancié.');
    } else {
      $this->memcache = new \Memcache();
      $this->memcache->addServer($params['cache_host'], $params['cache_port']);
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
    $value = $this->memcache->get($this->buildMKey($key));
    if ($value) {
      return true;
    }
    return false;
  }

  /**
   * Permet de retourner une valeur du cache à partir de sa clé.
   *
   * @param  string $key  Clé à rechercher
   * @return mixed        Valeur trouvée ou null si aucune valeur n'est trouvée
   */
  public function getFromCache($key)
  {
    $value = $this->memcache->get($this->buildMKey($key));
    if ($value) {
      return unserialize($value);
    }
    return null;
  }

  /**
   * Supprime une valeur du cache
   *
   * @param  string $key  Clé à supprimer
   */
  public function deleteFromCache($key)
  {
    $this->memcache->delete($this->buildMKey($key));
  }

  /**
   * Sauvegarde une valeur dans le cache
   *
   * @param  string $key    Clé à sauvegarder
   * @param  mixed  $value  Valeur à sauvegarder
   * @return void
   */
  public function saveInCache($key, $value)
  {
    $this->deleteFromCache($key);
    $this->memcache->set($this->buildMKey($key), serialize($value));
  }

  /**
   *  Supprime tout le contenu du cache
   */
  public function clearCache()
  {
    $this->memcache->flush();
  }

  /**
   *  Retourne une clé MemCache unique à partir d'une clé standard
   *
   *  @param  string  $key  Clé à rendre unique
   *  @return string        Clé unique
   */
  private function buildMKey($key)
  {
    return sprintf('%s_%s', __FILE__, $key);
  }

}
