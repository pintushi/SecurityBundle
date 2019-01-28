<?php

namespace Pintushi\Bundle\SecurityBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager as BaseJWTManager;

class JWTManager extends BaseJWTManager
{
    public function create(UserInterface $user, $payload)
    {

    }
}
