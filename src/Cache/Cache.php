<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Jin2\Cache;

/**
 * Classe principale de gestion du cache.
 * Se charge d'initialiser les classes spécifiques de gestion du cache en fonction de la configuration de l'environnement.
 */
class Cache
{

  const CACHE_MODE_FILE = 'file';
  const CACHE_MODE_MEMCACHE = 'memcache';

  /**
   * Classe de gestion du cache.
   * @var CacheInterface
   */
  protected static $cm = null;

  /**
   * Définit le type de cache à utiliser
   *
   * @param  string $type  Type de cache à utiliser
   * @throws \Exception
   */
  public static function setCacheType($type, $params = array())
  {
    if (self::$cm !== null) {
      throw new \Exception('Le type de cache ne peut pas être changé une fois le cache initialisé.');
    } else {
      if ($type == self::CACHE_MODE_MEMCACHE) {
        self::$cm = new MemcacheCache($params);
      } elseif ($type == self::CACHE_MODE_FILE) {
        self::$cm = new FileCache($params);
      } else {
        throw new \Exception('Le type de cache "' . $type . '" n\'est pas supporté.');
      }
    }
  }

  /**
   * Vérifie que le cache est bien initialisé.
   * Dans le cas contraire, un FileCache est utilisé par défaut
   */
  public static function initialize()
  {
    if (self::$cm === null) {
      static::setCacheType(self::CACHE_MODE_FILE);
    }
  }

  /**
   * Permet de savoir si une clé donnée est définie dans le cache
   *
   * @param  string $key  Clé à rechercher
   * @return boolean      TRUE si définie dans le cache
   */
  public static function isInCache($key)
  {
    static::initialize();
    return self::$cm->isInCache($key);
  }

  /**
   * Permet de retourner une valeur du cache à partir de sa clé.
   *
   * @param  string $key  Clé à rechercher
   * @return mixed        Valeur trouvée ou NULL si aucune valeur n'est trouvée
   */
  public static function getFromCache($key)
  {
    static::initialize();
    $v = self::$cm->getFromCache($key);
    return $v ? $v['value'] : null;
  }

  /**
   * Permet de retourner la valeur/date du cache à partir de sa clé.
   *
   * @param  string $key  Clé à rechercher
   * @return array        Valeur trouvée ou NULL si aucune valeur n'est trouvée. (array('time' => t, 'value' => v))
   */
  public static function getFromCacheWithDate($key)
  {
    static::initialize();
    return self::$cm->getFromCache($key);
  }

  /**
   *  Supprime une valeur du cache
   *
   *  @param  string $key  Clé à supprimer
   */
  public static function deleteFromCache($key)
  {
    static::initialize();
    self::$cm->deleteFromCache($key);
  }

  /**
   *  Sauvegarde une valeur dans le cache
   *
   *  @param  string $key    Clé à sauvegarder
   *  @param  mixed  $value  Valeur à sauvegarder
   */
  public static function saveInCache($key, $value)
  {
    static::initialize();
    self::$cm->saveInCache($key, array('time' => time(), 'value' => $value));
  }

  /**
   * Supprime tout le contenu du cache
   */
  public static function clearCache()
  {
    static::initialize();
    self::$cm->clearCache();
  }

}