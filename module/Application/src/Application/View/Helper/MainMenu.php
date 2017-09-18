<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;
use Application\MainMenu as Model;

class MainMenu extends AbstractHtmlElement
{
    /**
     * @var Model
     */
    private $mainMenu;

    public function __construct(Model $mainMenu)
    {
        $this->mainMenu = $mainMenu;
    }

    public function __invoke(bool $data = false, bool $full = false)
    {
        $user = $full ? null : $this->view->user()->get();
        $menu = $this->mainMenu->getMenu($user ? $user : null, $full);

        if ($data) {
            return $menu;
        }

        return $this->view->partial('application/main-menu', $menu);
    }
}
