<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Menus;

class BaseController extends Controller
{
    /**
     * @var Menus
     */
    protected $menus;

    public function __construct(Menus $menus)
    {
        $this->menus = $menus;
    }
}
