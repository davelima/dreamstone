<?php
namespace AppBundle\Utils;

use Symfony\Component\DependencyInjection\Container;

class UserRoles extends Container
{
    const ROLE_SUPER_ADMIN = 'Administrador geral';

    const ROLE_ADMIN = 'Administrador';

    const ROLE_AUTHOR = 'Autor';

    const ROLE_REVIEWER = 'Revisor';

}
