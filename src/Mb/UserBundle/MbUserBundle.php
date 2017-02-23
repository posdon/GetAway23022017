<?php

namespace Mb\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MbUserBundle extends Bundle
{
  public function getParent()
    {
      return 'FOSUserBundle';
    }
}
