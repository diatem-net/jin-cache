<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Jin2\Cache;

/**
 * Interface pour les classes de gestion de cache
 */
interface CacheInterface
{

  public function __construct($params);

  public function isInCache($key);

  public function getFromCache($key);

  public function deleteFromCache($key);

  public function saveInCache($key, $value);

  public function clearCache();

}
