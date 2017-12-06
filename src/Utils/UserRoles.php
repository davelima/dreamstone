<?php
namespace App\Utils;

use Symfony\Component\DependencyInjection\Container;

class UserRoles extends Container
{
    const ROLE_SUPER_ADMIN = 'role_super_administrator';

    const ROLE_ADMIN = 'role_administrator';

    const ROLE_AUTHOR = 'role_author';

    const ROLE_REVIEWER = 'role_reviewer';

}
