<?php

namespace Application;

return [
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'forbidden_template'       => 'error/403',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'layout/angular'          => __DIR__ . '/../view/layout/angular.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',

            'autowp/forums/frontend/index'        => __DIR__ . '/../view/forums/index.phtml',
            'autowp/forums/frontend/move-message' => __DIR__ . '/../view/forums/move-message.phtml',
            'autowp/forums/frontend/move'         => __DIR__ . '/../view/forums/move.phtml',
            'autowp/forums/frontend/new'          => __DIR__ . '/../view/forums/new.phtml',
            'autowp/forums/frontend/paginator'    => __DIR__ . '/../view/forums/paginator.phtml',
            'autowp/forums/frontend/subscribes'   => __DIR__ . '/../view/forums/subscribes.phtml',
            'autowp/forums/frontend/topic'        => __DIR__ . '/../view/forums/topic.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'pageTitle'         => View\Helper\PageTitle::class,
            'breadcrumbs'       => View\Helper\Breadcrumbs::class,
            'markdown'          => View\Helper\Markdown::class,
            'pastTimeIndicator' => View\Helper\PastTimeIndicator::class,
            'img'               => View\Helper\Img::class,
            'count'             => View\Helper\Count::class,
            View\Helper\FormElement::class => Form\View\Helper\FormElement::class,
            'form_element'                 => Form\View\Helper\FormElement::class,
            'formelement'                  => Form\View\Helper\FormElement::class,
            'formElement'                  => Form\View\Helper\FormElement::class,
            'FormElement'                  => Form\View\Helper\FormElement::class,
            'formpicturemulticheckbox'     => Form\View\Helper\FormPictureMultiCheckbox::class,
            'favicons'                     => View\Helper\Favicons::class,
        ],
        'factories' => [
            'car'            => View\Helper\Service\CarFactory::class,
            'comments'       => View\Helper\Service\CommentsFactory::class,
            'fileSize'       => View\Helper\Service\FileSizeFactory::class,
            'hostManager'    => View\Helper\Service\HostManagerFactory::class,
            'inlinePicture'  => View\Helper\Service\InlinePictureFactory::class,
            'language'       => View\Helper\Service\LanguageFactory::class,
            'languagePicker' => View\Helper\Service\LanguagePickerFactory::class,
            'mainMenu'       => View\Helper\Service\MainMenuFactory::class,
            'moderMenu'      => View\Helper\Service\ModerMenuFactory::class,
            'page'           => View\Helper\Service\PageFactory::class,
            'pageEnv'        => View\Helper\Service\PageEnvFactory::class,
            'pic'            => View\Helper\Service\PicFactory::class,
            'pictures'       => View\Helper\Service\PicturesFactory::class,
            'sidebar'        => View\Helper\Service\SidebarFactory::class,
            'userText'       => View\Helper\Service\UserTextFactory::class,
        ]
    ],
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];
