<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 1/14/15
 * Time: 6:00 PM
 */

namespace rest\seguridad\autenticacion\oauth2;
use Doctrine\Common\Cache\Cache;

abstract class oauth_token_decoder
{
    /**
     * @var  Cache
     */
    protected $cache;

    public function set_cache_manager(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $token un token oauth
     * @return array asociativo con la informaci�n correspondiente a el token
     */
    abstract public function decode($token);
} 