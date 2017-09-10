<?php

return array_replace(include 'root.php', [
    '' => [
        'plural_forms' => 'nplurals=2; plural=((n==0)||(n==1))?0:1'
    ],

    'test' => 'test est ok',

    /* Common words */
    'and' => 'et',
    '%1$s pictures'       => [
        '%1$s image',
        '%1$s images',
    ],
    'ng/%1$s-pictures' => '{count, plural,
        one {{count} image}
        other {{count} images}
    }',
    '%1$s items'      => [
        '%1$s item',
        '%1$s items',
    ],
    '%1$s vehicles'      => [
        '%1$s véhicule',
        '%1$s véhicules',
    ],
    '%1$s comments'      => [
        '%1$s commentaire',
        '%1$s commentaires',
    ],
    'ng/%1$s-comments' => '{count, plural,
        one {{count} comment}
        other {{count} comments}
    }',
    '%1$s messages'      => [
        '%1$s message',
        '%1$s messages',
    ],
    '%1$s topics'      => [
        '%1$s sujet',
        '%1$s sujets',
    ],

    'year' => 'Year',
    'month' => 'Month',

    'contributor' => 'contributeur',
    'contributors' => 'contributeurs',

    'deleted-user' => 'utilisateur supprimé',

    'submit' => 'Envoyer',

    /* Layout */
    'layout/meta/description' => 'Encyclopédie de voitures en images. WheelsAge.org',
    'layout/meta/keywords' => 'car, vehicle, auto, avto, voiture',
    'layout/brand' => 'WheelsAge.org',
    'layout/personal-messages' => 'Messages personnels',
    'layout/you-have-%1$s-new-messages' => [
        'Vous avez %1$s nouveau message',
        'Vous avez %1$s nouveaux messages',
    ],
    'ng-layout/you-have-new-messages' => '{messages, plural,
        one {Vous avez {messages} nouveau message}
        other {Vous avez {messages} nouveaux messages}
    }',

    'layout/search' => 'Rechercher',
    'layout/footer' => 'S\'il vous plaît noter que tous les documents sur ce autowp.ru, sont ajouté par  les visiteurs.

L\'administration du site ne dispose pas d\'informations de la légalité de la publication de ces matériaux.

Toutes les images sont disponibles à des fins éducatives.

[Signaler une violation de copyright](mailto:autowp@yandex.ru)',

    'layout/language-contributing' => 'Vous souhaitez contribuer?',

    /* Picture preview */
    'picture-preview/no-comments' => 'non',
    'picture-preview/no-ratio'    => 'non',
    'picture-preview/crop-desc'   => "L'image est recadrée",
    'picture-preview/special-name' => 'Image avec nom spécial',
    'Resolution'                  => 'Résolution',
    'Filesize'                    => 'Taille du fichier',
    'Ratio'                       => 'Ratio',
    'Views'                       => 'Vues',
    'Comments count'              => 'Commentaires',

    /* SPECIFICATIONS*/
    'rpm' => 'tr/min',
    'Nm'  => 'Nm',
    'kW'  => 'kW',
    'hp'  => 'cv',
    'spec/%1$s-gear' => [
        '%1$s vitesse',
        '%1$s vitesses'
    ],

    /* PAGINATOR */
    'next'                     => 'prochaine',
    'previous'                 => 'précédent',

    /* INDEX */
    'index/brands'             => 'Marques',
    'index/factories'          => 'Usines',
    'index/specifications'     => 'Specifications',
    'index/twins/description' => 'Savez-vous, que la Daewoo Matiz a [six clones](/twins/group118812)? Savez-vous, ce qui est commun entre [Lotus и Kia](/twins/group118750)? Sûr que vous vous sentez intéressant cette section.',
    'index/categories/description' => "Savez-vous quelle technique a permis d'observer la loi et l'ordre dans les rues de villes du monde à des moments différents? Et pas de voitures voyagé Pape? Non? Ensuite, vous êtes exactement dans cette section.",
    'index/categories/mosts' => "Voulez-vous savoir quel genre de voiture est le plus rapide? Voulez-vous savoir combien pèse le plus puissant camion de l'exploitation minière",

    'mosts/fastest/roadster'          => 'La plupart des roadsters les plus rapides',
    'mosts/mighty/sedan/today'        => 'La plupart puissantes berlines aujourd\'hui',
    'mosts/dynamic/universal/2000-09' => 'La plupart des universaux dynamiques années 2000',
    'mosts/heavy/truck'               => 'La plupart des camions lourds',

    'back'                     => 'arrière',
    'forward'                  => 'vers l\'avant',

    'Picture of the day'       => 'Image du jour',
    'car-of-day'               => 'voiture de la journée',
    'theme-of-day'             => 'theme of the day',
    'day ahead'                => 'journée à l\'avance',
    'day ago'                  => 'il y a jour',
    'Cars by destination'      => 'Voitures par destination',
    'All new pictures'         => 'toutes les nouvelles images',

    'all-link'                 => 'tous',

    'brands/new-cars-of-brand' => 'Nouveaux modèles %s',

    'brands/more-companies'    => [
        'plus d\'%s marque',
        'plus de %s marques'
    ],
    'brands/pictures:' => 'pictures: ',

    'item/type/1/name' => 'Voiture',
    'item/type/1/name-plural' => 'Voitures',
    'item/type/1/new-item' => 'New voiture',
    'item/type/1/add-sub-item' => 'Add sub-voiture',
    'item/type/2/name' => 'Moteur',
    'item/type/2/name-plural' => 'Moteurs',
    'item/type/2/new-item' => 'New moteur',
    'item/type/2/add-sub-item' => 'Add sub-moteur',
    'item/type/3/name' => 'Category',
    'item/type/3/name-plural' => 'Categories',
    'item/type/3/new-item' => 'New category',
    'item/type/3/add-sub-item' => 'Add sub-category',
    'item/type/4/name' => 'Twins',
    'item/type/4/name-plural' => 'Twins',
    'item/type/4/new-item' => 'New twins group',
    'item/type/4/add-sub-item' => 'Add sub-twins',
    'item/type/5/name' => 'Brand',
    'item/type/5/name-plural' => 'Brands',
    'item/type/5/new-item' => 'New brand',
    'item/type/5/add-sub-item' => 'Add sub-brand',
    'item/type/6/name' => 'Factory',
    'item/type/6/name-plural' => 'Factories',
    'item/type/6/new-item' => 'New factory',
    'item/type/6/add-sub-item' => 'Add sub-factory',
    'item/type/7/name' => 'Museum',
    'item/type/7/name-plural' => 'Museums',
    'item/type/7/new-item' => 'New museum',
    'item/type/7/add-sub-item' => 'Add sub-museum',

    'item/type/8/name' => 'Person',
    'item/type/8/name-plural' => 'Persons',
    'item/type/8/new-item' => 'New person',
    'item/type/8/add-sub-item"' => 'Add sub-person',

    /* BRAND PAGE*/
    'unsorted'                => 'Non triés',
    'engines'                 => 'Moteurs',
    'concepts and prototypes' => 'Concepts et prototypes',
    'logotypes'               => 'Logotypes',
    'mixed'                   => 'Varié',

    'count 0'                 => 'non',

    /* CAR LIST */
    'carlist/no-photo'        => 'No photos available',
    'carlist/no-photo/add'    => 'Add photo',
    'carlist/all pictures'    => 'toutes les images',
    'carlist/details'         => 'détails',
    'carlist/twins'           => 'des jumeaux',
    'carlist/produced/one'    => 'Produit en un seul exemplaire',
    'carlist/produced/%1$s'   => [
        'Produit en %1$s exemplaire',
        'Produit à %1$s exemplaires'
    ],
    'carlist/produced-about/%1$s' => [
        'Produit dans environ %1$s exemplaire',
        'Produit dans environ %1$s exemplaires'
    ],
    'carlist/specifications'  => 'caractéristiques',
    'carlist/designed by %s'  => 'dessinée par %s',
    'carlist/edit-specs' => 'modifier spécifications',
    'carlist/years' => 'years of production',
    'carlist/model-years' => 'model years',

    'picturelist/engine' => 'Moteur',
    'picturelist/engine-%s' => '%s moteur',
    'picturelist/factory' => 'Usine',

    'present-time-abbr'       => 'pr.',

    /* Categories */
    'category/%s cars'        => [
        '%s voiture',
        '%s voitures'
    ],
    'category/%s new cars'    => [
        '%s nouvelle',
        '%s nouvelles'
    ],

    /* most */
    'most/fastest' => 'le plus rapide',
    'most/fastest/param' => 'vitesse maximal',
    'most/slowest' => 'le plus lent',
    'most/slowest/param' => 'vitesse maximal',
    'most/dynamic' => 'dynamique',
    'most/dynamic/param' => 'accélération',
    'most/static' => 'statique',
    'most/static/param' => 'accélération',
    'most/mighty' => 'puissant',
    'most/mighty/param' => 'la puissance du moteur',
    'most/weak' => 'faible',
    'most/weak/param' => 'la puissance du moteur',
    'most/big-engine' => 'gros moteur',
    'most/big-engine/param' => 'volume de moteur',
    'most/small-engine' => 'petit moteur',
    'most/small-engine/param' => 'volume de moteur',
    'most/nimblest' => 'agile',
    'most/nimblest/param' => 'braquage',
    'most/economical' => 'économique',
    'most/economical/param' => 'la consommation de carburant dans le cycle combiné',
    'most/gluttonous' => 'glouton',
    'most/gluttonous/param' => 'la consommation de carburant dans le cycle combiné',
    'most/clenaly' => 'clenaly écologique',
    'most/clenaly/param' => 'émission',
    'most/dirty' => 'sale écologique',
    'most/dirty/param' => 'émission',
    'most/heavy' => 'lourd',
    'most/heavy/param' => 'poids équipée',
    'most/lightest' => 'plus léger',
    'most/lightest/param' => 'poids équipée',
    'most/longest' => 'la plus longue',
    'most/longest/param' => 'longueur',
    'most/shortest' => 'le plus court',
    'most/shortest/param' => 'longueur',
    'most/widest' => 'plus large',
    'most/widest/param' => 'largeur',
    'most/narrow' => 'étroit',
    'most/narrow/param' => 'largeur',
    'most/highest' => 'le plus haut',
    'most/highest/param' => 'hauteur',
    'most/lowest' => 'le plus bas',
    'most/lowest/param' => 'hauteur',
    'most/air' => 'air',
    'most/air/param' => 'coefficients de traînée',
    'most/antiair' => 'anti air',
    'most/antiair/param' => 'coefficients de traînée',
    'most/bigwheel' => 'grande roue',
    'most/bigwheel/param' => 'taille de la roue',
    'most/smallwheel' => 'petite roue',
    'most/smallwheel/param' => 'taille de la roue',
    'most/bigbrakes' => 'gros freins',
    'most/bigbrakes/param' => 'freins taille',
    'most/smallbrakes' => 'petits freins',
    'most/smallbrakes/param' => 'freins taille',
    'most/bigclearance' => 'grande garde au sol',
    'most/bigclearance/param' => 'garde au sol',
    'most/smallclearance' => 'petite garde au sol',
    'most/smallclearance/param' => 'garde au sol',

    'mosts/sidebar/mostly…' => 'Le plus …',
    'mosts/sidebar/between…' => 'Parmi …',

    'mosts/period/before1920' => 'jusqu\'à 1920',
    'mosts/period/1920-29'    => '1920-29',
    'mosts/period/1930-39'    => '1930-39',
    'mosts/period/1940-49'    => '1940-49',
    'mosts/period/1950-59'    => '1950-59',
    'mosts/period/1960-69'    => '1960-69',
    'mosts/period/1970-79'    => '1970-79',
    'mosts/period/1980-89'    => '1980-89',
    'mosts/period/1990-99'    => '1990-99',
    'mosts/period/2000-09'    => '2000-09',
    'mosts/period/2010-16'    => '2010-16',
    'mosts/period/present'    => 'au présent',
    'mosts/period/all-time'   => 'dans l\'histoire',

    /* upload */
    'upload/image-file' => "Fichier d'image",
    'upload/note' => 'Note',
    'upload/picture/crop' => 'Recadrer',
    'upload/picture/cropped-to' => 'Recadrée à %s',
    'upload/select/unsorted' => 'Non triée',
    'upload/select/unsorted-long' => 'Non triée (inexistant de véhicule ou de moteur)',
    'upload/select/mixed' => 'Mixte',
    'upload/select/mixed-long' => 'Mixte (peu de véhicules sur une seule image)',
    'upload/select/logo' => 'Logotype',
    'upload/select/engines' => 'Moteurs',
    'upload/select/concepts' => 'Concepts et prototypes',
    'upload/select/other-modification' => 'Autre modification',

    'upload/add-picture' => 'Ajouter une image',
    'upload/select-another' => 'Sélectionnez un autre catalogue',
    'upload/description' => "Si vous avez une image que vous ne trouvez pas sur ce site et elle correspondes au thème du site, vous pouvez les ajouter dans notre catalogue en utilisant le formulaire ci-dessous.

Ce serait formidable si vous aviez indiqué qu'une voiture est représentée sur la photo, pour faciliter son ajout et éviter les erreurs.

Exigences pour les images ajoutées:

* Le format [JPEG](http://ru.wikipedia.org/wiki/JPEG) ou [PNG](http://ru.wikipedia.org/wiki/PNG). Résolution: 640×360 jusqu'à 4096×4096.
* L'image doit être d'excellente qualité, ne contient pas les différents artefacts redimensionnement ou compression (flou, la couleur propagation, nervures droites etc.). Les exceptions sont rares photos (fabricant), scans de brochures. La décision d'abandonner cette photo sur le site a fait l'équipe de modérateur.
* L'absence d'inscriptions et les logos des sites étrangers, ne causé aucun auteurs/propriétaires des images.
* La voiture doit avoir un premier regard à la sortie de l'usine. Il ne doit pas présenter des signes de <em>tuning maison</em>, de préférence l'absence de changements liés à la voiture de vieillissement.
* La voiture doit être placé complètement dans l'image. L'exception est lorsque l'image montre l'intérieur de la voiture, le moteur, le coffre, ou une caractéristique distinctive (par exemple, le titre de la voiture de série limitée).
* Le principal et l'unique thème de l'image doit être la voiture, le logo d'une marque ou d'un moteur. La présence de corps étrangèrs ou de personnes dans le cadre est indésirable.
* Photos amateur de votre voiture, de la voiture de vos amis, ou tout autre, vous rencontrent dans la rue, ainsi que des photos des expositions ne seront pas acceptées sur le site, sauf si vous êtes un professionnel.
* Pour les premières photos de la voiture sont possibles des exigences.

**S'il vous plaît**, avant d'ajouter des images, assurez-vous qu'ils ne sont pas dans le catalogue.

S'il vous plaît noter que l'ajout d'images modérés. c'est-à-dire toutes les photos avant d'ajouter au catalogue seront testés par moderateurs.",
    'upload/only-registered' => 'Seuls les utilisateurs enregistrés peuvent télécharger des images

[Login](/login) or [Enregistrer](/ng/signup)',

    /* catalogue */
    'catalogue/other-pictures-of-%1$s' => 'Autres photos de %1$s',
    'catalogue/all-pictures-of-%1$s' => 'Toutes les images de %1$s',
    'catalogue/brand/logo-of-%s' => '%s logotype',
    'catalogue/brand/links' => 'Références',
    'catalogue/brand/popular-images' => 'Images populaires',
    'catalogue/brand/new-pictures' => 'Nouvelles photos',
    'catalogue/brand/inbox/by-brand' => 'Brand inbox',
    'catalogue/brand/factories' => 'Les usines',
    'catalogue/link-category/official' => 'Les sites officiels',
    'catalogue/link-category/helper'   => 'Aider à créer le projet',
    'catalogue/link-category/club'     => 'Clubs de voitures',
    'catalogue/link-category/default'  => 'Autres',
    'catalogue/chronological' => 'Chronologique',
    'catalogue/related' => 'Associé',
    'catalogue/related-with-%1$s' => 'Associé à %1$s',
    'catalogue/sport' => 'Sport',
    'catalogue/design' => 'Design',
    'catalogue/stock-model' => 'Stock model',
    'catalogue/sub-model' => 'Submodel',
    'catalogue/sport-of-%1$s' => '%1$s Sport',
    'catalogue/specifications-of-%1$s' => 'Caractéristiques de %1$s',
    'catalogue/specifications' => 'Caractéristiques',
    'catalogue/other-photos' => 'Plus d\'images',
    'catalogue/section/moto' => 'Moto',
    'catalogue/section/tractors' => 'Tractors',
    'catalogue/section/buses' => 'Buses',
    'catalogue/section/trucks' => 'Trucks',
    'catalogue/section/engines' => 'Les engines',

    'catalogue/engine-menu/vehicles' => 'Vehicles',
    'catalogue/engine-menu/pictures' => 'Pictures',
    'catalogue/specifications/contributors:' => 'Contributors:',
    'catalogue/specifications/disclaimer' => "Les informations recueillies par les membres du site. Si vous trouvez une erreur ou que vous souhaitez combler les lacunes, vous pouvez le faire, en passant de la procédure d'inscription sur le site.",
    'catalogue/vehicle/inbox' => 'inbox',
    'catalogue/vehicle/comments' => 'comments',
    'catalogue/engine/add-engine' => 'add engine',
    'catalogue/engine/mounted-on:' => 'Mounted on:',

    'factories/factory-items' => 'Produits',
    'factories/factory/pictures' => 'Images',

    /* comments */
    'comments/title' => 'Commentaires',
    'comments/reply' => 'répondre',
    'comments/what-to-do-for-write-comments' => 'Pour pouvoir poster un commentaire, vous devez <a href="/login">créer un compte ou de vous connecter en utilisant le compte des réseaux sociaux</a>',
    'comments/it-requires-attention-of-moderators' => 'Il nécessite une attention des modérateurs',
    'comments/form-title' => 'Ajouter un commentaire',
    'comments/message' => 'Message',
    'comments/send' => 'Envoyer',
    'comments/cancel' => 'Annuler',
    'comments/need-wait-text' => 'Malheureusement, il faut attendre un peu pour obtenir la possibilité de laisser un commentaire.

Cela est dû à la présence d\'un seuil "messages par heure" nécessaire pour lutter contre le spam et autres moments négatifs.

Le commentaire suivant, vous pourrez laisser "%s", et alors que vous avez le temps de clarifier l\'idée que vous voulez transmettre au public:',

    'comments/author/anonymous' => 'Anonyme',
    'comments/message-deleted-by-admin' => "Le message a été supprimé par l'administration du site",
    'comments/message/delete' => 'Supprimer',
    'comments/message/restore' => 'Restaurer',
    'comments/message/move' => 'Déplacer',
    'comments/vote/no-more-votes' => 'La limite de vote quotidienne est atteint.',
    'comments/subscribe' => "S'abonner à des notifications de nouvelles réponses",
    'comments/unsubscribe' => 'Se désabonner à des notifications',

    /*perspectives*/
    'perspective/front'          => 'avant',
    'perspective/back'           => 'derrière',
    'perspective/left'           => 'gauche',
    'perspective/right'          => 'droit',
    'perspective/interior'       => 'intérieur',
    'perspective/front-panel'    => 'panneau avant',
    'perspective/3/4-left'       => '¾ gauche',
    'perspective/3/4-right'      => '¾ droit',
    'perspective/cutaway'        => 'cutaway',
    'perspective/front-strict'   => 'avant (strictement)',
    'perspective/left-strict'    => 'gauche (strictement)',
    'perspective/right-strict'   => 'droit (strictement)',
    'perspective/back-strict'    => 'derrière (strictement)',
    'perspective/n/a'            => 'n/a',
    'perspective/label'          => 'label',
    'perspective/upper'          => 'haut',
    'perspective/under-the-hood' => 'sous la capuche',
    'perspective/upper-strict'   => 'haut (strictement)',
    'perspective/bottom'         => 'bas',
    'perspective/dashboard'      => 'tableau de bord',
    'perspective/boot'           => 'coffre',
    'perspective/logo'           => 'logo',
    'perspective/mascot'         => 'mascot',
    'perspective/sketch'         => 'sketch',
    'perspective/mixed'          => 'mixed',
    'perspective/exterior-details' => 'exterior details',

    'login/sign-in' => 'Entrée',
    'login/sign-out' => 'Sortie',
    'login/login-or-email' => 'login ou e-mail',
    'login/captcha' => 'entrez le code de sécurité',
    'login/registration' => 'Enregistrement',
    'login/forgot-password?' => 'Récupérer mot de passe',
    'login/success-text' => 'Nous sommes heureux de vous revoir sur notre site',
    'login/remember' => 'rappeller',
    'login/if-you-lose-password' => 'Si vous avez oublié votre login ou mot de passe, vous pouvez utiliser <a href="/restorepassword">le formulaire de récupération par e-mail</a>',
    'login/if-you-not-registered' => 'Si vous n\'êtes pas enregistré sur le site, vous <a href="/ng/signup">pouvez le faire maintenant</a>!',
    'login/sign-in-using-account' => 'Connectez-vous en utilisant un compte existant',
    'login/login-or-password-is-incorrect' => 'Login ou mot de passe est incorrect',
    'login/user-%value%-not-found' => 'Utilisateur avec login ou e-mail "%value%" non trouvé',

    'registration/if-you-registered' => 'Si vous êtes déjà enregistré, vous ne devriez pas le faire à nouveau, mais seulement besoin de saisir votre login et mot de passe dans le formulaire à droite.',

    'account/personal-message/reply' => 'répondre',
    'account/personal-message/delete' => 'effacer',
    'account/personal-message/entire-dialog' => 'dialogue entière',
    'account/personal-message/system-notification' => 'Système notification',
    'account/personal-message/delete-all-sent' => 'Supprimer tous les messages envoyés',
    'account/personal-message/delete-all-system' => 'Supprimer toutes les notifications système',
    'account/personal-message/new' => 'new',
    'account/personal-message/sent' => 'Message sent',
    'account/profile/personal' => 'Les données personnelles',
    'account/profile/photo' => 'Photo',
    'account/profile/photo/saved' => 'Photo saved',
    'account/profile/photo/deleted' => 'Photo deleted',
    'account/profile/use-gravatar' => 'Vous pouvez télécharger votre photo ou utiliser le service <a href="http://gravatar.com/">Gravatar</a>',
    'account/profile/photo/delete' => 'Supprimer la photo',
    'account/profile/other' => 'Autre',
    'account/profile/votes-per-day' => 'Votes par jour',
    'account/profile/votes-left' => 'Votes gauche',
    'account/profile/timezone' => 'Fuseau horaire',
    'account/profile/language' => 'Langue',
    'account/profile/saved' => 'Data saved',

    'account/access/change-password' => 'Changer le mot de passe',
    'account/access/change-password/current' => 'Actuel',
    'account/access/change-password/new' => 'Nouveau',
    'account/access/change-password/new-confirm' => 'Nouveau (confirmer)',
    'account/access/change-password/current-password-is-incorrect' => 'Le mot de passe est incorrect',
    'account/access/change-password/saved' => 'Le mot de passe a été changé',
    'account/access/self-delete' => 'Supprimer le compte',
    'account/access/self-delete/password-is-incorrect' => 'Le mot de passe est incorrect',

    'account/specs/weight' => "L'influence",

    'account/specs/conflicts/filter/all' => 'Toutes',
    'account/specs/conflicts/filter/i-disagree' => "je ne suis pas d'accord",
    'account/specs/conflicts/filter/do-not-agree-with-me' => "Ne pas d'accord avec moi",
    'account/specs/conflicts/filter/errors' => 'Les erreurs',

    'account/specs/conflicts/title/object' => "L'objet",
    'account/specs/conflicts/title/object/engine' => 'le moteur',
    'account/specs/conflicts/title/parameter' => 'Le paramètre',
    'account/specs/conflicts/title/my-value' => 'Ma valeur',
    'account/specs/conflicts/title/other-values' => "D'autres valeurs",

    'account/specs/conflicts/my-value/none' => 'non',

    'account/email/your-current-email:' => 'Votre adresse e-mail actuelle: ',
    'account/email/your-dont-enter-email' => "Vous n'avez pas entré une adresse e-mail",
    'account/email/text' => 'Sur cette page vous pouvez changer votre adresse e-mail.

Message avec des instructions enverra à la nouvelle adresse e-mail pour le confirmer.',

    'account/accounts/add' => 'Ajouter un compte',
    'account/accounts/remove' => 'Supprimer',
    'account/accounts/removed' => 'Compte supprimé',
    'account/accounts/connect-failed' => "Impossible de se connecter avec un compte. Une erreur est survenue ou l'accès à compte refusé",
    'account/accounts/disconnect-failed' => "Impossible de supprimer le compte.

Cela se produit si le compte lié est la seule méthode d'authentification, à savoir, non spécifiée e-mail ou d'autres comptes.',

    'account/delete/text' => 'Nous sommes désolés, que vous avez dû se trouver sur cette page, et vous envisagez de quitter notre site pour toujours, mais nous ne pouvons pas vous refuser.

En cliquant sur le bouton rouge, toutes les données vous concernant seront anonymisées ou supprimés.

Ce processus est irréversible, alors réfléchissez bien et ne prenez pas de décisions hâtives.",
    'account/deleted/text' => 'Compte supprimé.
Bonne chance.',

    'feedback/title' => 'Retour d\'information',
    'feedback/name'  => 'Votre nom',
    'feedback/message' => 'Message',
    'feedback/donate-text' => 'Si vous êtes venu ici pour exprimer des mots de soutien, il est possible [de l\'exprimer en devises](/donate)',
    'feedback/sent' => 'Your message was sent',

    'map/museums-and-factories' => 'Les musées et les usines',

    'picture/image-specifications' => 'Caractéristiques de l\'image',
    'picture/added-by:' => 'Ajouté:',
    'picture/location' => 'Coordinates',
    'picture/status:' => 'Titre de l\'image:',
    'picture/status/inbox' => 'Une décision ne se fait pas',
    'picture/status/accepted' => 'Reçu',
    'picture/status/removing' => 'Retrait en attente',
    'picture/propose-image-replacement' => 'Propose de remplacer l\'image',
    'picture/moderators-about-this-picture' => 'L\'opinion des modérateurs',
    'picture/all-images-have-owners' => 'Toutes les images ont propriétaires.',
    'picture/if-you-found-error' => 'Si vous pensez que utilisateurs du site s\'est trompé en affichant cette image dans cette section, ce serait bien si vous avez écrit à ce sujet dans les commentaires et ont indiqué à l\'erreur.',
    'picture/where-to-talk' => 'Partagez votre joie, discuter urgente et simplement bavarder sur les automobiles et non seulement vous pouvez sur <a href="/forums/">notre forum</a>',
    'picture/other-languages' => 'In other languages',
    'picture/picture-suggested-to-replace' => 'Picture suggested to replace',
    'picture/that-engine-is-mounted-to-vehicle' => 'That engine is mounted to vehicle',
    'picture/that-engine-is-mounted-to-vehicles:' => 'That engine is mounted to vehicles:',
    'picture/factory-details' => 'Factory details ...',
    'picture/on-that-factory-produced-vehicle' => 'На этом заводе производился такой автомобиль, как',
    'picture/on-that-factory-produced-vehicles:' => 'На этом заводе производились такие автомобили, как: ',
    'picture/on-that-factory-produced-vehicles/and' => 'and',
    'picture/on-that-factory-produced-vehicles/and-other' => 'other ...',

    'user/name' => 'Nom',
    'user/password' => 'Mot de passe',
    'user/password-confirm' => 'Mot de passe (bis)',

    'users/user/known-as:' => 'Connu comme: ',
    'users/user/before-known-as:' => 'avant connu comme: ',
    'users/user/type:' => 'Type de compte: ',
    'users/user/type/moderator' => 'Modérateur',
    'users/user/type/visitor' => 'Visiteur',

    'users/user/upload-pictures:' => 'Images téléchargées: ',
    'users/user/pictures-left-on-site:' => 'laissés sur le site: ',

    'users/comments/order/new' => 'New',
    'users/comments/order/old' => 'Old',
    'users/comments/order/positive' => 'Positive',
    'users/comments/order/negative' => 'Negative',

    'users/for-moderators' => 'Pour les modérateurs',
    'users/for-moderators/remove-photo' => "Supprimer photo de l'utilisateur",
    'users/for-moderators/remove-user' => "Supprimer l'utilisateur",
    'users/for-moderators/last-visit-from-ip' => "Dernière visite de l'adresse IP: ",

    'ban/that-address-is-banned' => 'Cette adresse est interdit',
    'ban/until' => "jusqu'à",
    'ban/unban' => 'débannir',
    'ban/ban' => 'Interdire',
    'ban/reason' => 'Raison',
    'ban/period' => 'Pour la période',
    'ban/period/hour' => 'heure',
    'ban/period/2-hours' => '2 heures',
    'ban/period/4-hours' => '4 heures',
    'ban/period/8-hours' => '8 heures',
    'ban/period/16-hours' => '16 heures',
    'ban/period/day' => 'jour',
    'ban/period/2-days' => '2 jours',

    'users/user/recent-activity' => 'Activité récente',
    'users/user/recent-uploads' => 'Uploads récents',
    'users/user/recent-comments' => 'Commentaires récents',
    'users/user/registration-date' => "Date d'inscription",
    'users/user/last-visited' => 'Dernière visite',
    'users/user/send-personal-message' => 'Envoyer un message personnel',
    'users/user/log' => "Journal de l'utilisateur",
    'users/user/uploaded-pictures' => 'Images téléchargées',
    'users/user/not-upload-pictures' => 'Pas télécharger des images',

    'users/rating/specifications' => 'Caractéristiques',
    'users/rating/pictures' => 'Images',
    'users/rating/likes' => 'Likes',
    'users/rating/picture-likes' => 'Picture likes',
    'users/rating/specs-volume' => 'Volume',
    'users/rating/weight' => 'Poids',

    'users/registration/email-confirm-subject' => 'Enregistrement pour %1$s',
    'users/registration/email-confirm-message' =>
        "Bonjour.\n" .
        "Vous êtes inscrit sur le site %1\$s\n" .
        "Vos données d'enregistrement:\n" .
        "E-mail: %2\$s\n" .
        "Pour confirmer votre inscription, et votre e-mail, vous devrez cliquer sur le lien %3\$s\n\n" .
        "Si vous n'êtes pas inscrit sur le site, il suffit de retirer ce message\n\n" .
        "Sincèrement, le Robot de %4\$s",
    'users/registration/email-confirm/token-not-found' => 'Le lien que vous avez cliqué est expiré',
    'users/registration/email-confirm/success-text' => 'Votre adresse e-mail est confirmé avec succès.',
    'users/registration/success/email-sent' => 'Message contenant des instructions pour vérifier votre e-mail envoyé.',
    'users/change-email/confirm-subject' => 'E-mail confirm pour %1$s',
    'users/change-email/confirm-message' =>
        "Bonjour.\n\n" .
        "Vous ou quelqu'un d'autre a demandé un changement dans l'adresse de votre compte de contact à %2\$s sur le site de %1\$s" .
        "On the %1\$s you or someone else asked outcome variables contact address on your account %2\$s\n" .
        "Pour la confirmation de cette action, vous devez cliquer sur le lien %3\$s\n\n" .
        "Si le message a eu à vous par erreur - il suffit de le supprimer\n\n" .
        "Sincèrement, le Robot de %1\$s\n",
    'users/change-email/confirmation-message-sent' => 'Vous avez envoyé un e-mail avec un code de confirmation',

    'user/add-to-contacts' => 'Ajouter aux contacts',
    'user/remove-from-contacts' => 'Enlever des contacts',

    'donate/text' => 'Malgré le fait que nous positionnons notre Encyclopédie comme un but non lucratif, tout à fait se passer de l\'argent est impossible.

Le contenu du site nécessite certains coûts.

Si vous avez le désir et la capacité de nous soutenir vous pouvez le faire en nous envoyant un peu d\'argent par Paypal ou Yandex.Money.

Avec un don, vous pouvez nous envoyer  quelques lignes sur l\'endroit où vous souhaitez voir le développement du site et nous nous sentirons un peu plus obligés.

Nous ne recueillons pas d\'informations sur les donateurs, mais, si vous insistez, nous pouvons noter votre contribution.

In addition we can offer in return to [choose the next vehicle of the day](/donate/vod)',
    'donate/target' => 'Sur le site',
    'donate/project' => 'WheelsAge.org',
    'donate/comment-hint' => 'Votre souhait',
    'donate/success-text' => 'Merci pour votre soutien.

Allons essayer de ne pas vous décevoir.',

    'donate/vod/text' => 'The little that we can offer in return for financial assistance of the site - it is an opportunity to choose the next "vehicle of the day".

Follow the instructions below.

We will be happy if you want to [perform selfless donation](/donate) to an arbitrary amount.',
    'donate/vod/select-date' => '1. Choose a date',
    'donate/vod/date-busy' => 'busy',
    'donate/vod/select-item' => '2. Choose vehicle',
    'donate/vod/select-item-text' => 'Almost any vehicle can be "Vehicle of the day", but there are some limitations:

- the vehicle must be selected for the first time
- the vehicle must have at least 3 photos',
    'donate/vod/select-item/selection:' => 'Selection: ',
    'donate/vod/select-item/select' => 'Choose ...',
    'donate/vod/preview' => '3. Preview',
    'donate/vod/anonymous' => 'Anonymously',
    'donate/vod/with-name' => 'With the name (registration required)',
    'donate/vod/make-donation' => '4. Make a donation',
    'donate/vod/order-message' => 'WheelsAge.org: vehicle of the day',
    'donate/vod/order-target' => 'Order %s',
    'donate/vod/message' => 'Your wishes',
    'donate/vod/price:' => 'Price:',
    'donate/vod/price/currency' => ' rubles',
    'donate/vod/price-aroud' => 'about $%s',
    'donate/vod/method/credit-card' => 'Credit card',
    'donate/vod/method/mobile' => 'Mobile operator account',
    'donate/vod/method/yandex.money' => 'Yandex.Money',
    'donate/vod/send' => 'Pay',

    'donate/vod/success-text' => 'When the information about your donation comes to us your choice will be carried out.

Thank you for your support.

We will try not to disappoint you.',
    'donate/vod/wants-to-select?' => 'Wants choose next?',

    'message/user-cancel-car-engine' => '%1$s cancel engine %2$s for %3$s ( %4$s ]',

    'car-type/roadster'       => 'Roadster',
    'car-type/spyder'         => 'Spider',
    'car-type/cabriolet'      => 'Cabriolet',
    'car-type/cabrio-coupe'   => 'Coupé cabriolet',
    'car-type/targa'          => 'Targa',
    'car-type/coupe'          => 'Coupé',
    'car-type/sedan'          => 'Berline tricorps',
    'car-type/hatchback'      => 'Berline bicorps',
    'car-type/crossover'      => 'Multisegment',
    'car-type/universal'      => 'Break',
    'car-type/limousine'      => 'Limousine',
    'car-type/pickup'         => 'Pick-up',
    'car-type/caravan'        => 'Caravane',
    'car-type/offroad'        => 'SUV',
    'car-type/minivan'        => 'Monospace',
    'car-type/van'            => 'Fourgon',
    'car-type/truck'          => 'Camion',
    'car-type/bus'            => 'Autobus',
    'car-type/phaeton'        => 'Phaeton',
    'car-type/4door-hardtop'  => 'Berline sans pilier central',
    'car-type/landau'         => 'Landau',
    'car-type/offroad-cabrio' => 'SUV cabriolet',
    'car-type/liftback-coupe' => 'Coupé hayon',
    'car-type/liftback-sedan' => 'Berline hayon',
    'car-type/2door-hardtop'  => 'Coupé sans montants',
    'car-type/minibus'        => 'Minibus',
    'car-type/car'            => 'Voiture',
    'car-type/multiplex-bus'  => 'Autobus multisection',
    'car-type/offroad-short'  => 'SUV court',
    'car-type/brougham'       => 'Coupé Chauffers',
    'car-type/fastback-sedan' => 'Berline à arrière profilé',
    'car-type/fastback-coupe' => 'Coupé à arrière profilé',
    'car-type/tonneau'        => 'Tonneau',
    'car-type/2-floor-bus'    => 'Autobus de 2 étages',
    'car-type/town-car'       => 'Coupé de Ville',
    'car-type/barchetta'      => 'Barquette',
    'car-type/moto'           => 'Moto',
    'car-type/tractor'        => 'Tracteur',
    'car-type/tracked'        => 'Plateforme à chenilles',

    'car-type-rp/roadster'       => 'Roadster',
    'car-type-rp/spyder'         => 'Spider',
    'car-type-rp/cabriolet'      => 'Cabriolet',
    'car-type-rp/cabrio-coupe'   => 'Coupé cabriolet',
    'car-type-rp/targa'          => 'Targa',
    'car-type-rp/coupe'          => 'Coupé',
    'car-type-rp/sedan'          => 'Berline tricorps',
    'car-type-rp/hatchback'      => 'Berline bicorps',
    'car-type-rp/crossover'      => 'Multisegment',
    'car-type-rp/universal'      => 'Break',
    'car-type-rp/limousine'      => 'Limousine',
    'car-type-rp/pickup'         => 'Pick-up',
    'car-type-rp/caravan'        => 'Caravane',
    'car-type-rp/offroad'        => 'SUV',
    'car-type-rp/minivan'        => 'Monospace',
    'car-type-rp/van'            => 'Fourgon',
    'car-type-rp/truck'          => 'Camion',
    'car-type-rp/bus'            => 'AutoBus',
    'car-type-rp/phaeton'        => 'Phaeton',
    'car-type-rp/4door-hardtop'  => 'Berline sans pilier central',
    'car-type-rp/landau'         => 'Landau',
    'car-type-rp/offroad-cabrio' => 'SUV-cabriolet',
    'car-type-rp/liftback-coupe' => 'Coupé hayon',
    'car-type-rp/liftback-sedan' => 'Berline hayon',
    'car-type-rp/2door-hardtop'  => 'Coupé sans montants',
    'car-type-rp/minibus'        => 'Minibus',
    'car-type-rp/car'            => 'Voiture',
    'car-type-rp/multiplex-bus'  => 'Autobus multisection',
    'car-type-rp/offroad-short'  => 'SUV court',
    'car-type-rp/brougham'       => 'Coupé Chauffers',
    'car-type-rp/fastback-sedan' => 'Berline à arrière profilé',
    'car-type-rp/fastback-coupe' => 'Coupé à arrière profilé',
    'car-type-rp/tonneau'        => 'Tonneau',
    'car-type-rp/2-floor-bus'    => 'Autobus de 2 étages',
    'car-type-rp/town-car'       => 'Coupé de Ville',
    'car-type-rp/barchetta'      => 'Barquette',
    'car-type-rp/moto'           => 'Moto',
    'car-type-rp/tractor'        => 'Tracteur',
    'car-type-rp/tracked'        => 'Plateforme à chenilles',

    'about/text' => "### Notre gens
Son existence de notre projet est obligé de les gens qui viennent ici et ont contribué leur temps et leur connaissances.

Quelqu'un ajoute matériaux, et quelqu'un aide à trouver des erreurs dans les existants. Quelqu'un qui se specialize dans une marque particulière, mais quelqu'un a le temps pour tout. Quelqu'un remplit le site étape par étape sans beaucoup d'attention, mais quelqu'un qui recueille ovation avec rares, mais brûlants photos.

Nous sommes nombreux, nous sommes différents, et c'est très bien. Voici quelques-uns d'entre nous:

%users%

#### \"La différenciation de la couleur des utilisateurs\"

Donc, il est arrivé que nous mettons en évidence certains de nos gens d'une couleur spéciale – vert, mais pas seulement - c'est une etiquette spéciale. Vous savez, si vous voyez une personne de «vert», vous pouvez toujours l'attraper et demander quoi que ce soit autour de notre projet, car les “verts” sont les plus sensibles et intéressés gens par la vie de projet.

Une partie des \"verts\" sont les modérateurs.

### Dialogue et retour d'information.

Si vous avez des commentaires, suggestions ou autres idées, vous pouvez les annoncer sur [le forum](/forums/), demander personnellement via la messagerie, ou écrire dans le \"[Retour d'information](/ng/feedback)\" de l'administration du site.

Si vous avez des questions concernant la publicité, échange de lien ou de promouvoir votre produit par d'autres moyens, ils ont tous une réponse: on ne place pas la publicité.

### Nombres

C'est ainsi, que nous aimons le nombre élevé de vanité amuse, ainsi que de leur spectacle. À votre attention sont présentés quelques-unes:

* sur le site il y a plus de %total-pictures% images, %total-vehicles% véhicules, ce qui représente environ %total-size% de données
* il y a environ %total-users% membres, qui ont écrit plus de %total-comments% messages.

### Développement

Développement et maintenance du projet est réalisée principalement par %developer% ([contributors](https://github.com/autowp/autowp/graphs/contributors))

La traduction du site en français: %fr-translator%

La traduction du site en chinois: %zh-translator%

Le site propulsé par [Zend Framework](http://framework.zend.com/), [jQuery](http://jquery.com/), [Twitter bootstrap](http://getbootstrap.com/), ainsi que de nombreux autres.

Une partie du code source du site est ouverte et un cours de communication supplémentaire, que tout le monde a eu l'occasion d'influencer la nature et la qualité du projet.

%github%

[![Build Status](https://travis-ci.org/autowp/autowp.svg?branch=master)](https://travis-ci.org/autowp/autowp)
[![Code Climate](https://codeclimate.com/github/autowp/autowp/badges/gpa.svg)](https://codeclimate.com/github/autowp/autowp)
[![Coverage Status](https://coveralls.io/repos/github/autowp/autowp/badge.svg?branch=master)](https://coveralls.io/github/autowp/autowp?branch=master)

### Soutenir le projet

Vous pouvez soutenir notre projet [financièrement](/donate) ou [moralement](/ng/feedback).
Take part in [the translation of the site](https://github.com/autowp/autowp/tree/master/module/Application/language) into other languages.",

    'page/1/name' => "Page d'accueil",
    'page/2/name' => "Main menu",
    'page/10/name' => "Marque",
    'page/14/name' => "%BRAND_NAME% dans l'ordre chronologique",
    'page/15/name' => "Les dernières photos de %BRAND_NAME%",
    'page/19/name' => "Marques",
    'page/20/name' => "Type de fabricants",
    'page/21/name' => "Les meilleurs",
    'page/22/name' => "Catégories",
    'page/24/name' => "Editions limitées",
    'page/25/name' => "Jumeaux",
    'page/27/name' => "Caractéristiques de %TWINS_GROUP_NAME%",
    'page/28/name' => "Toutes les photos de %TWINS_GROUP_NAME%",
    'page/29/name' => "Ajouter une image",
    'page/30/name' => "Sélectionnez la marque",
    'page/31/name' => "Articles",
    'page/34/name' => "Toutes les images de %CAR_NAME%",
    'page/36/name' => "Caractéristiques de %CAR_NAME%",
    'page/37/name' => "Concepts et prototypes de %BRAND_NAME%",
    'page/39/name' => "Logotypes de %BRAND_NAME%",
    'page/40/name' => "%BRAND_NAME% varié",
    'page/41/name' => "Non triée",
    'page/42/name' => "Forums",
    'page/45/name' => "Nouveau sujet",
    'page/48/name' => "Page personnelle",
    'page/49/name' => "Messages personnels",
    'page/51/name' => "Nouvelles photos",
    'page/52/name' => "Enregistrement",
    'page/53/name' => "ok",
    'page/54/name' => "Confirmez l'adresse e-mail",
    'page/55/name' => "Mon e-mail",
    'page/56/name' => "Changed",
    'page/57/name' => "Abonnement aux forums",
    'page/60/name' => "Récupération de mot de passe",
    'page/61/name' => "Toutes les marques",
    'page/63/name' => "Utilisateurs images",
    'page/66/name' => "Toutes les images de %BRAND_NAME% %DESIGN_PROJECT_NAME%",
    'page/67/name' => "Page Modérateur",
    'page/68/name' => "Pages",
    'page/69/name' => "Ajouter",
    'page/70/name' => "Changer",
    'page/71/name' => "Les droits",
    'page/73/name' => "Les images",
    'page/74/name' => "Voitures par ordre alphabétique",
    'page/75/name' => "Le journal des événements",
    'page/76/name' => "Attendre la modération",
    'page/77/name' => "Le trafic",
    'page/79/name' => "Entrée",
    'page/80/name' => "Envoyé",
    'page/81/name' => "Notifications système",
    'page/83/name' => "Déplacer",
    'page/86/name' => "Image téléchargée avec succès sur le site",
    'page/87/name' => "Plus",
    'page/89/name' => "Retour d'information",
    'page/90/name' => "Sortie",
    'page/91/name' => "Registration",
    'page/93/name' => "Message envoyé",
    'page/94/name' => "En attente de modération",
    'page/96/name' => "Les voitures-jumeaux",
    'page/97/name' => "Les angles",
    'page/100/name' => "Les attributs",
    'page/102/name' => "%CAR_NAME% spécifications éditeur",
    'page/103/name' => "L'histoire",
    'page/104/name' => "Personnalisée statistiques",
    'page/105/name' => "Ajouter un commentaire",
    'page/106/name' => "Le règlement",
    'page/107/name' => "Les demandes de retrait",
    'page/109/name' => "Cutaway",
    'page/110/name' => "Les comments",
    'page/114/name' => "Le journal des caractéristiques techniques",
    'page/117/name' => "Le carte",
    'page/119/name' => "La statistique",
    'page/120/name' => "Les modules",
    'page/122/name' => "Les caractéristiques",
    'page/123/name' => "Mes comptes",
    'page/124/name' => "Qui est connecté?",
    'page/125/name' => "Les categories",
    'page/126/name' => "Ajouter",
    'page/127/name' => "Modifier",
    'page/128/name' => "Les entrants",
    'page/129/name' => "Le profile",
    'page/130/name' => "Mes images",
    'page/131/name' => "Items",
    'page/133/name' => "Contrôle d'accès",
    'page/134/name' => "Neuve passvord",
    'page/135/name' => "Nouveau mot de passe sauvegardé",
    'page/136/name' => "À propos",
    'page/137/name' => "Suppression d'un compte",
    'page/138/name' => "%BRAND_NAME% %CAR_TYPE_NAME% dans l'ordre chronologique",
    'page/141/name' => "Images de %BRAND_NAME%",
    'page/144/name' => "La sélection",
    'page/148/name' => "Surgir",
    'page/149/name' => "Image déplacer",
    'page/153/name' => "%BRAND_NAME% jumeaux",
    'page/154/name' => "La plupart %MOST_NAME%",
    'page/155/name' => "La plupart %MOST_NAME% %CAR_TYPE_NAME%",
    'page/156/name' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %YEAR_NAME%",
    'page/159/name' => "le musée",
    'page/161/name' => "Impulsion",
    'page/162/name' => "Toutes les images",
    'page/164/name' => "Les meilleurs",
    'page/165/name' => "La plupart %MOST_NAME% de %BRAND_NAME%",
    'page/166/name' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %BRAND_NAME%",
    'page/167/name' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %BRAND_NAME% de %YEAR_NAME%",
    'page/173/name' => "La statistique",
    'page/174/name' => "Les spécifications",
    'page/175/name' => "Les usines",
    'page/176/name' => "Ajouter",
    'page/180/name' => "Les usines",
    'page/182/name' => "Produits",
    'page/186/name' => "Toutes les images",
    'page/188/name' => "Conflits",
    'page/189/name' => "Faible poids",
    'page/196/name' => "La donation",
    'page/197/name' => "Texte Histoire",
    'page/198/name' => "Contacts",

    'page/1/title' => "Encyclopédie de voitures en images. WheelsAge.org",
    'page/14/title' => "%BRAND_NAME% dans l'ordre chronologique",
    'page/15/title' => "Les dernières photos de %BRAND_NAME%",
    'page/19/title' => "Marques",
    'page/21/title' => "Les meilleurs",
    'page/22/title' => "Catégories",
    'page/25/title' => "Voitures jumeaux",
    'page/27/title' => "Caractéristiques de %TWINS_GROUP_NAME%",
    'page/28/title' => "Toutes les photos de %TWINS_GROUP_NAME%",
    'page/29/title' => "Ajouter une image",
    'page/30/title' => "Sélectionnez la marque",
    'page/31/title' => "Articles",
    'page/34/title' => "Toutes les images de %CAR_NAME%",
    'page/36/title' => "Caractéristiques de %CAR_NAME%",
    'page/37/title' => "Concepts et prototypes de %BRAND_NAME%",
    'page/39/title' => "Logotypes de %BRAND_NAME%",
    'page/40/title' => "%BRAND_NAME% varié",
    'page/41/title' => "Unsorted",
    'page/42/title' => "Forums",
    'page/45/title' => "New topic",
    'page/48/title' => "Page personnelle",
    'page/49/title' => "Messages personnels",
    'page/52/title' => "Enregistrement",
    'page/53/title' => "Inscription réussie",
    'page/54/title' => "Confirmez l'adresse e-mail",
    'page/55/title' => "Mon e-mail",
    'page/56/title' => "Changing e-mail",
    'page/57/title' => "Abonnement aux forums",
    'page/60/title' => "Récupération de mot de passe",
    'page/63/title' => "Utilisateurs images",
    'page/66/title' => "Toutes les images de %BRAND_NAME% %DESIGN_PROJECT_NAME%",
    'page/76/title' => "Attendre la modération",
    'page/79/title' => "Entrée",
    'page/80/title' => "Envoyé",
    'page/81/title' => "Notifications système",
    'page/83/title' => "Déplacer",
    'page/86/title' => "Image téléchargée avec succès sur le site",
    'page/87/title' => "Plus",
    'page/94/title' => "En attente de modération",
    'page/102/title' => "%CAR_NAME% spécifications éditeur",
    'page/103/title' => "Histoire",
    'page/105/title' => "Ajouter un commentaire",
    'page/106/title' => "Règlement",
    'page/107/title' => "Les demandes de retrait",
    'page/110/title' => "Commentaires",
    'page/114/title' => "Log de caractéristiques",
    'page/117/title' => "Carte",
    'page/122/title' => "Caractéristiques",
    'page/123/title' => "Mes comptes",
    'page/125/title' => "Catégories",
    'page/126/title' => "Ajouter",
    'page/127/title' => "Modifier",
    'page/128/title' => "Entrants",
    'page/129/title' => "Profile",
    'page/130/title' => "Mes images",
    'page/133/title' => "Contrôle d'accès",
    'page/134/title' => "Nouveau mot de passe",
    'page/136/title' => "À propos",
    'page/138/title' => "%BRAND_NAME% %CAR_TYPE_NAME% dans l'ordre chronologique",
    'page/141/title' => "Images de %BRAND_NAME%",
    'page/144/title' => "Parent selection",
    'page/148/title' => "Couper",
    'page/149/title' => "Déplacer l'image",
    'page/153/title' => "%BRAND_NAME% jumeaux",
    'page/154/title' => "La plupart %MOST_NAME%",
    'page/155/title' => "La plupart %MOST_NAME% %CAR_TYPE_NAME%",
    'page/156/title' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %YEAR_NAME%",
    'page/161/title' => "Impulsion",
    'page/162/title' => "Toutes les images",
    'page/163/title' => "Nouveau véhicule",
    'page/164/title' => "Les meilleurs",
    'page/165/title' => "La plupart %MOST_NAME% de %BRAND_NAME%",
    'page/166/title' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %BRAND_NAME%",
    'page/167/title' => "La plupart %MOST_NAME% %CAR_TYPE_NAME% de %BRAND_NAME% de %YEAR_NAME%",
    'page/173/title' => "La statistique",
    'page/174/title' => "Spécifications",
    'page/175/title' => "Des usines",
    'page/176/title' => "Ajouter",
    'page/180/title' => "Des usines",
    'page/182/title' => "Produits",
    'page/186/title' => "Toutes les images",
    'page/188/title' => "Conflits",
    'page/189/title' => "Faible poids",
    'page/196/title' => "Donation",
    'page/197/title' => "Texte d'histoire",
    'page/198/title' => "Contacts",

    'page/14/breadcrumbs' => "Dans l'ordre chronologique",
    'page/15/breadcrumbs' => "Les dernières photos",
    'page/19/breadcrumbs' => "Marques",
    'page/27/breadcrumbs' => "Spécifications",
    'page/28/breadcrumbs' => "Toutes les images",
    'page/30/breadcrumbs' => "Sélectionnez la marque",
    'page/31/breadcrumbs' => "Des articles",
    'page/34/breadcrumbs' => "Toutes les images",
    'page/36/breadcrumbs' => "Spécifications",
    'page/37/breadcrumbs' => "Concepts et prototypes",
    'page/39/breadcrumbs' => "Logotypes",
    'page/40/breadcrumbs' => "Varié",
    'page/41/breadcrumbs' => "Non trié",
    'page/45/breadcrumbs' => "New topic",
    'page/63/breadcrumbs' => "Utilisateurs images",
    'page/66/breadcrumbs' => "Toutes les images",
    'page/76/breadcrumbs' => "Attendre la modération",
    'page/83/breadcrumbs' => "Déplacer",
    'page/86/breadcrumbs' => "Succès",
    'page/102/breadcrumbs' => "Spécifications éditeur",
    'page/109/breadcrumbs' => "Cutaway",
    'page/122/breadcrumbs' => "Spécifications",
    'page/123/breadcrumbs' => "Mes comptes",
    'page/136/breadcrumbs' => "À propos",
    'page/141/breadcrumbs' => "Images de %BRAND_NAME%",
    'page/144/breadcrumbs' => "Séléction",
    'page/148/breadcrumbs' => "Surgir",
    'page/149/breadcrumbs' => "Déplacer l'image",
    'page/161/breadcrumbs' => "Impulsion",
    'page/162/breadcrumbs' => "Toutes les images",
    'page/163/breadcrumbs' => "Nouveau véhicule",
    'page/164/breadcrumbs' => "Les meilleurs",
    'page/173/breadcrumbs' => "La statistique",
    'page/174/breadcrumbs' => "Spécifications",
    'page/175/breadcrumbs' => "Des usines",
    'page/176/breadcrumbs' => "Ajouter",
    'page/180/breadcrumbs' => "Des usines",
    'page/182/breadcrumbs' => "Produits",
    'page/186/breadcrumbs' => "Toutes les images",
    'page/188/breadcrumbs' => "Conflits",
    'page/189/breadcrumbs' => "Faible poids",
    'page/196/breadcrumbs' => "Donation",
    'page/197/breadcrumbs' => "Texte d'histoire",
    'page/198/breadcrumbs' => "Contacts",

    'page/201/name'        => "Mascottes",
    'page/201/title'       => "Mascottes",
    'page/201/breadcrumbs' => "Mascottes",

    'page/202/name'        => 'Perspectives',
    'page/202/title'       => 'Perspectives',
    'page/202/breadcrumbs' => 'Perspectives',

    'page/203/name'        => 'Utilisateurs',
    'page/203/title'       => 'Utilisateurs',
    'page/203/breadcrumbs' => 'Utilisateurs',

    'page/204/name'        => 'Telegram',
    'page/204/title'       => 'Telegram',
    'page/204/breadcrumbs' => 'Telegram',

    'page/205/name'        => 'Commentaires',
    'page/205/title'       => 'Commentaires',
    'page/205/breadcrumbs' => 'Commentaires',

    'page/208/name'        => '%BRAND_NAME% Engines',
    'page/208/title'       => '%BRAND_NAME% Engines',
    'page/208/breadcrumbs' => '%BRAND_NAME% Engines',

    'page/211/name'        => 'Contacts',
    'page/211/title'       => 'Contacts',
    'page/211/breadcrumbs' => 'Contacts',

    'page/212/name'        => 'Picture vote templates',
    'page/212/title'       => 'Picture vote templates',
    'page/212/breadcrumbs' => 'Picture vote templates',

    'moder-menu/title' => 'Menu modérateur',
    'moder-menu/inbox' => 'Inbox',

    'moder/database-id-%s' => 'Base de données id: %s',
    'ng/moder/database-id-n' => 'Base de données id: {id}',

    'moder/picture/missing-perspective' => 'Perspective manquant',
    'moder/picture/delete-queue' => "File d'attente",
    'moder/picture/votes' => 'Votes',
    'moder/picture/comments' => 'Commentaires',
    'moder/picture/new-votes' => 'Nouveaux votes',
    'moder/picture/replaces' => 'Remplacements',
    'moder/picture/edit/special-name' => 'Nom spécial',

    'moder/pictures/acceptance/message' => 'Message',

    'moder/vehicle/name' => 'Name',
    'moder/vehicle/body' => 'Body number',
    'moder/vehicle/spec' => 'Spec',
    'moder/vehicle/type' => 'Type',
    'moder/vehicle/model-years' => 'Model years',
    'moder/vehicle/begin' => 'Begin',
    'moder/vehicle/end' => 'End',
    'moder/item/produced' => 'Produced',
    'moder/item/produced/number' => 'number',
    'moder/item/produced/precision' => 'exactly?',
    'moder/item/produced/about' => 'about',
    'moder/item/produced/exactly' => 'exactly',
    'moder/vehicle/concept' => 'Concept (prototype)',
    'moder/vehicle/group' => 'Group',
    'moder/vehicle/year' => 'year',
    'moder/vehicle/year/from' => 'from',
    'moder/vehicle/year/to' => 'to',
    'moder/vehicle/month' => 'month',
    'moder/vehicle/today' => 'today',
    'moder/vehicle/today/ended' => 'ended',
    'moder/vehicle/today/continue' => 'continue in pr.',
    'moder/vehicle/is-concept/no' => 'no',
    'moder/vehicle/is-concept/yes' => 'yes',
    'moder/vehicle/is-concept/inherited-no' => 'inherited (no)',
    'moder/vehicle/is-concept/inherited-yes' => 'inherited (yes)',
    'moder/vehicle/is-concept/inherited' => 'inherited',

    'moder/item/short-description' => 'Short description',
    'moder/item/full-description' => 'Full description',

    'moder/vehicle/meta/description' => 'Short description',

    'moder/vehicle/move/here' => 'here',

    'moder/vehicle/add/as-submodel' => 'As submodel',

    'attrs/attribute/name' => 'Name',
    'attrs/attribute/type' => 'Type',
    'attrs/attribute/unit' => 'Unit',
    'attrs/attribute/precision' => 'Precision (for float attribute)',
    'attrs/attribute/description' => 'Description',

    'attrs/list-options/parent' => 'Parent',
    'attrs/list-options/name' => 'Name',

    'moder/attrs/zones' => 'Zones',
    'moder/attrs/parameters' => 'Attribute parameters',
    'moder/attrs/parameters/options-list' => 'Values list (for select)',
    'moder/attrs/parameters/options-list/add' => 'Add value',
    'moder/attrs/attributes' => 'Attributes',
    'moder/attrs/attribute/add-subattribute' => 'add subattribute',
    'moder/attrs/attributes-order' => 'Attributes order',

    'category/name' => 'Name',
    'category/parent' => 'Parent',
    'categories/other' => 'Other',

    'moder/categories/add' => 'Add',
    'moder/categories/edit' => 'Edit',
    'moder/categories/new' => 'New',

    'moder/picture/edit-picture-%s' => 'Edit picture №%s',
    'moder/picture/edit-item-%s' => 'Edit item %s',
    'moder/picture/edit-brand-%s' => 'Edit brand %s',
    'moder/picture/edit-engine-%s' => 'Edit engine %s',
    'moder/picture/edit-factory-%s' => 'Edit factory %s',

    'moder/picture/picture-n-%s' => 'Picture №%s',
    'moder/picture/previous' => '<< previous',
    'moder/picture/next' => 'next >>',
    'moder/picture/previous-new' => '<< previous new',
    'moder/picture/next-new' => 'next new >>',

    'moder/picture/perspective' => 'Perspective',
    'moder/picture/perspective-by' => 'By: ',

    'moder/picture/acceptance' => 'Status/Delete/Accept',
    'moder/picture/acceptance/accepted' => 'Accepted',
    'moder/picture/acceptance/not-accepted' => 'Not accepted',
    'moder/picture/acceptance/in-delete-queue' => 'In delete queue',
    'moder/picture/acceptance/accept' => 'Accept',
    'moder/picture/acceptance/delete' => 'Delete',
    'moder/picture/acceptance/unaccept' => 'Unaccept',
    'moder/picture/acceptance/restore' => 'Restore',
    'moder/picture/acceptance/removed' => 'Deleted',
    'moder/picture/acceptance/removing' => 'Deleting',
    'moder/picture/acceptance/inbox' => 'Inbox',
    'moder/picture/acceptance/reason' => 'Reason',
    'moder/picture/acceptance/custom' => 'Custom ...',
    'moder/picture/acceptance/add-reason' => 'Add template',
    'moder/picture/acceptance/vote' => 'Vote',

    'moder/picture/acceptance/want-accept' => 'Want accept',
    'moder/picture/acceptance/want-delete' => 'Want delete',
    'moder/picture/acceptance/cancel-vote' => 'Cancel my vote',
    'moder/picture/acceptance/already-voted' => 'Already voted: ',
    'moder/picture/acceptance/that-is-one-accepted-picture' => 'That is single picture of that vehicle',
    'ng/moder/picture/acceptance/accepted-pictures-is-n' => 'Accepted pictures count still {count}',
    'moder/picture/acceptance/accepted-pictures-is-%s' => 'Accepted pictures count still %s',

    'moder/picture/public-url:' => 'Public URL: ',
    'moder/picture/image:' => 'Image: ',
    'moder/picture/image-specs:' => 'Image specs: ',
    'moder/picture/resolution:' => 'Resolution: ',
    'moder/picture/filesize:' => 'File size: ',
    'moder/picture/upload-date:' => 'Upload date: ',
    'moder/picture/settings' => 'Settings',
    'moder/picture/copyrights' => 'Copyrights',

    'moder/picture/replacement' => 'Replacement',
    'moder/picture/replacement/photo-suggested-to-replace' => 'Photo suggested to replace',
    'moder/picture/replacement/accept-and-delete-double' => 'Accept and remove double',
    'moder/picture/replacement/cancel' => 'Cancel replacement',

    'moder/picture/uploader' => 'Uploader',
    'moder/picture/uploader/unknown' => 'Unknown',
    'moder/picture/uploader/id-address:' => 'IP-address of uploader: ',

    'moder/pciture/move/brands' => 'Brands',
    'moder/pciture/move/factories' => 'Factories',
    'moder/pciture/move/engines' => 'Engines',

    'brand' => 'Brand',
    'brand/name' => 'Name',
    'brand/logo' => 'Logotype',

    'moder/brands/meta-data/full-name' => 'Full name',
    'moder/brands/logo/description' => '* Logotopy must be in PNG format.
* All transparent regions must be transparent but not white!
* Logotype must be close to edges. Margin not required
* Upload images in maximum resolution - thats give quality and ability to use it in future. On website logo scaled automaticaly',
    'moder/brands/logo/saved' => 'Logotype saved',

    'moder/brands/links' => 'Links',
    'moder/brands/links/text' => 'Text',
    'moder/brands/links/address' => 'Address',
    'moder/brands/links/type' => 'Type',
    'moder/brands/links/type/interest-link' => 'interest link',
    'moder/brands/links/type/official' => 'official',
    'moder/brands/links/type/club' => 'club',

    'moder/edit-object' => 'edit',

    'moder/markdown/description' => 'Markdown syntax.

Few manuals:
[1](https://en.wikipedia.org/wiki/Markdown),
[2](https://guides.github.com/features/mastering-markdown/),
[3](https://daringfireball.net/projects/markdown/basics),

Internal hyperlinks must be relative: ~~http://autowp.ru/bmw/~~ */bmw/*',
    'moder/markdown/edit' => 'Edit',
    'moder/markdown/preview' => 'Preview',
    'moder/markdown/history' => 'History',

    'moder/users/login' => 'Login',
    'moder/users/name' => 'Name',
    'moder/users/role' => 'Role',
    'moder/users/profile' => 'Profile',
    'moder/users/last-visit' => 'Last visit',
    'moder/users/registration' => 'Registration',

    'moder/twins/public-url:' => 'Public URL: ',
    'moder/twins/meta-data' => 'Meta-data',
    'moder/twins/name' => 'Name',
    'moder/twins/short-description' => 'Short description',
    'moder/twins/vehicles' => 'Vehicles',
    'moder/twins/add/title' => 'Create new twins group',

    'latitude' => 'Latitude',
    'longtitude' => 'Longtitude',

    'museum/name' => 'Name',
    'museum/address' => 'Address',
    'museum/photo' => 'Photo',
    'museum/description' => 'Description',

    'moder/museums/add' => 'Add museum',

    'factory/name' => 'Name',
    'factory/year_from' => 'From year',
    'factory/year_to' => 'To year',

    'moder/comments/title' => 'Comments',
    'moder/comments/filter' => 'Filter',
    'moder/comments/filter/user-id' => 'User №',
    'moder/comments/filter/brand-id' => 'Brand №',
    'moder/comments/filter/moderator_attention' => 'Moderator attention',
    'moder/comments/filter/moderator_attention/not-matters' => 'Not matters',
    'moder/comments/filter/moderator_attention/not-required' => 'Not required',
    'moder/comments/filter/moderator_attention/required' => 'Required',
    'moder/comments/filter/moderator_attention/resolved' => 'Resolved',
    'moder/comments/filter/vehicle-id' => 'Item',
    'moder/comments/not-readed' => 'not readed',

    'engine/name' => 'Name',

    'moder/engines/engine/vehicles' => 'Vehicles with that engine',

    'page/name' => 'Name',
    'page/is_group_node' => 'Is group node?',
    'page/registered_only' => 'Only for registered?',
    'page/guests_only' => 'Only for guests?',
    'page/class' => 'Class',
    'page/parent' => 'Parent',

    'moder/pages/new' => 'New',

    'moder/index/other-tools' => 'Other tools',

    'moder/acl/add-role' => 'Add role',
    'moder/acl/add-rule' => 'Add rule',
    'moder/acl/add-rule/action' => 'Action',
    'moder/acl/add-rule/action/allow' => 'allow',
    'moder/acl/add-rule/action/deny' => 'deny',
    'moder/acl/add-parent' => 'Add parent',
    'moder/acl/role' => 'Role',
    'moder/acl/parent-role' => 'Parent role',
    'moder/acl/privilege' => 'Privilege',

    'votings/do-vote' => 'Vote',
    'votings/who-voted' => 'Qui a voté?',
    'votings/voting/voters/during-%s-%s' => 'Vote au cours de %s à %s',
    'votings/voting/voters/show-all' => 'montre tout',
    'votings/voting/voters/show-contributors' => 'montrer à partir de 100 images téléchargées',

    'museums/museum/address:' => 'Adresse:',
    'museums/museum/on-the-map' => 'Sur la carte',
    'museums/museum/website:' => 'site Web: ',

    'restore-password/text' => 'Si vous avez perdu votre mot de passe - entrez votre e-mail et nous vous donner des instructions pour créer une nouvelle',
    'restore-password/new-password/text' => 'Entrez un nouveau mot de passe',
    'restore-password/new-password/saved' => "Nouveau mot de passe ont été enregistrés.

Ne l'oubliez pas.",
    'restore-password/new-password/instructions-sent' => 'Instructions envoyées à votre e-mail',
    'restore-password/new-password/email-not-found' => 'Utilisateur avec cette e-mail ne trouve pas',
    'restore-password/new-password/mail/subject' => 'Mot de passe restauré',
    'restore-password/new-password/mail/body-%s' =>
        "Suivez le lien pour entrer un nouveau mot de passe: %s\n\n" .
        "Sincèrement, robot www.wheelsage.org\n",

    'twins/group/name' => 'Nom',
    'twins/group/description' => 'Description',

    'specifications-editor/description' => "S'il vous plaît prendre avec toute la responsabilité à toutes les actions effectuées depuis ils deviennent publics.


Sur la structure des mêmes spécifications.

Toutes les spécifications sont organisées comme un arbre d'éléments \"l'option - valeur\". Les valeurs peuvent être numériques ou texte.

Une caractéristique importante est le fait que chaque spécification peut être définie par plusieurs utilisateurs simultanément. Dans ce cas, la valeur (utilisée) actuelle est que le système juge une priorité pour un certain nombre de motifs.

Si vous êtes confronté à une situation où vous ne pouvez pas bloquer l'entrée de quelqu'un de la valeur précédemment, vous pouvez simplement écrire sur cet auteur. D'ailleurs dans le processus de mise en œuvre de système automatique notifie à l'auteur que de la valeur de quelqu'un n'est pas d'accord.


Toutes les valeurs qui sont entrées ici - apparaissent sur le site, dans les spécifications, utilisés dans la construction des sections de \"plus-plus\", et apparaissent dans plusieurs autres zones du site.",
    'specifications-editor/not-save' => "Les données enregistrez pas à cause de l'erreur. Les détails ci-dessous",
    'specifications-editor/parameter' => 'Paramètre',
    'specifications-editor/your-value' => 'Votre valeur',
    'specifications-editor/actual-value' => 'Valeur actuelle',
    'specifications-editor/all-values' => 'Toutes les valeurs',
    'specifications-editor/tabs/info' => 'Info',
    'specifications-editor/tabs/engine' => 'Moteur',
    'specifications-editor/tabs/specs' => 'Spécifications',
    'specifications-editor/tabs/result' => 'Résultat',
    'specifications-editor/tabs/admin' => 'Administrateur',
    'specifications-editor/engine' => 'Moteur à partir du catalogue',
    'specifications-editor/engine/inherited-from' => 'Hérité de',
    'specifications-editor/engine/select-another' => 'Sélectionnez un autre moteur',
    'specifications-editor/engine/cancel' => 'Annuler la sélection du moteur',
    'specifications-editor/engine/inherit' => 'Hériter moteur',
    'specifications-editor/engine/not-selected' => '[not selected]',
    'specifications-editor/engine/select' => 'Sélectionnez un moteur',
    'specifications-editor/engine/dont-inherit' => 'Ne pas hériter moteur',
    'specifications-editor/save' => 'Envoyer',

    'specifications-editor/log' => 'Journal de valeurs',
    'specifications-editor/log/date' => 'Date',
    'specifications-editor/log/user' => 'Utilisateur',
    'specifications-editor/log/object' => 'Objet',
    'specifications-editor/log/attribute' => 'Attribut',
    'specifications-editor/log/value' => 'Valueur',
    'specifications-editor/log/editor' => 'Editeur',
    'specifications-editor/log/to-editor' => 'Éditer',
    'specifications-editor/log/filter/user-id' => 'Utilisateur',
    'specifications-editor/log/low-weight-text' => "Enregistré un grand nombre de conflits dans les données que vous avez entré. D'autres personnes sont souvent démentent les informations que vous nous fournissez.
La saisie des données est temporairement suspendu. S'il vous plaît corriger la situation, procéder à un audit des conflits.

Réaliser cela aidera à [une interface spéciale](/account/specs-conflicts/conflict/minus-weight), qui contient au moins les erreurs qui ont trouvé les visiteurs.",
    'specifications-editor/errors-alert' => "**Attention!** Probablement, vous avez accumulé un grand nombre d'erreurs.

Certains d'entre eux, trouvés par d'autres utilisateurs, vous pouvez trouver [ici](/account/specs-conflicts)",

    'specifications-editor/admin/date' => 'Date',
    'specifications-editor/admin/user' => 'Utilisateur',
    'specifications-editor/admin/parameter' => 'Paramètre',
    'specifications-editor/admin/value' => 'Valueur',
    'specifications-editor/admin/move' => 'Déplacer',

    'pm/user-%s-edited-brand-description-%s-%s' => 'User %s edited brand description %s ( %s )',
    'pm/user-%s-edited-item-language-%s-%s' => "User %s edited language data %s ( %s )\n%s",
    'pm/user-%s-edited-vehicle-meta-data-%s-%s-%s' => "User %s edited meta-data %s ( %s )\n%s",
    'pm/user-%s-adds-item-%s-%s-to-item-%s-%s' => 'User %s added %s ( %s ) to %s ( %s )',
    'pm/user-%s-removed-item-%s-%s-from-item-%s-%s' => 'User %s removed %s ( %s ) from %s ( %s )',
    'pm/user-%s-cancel-link-vehicle-%s-%s-with-categories-%s' => 'User %s cancel link vehicle %s ( %s ) with categories: %s',
    'pm/user-%s-edited-factory-description-%s-%s' => 'User %s edited factory description %s ( %s )',
    'pm/your-picture-%s-enqueued-to-remove-%s' => "Your picture %s enqueued to remove\n%s",
    'pm/new-picture-%s-vote-%s/accept' => "New picture accept vote\n%s\nReason: %s",
    'pm/new-picture-%s-vote-%s/delete' => "New picture delete vote\n%s\nReason: %s",
    'pm/user-%s-edited-picture-copyrights-%s-%s' => 'User %s edited picture copyrights %s ( %s )',
    'pm/user-%s-accept-replace-%s-%s' => '%s accept replacement %s на %s',
    'pm/your-picture-accepted-%s' => "Your picture was accepted\n%s",
    'pm/user-%s-edited-twins-description-%s-%s' => 'User %s edited twins group description %s ( %s )',
    'pm/user-%s-edited-vehicle-specs-%s' => '%s edited vehicle spcifications %s',
    'pm/user-%s-canceled-vehicle-engine-%s-%s-%s' => '%s canceled engine %s for vehicle %s ( %s )',
    'pm/user-%s-set-inherited-vehicle-engine-%s-%s' => '%s set inherited engine to vehicle %s ( %s )',
    'pm/user-%s-set-vehicle-engine-%s-%s-%s' => '%s set engine %s to vehicle %s ( %s )',
    'pm/user-%s-replies-to-you-%s' => "%s replies to you\n%s",
    'pm/user-%s-post-new-message-%s' => "%s posted new message\n%s",

    'moder/vehicle/changes/name-%s-%s' => 'name from "%s" to "%s"',
    'moder/vehicle/changes/body-%s-%s' => 'body number from "%s" to "%s"',
    'moder/vehicle/changes/from/year-%s-%s' => 'start year from "%s" to "%s"',
    'moder/vehicle/changes/from/month-%s-%s' => 'start month from "%s" to "%s"',
    'moder/vehicle/changes/to/year-%s-%s' => 'end year from "%s" to "%s"',
    'moder/vehicle/changes/to/month-%s-%s' => 'end month from "%s" to "%s"',
    'moder/vehicle/changes/to/today-%s-%s' => 'produced today from "%s" to "%s"',
    'moder/vehicle/changes/produced/count-%s-%s' => 'produced count from "%s" to "%s"',
    'moder/vehicle/changes/produced/exactly-%s-%s' => 'produced count exactly from "%s" to "%s"',
    'moder/vehicle/changes/is-group-%s-%s' => 'group flag from "%s" to "%s"',
    'moder/vehicle/changes/car-type-%s-%s' => 'car type from "%s" to "%s"',
    'moder/vehicle/changes/model-years/from-%s-%s' => 'start model year from "%s" to "%s"',
    'moder/vehicle/changes/model-years/to-%s-%s' => 'end model year from "%s" to "%s"',
    'moder/vehicle/changes/spec-%s-%s' => 'spec from "%s" to "%s"',

    'moder/vehicle/changes/boolean/true' => 'yes',
    'moder/vehicle/changes/boolean/false' => 'no',

    'specifications/no-value-text' => 'Non valeur',
    'specifications/boolean/false' => 'Non',
    'specifications/boolean/true' => 'Oui',

    "Brand '%value%' already exists" => "Brand '%value%' already exists",
    "E-mail '%value%' not registered" => "E-mail '%value%' not registered",
    "E-mail '%value%' already registered" => "E-mail '%value%' already registered",

    'specs/attrs/45' => 'le titre de la modification',
    'specs/attrs/95' => 'années de production',
    'specs/attrs/95/96' => 'de',
    'specs/attrs/95/97' => 'a',
    'specs/attrs/95/106' => 'vente',
    'specs/attrs/95/106/109' => 'de',
    'specs/attrs/95/106/109/129' => 'année',
    'specs/attrs/95/106/109/130' => 'mois',
    'specs/attrs/95/106/109/131' => 'nombre',
    'specs/attrs/95/106/111' => 'a',
    'specs/attrs/95/106/111/132' => 'année',
    'specs/attrs/95/106/111/133' => 'mois',
    'specs/attrs/95/106/111/134' => 'nombre',
    'specs/attrs/95/104' => 'années modèles',
    'specs/attrs/95/104/113' => 'de',
    'specs/attrs/95/104/114' => 'a',
    'specs/attrs/95/107' => "le début au salon de l'auto",
    'specs/attrs/95/107/118' => 'année',
    'specs/attrs/95/107/119' => 'mois',
    'specs/attrs/95/107/120' => 'nombre',
    'specs/attrs/95/108' => 'production',
    'specs/attrs/95/108/121' => 'de',
    'specs/attrs/95/108/121/123' => 'année',
    'specs/attrs/95/108/121/124' => 'mois',
    'specs/attrs/95/108/121/125' => 'nombre',
    'specs/attrs/95/108/122' => 'a',
    'specs/attrs/95/108/122/126' => 'année',
    'specs/attrs/95/108/122/127' => 'mois',
    'specs/attrs/95/108/122/128' => 'nombre',
    'specs/attrs/95/135' => 'participation à des compétitions',
    'specs/attrs/95/135/136' => 'de',
    'specs/attrs/95/135/137' => 'a',
    'specs/attrs/16' => 'basique',
    'specs/attrs/16/12' => 'nombre de places',
    'specs/attrs/16/12/67' => 'en tout',
    'specs/attrs/16/12/67/description' => 'sièges',
    'specs/attrs/16/12/68' => 'sont défectueux',
    'specs/attrs/16/12/69' => 'debout',
    'specs/attrs/16/12/103' => 'capacité totale',
    'specs/attrs/16/12/103/description' => 'en tenant compte des places debout',
    'specs/attrs/16/13' => 'nombre de portes',
    'specs/attrs/16/66' => 'Dispositif de direction',
    'specs/attrs/16/66/options/11' => 'à gauche',
    'specs/attrs/16/66/options/12' => 'à droite',
    'specs/attrs/16/66/options/13' => 'central',
    'specs/attrs/16/204' => 'conception',
    'specs/attrs/16/204/options/85' => 'coque',
    'specs/attrs/16/204/options/86' => 'châssis',
    'specs/attrs/14' => 'géométrie',
    'specs/attrs/14/4' => 'empattement',
    'specs/attrs/14/17' => 'dimensions',
    'specs/attrs/14/17/description' => 'Dimensions extérieures',
    'specs/attrs/14/17/1' => 'longueur',
    'specs/attrs/14/17/2' => 'largeur',
    'specs/attrs/14/17/3' => 'hauteur',
    'specs/attrs/14/17/140' => 'la largeur, en tenant compte des miroirs',
    'specs/attrs/14/17/141' => 'hauteur, compte tenu de rails',
    'specs/attrs/14/17/203' => 'hauteur avec toit ouvert',
    'specs/attrs/14/18' => 'voie',
    'specs/attrs/14/18/5' => 'hall',
    'specs/attrs/14/18/6' => 'arrière',
    'specs/attrs/14/63' => 'résistance aérodynamique',
    'specs/attrs/14/63/64' => 'frontal',
    'specs/attrs/14/63/65' => 'latérale',
    'specs/attrs/14/167' => 'garde au sol',
    'specs/attrs/14/167/description' => 'garde au sol',
    'specs/attrs/14/167/176' => 'min',
    'specs/attrs/14/167/7' => 'standard',
    'specs/attrs/14/167/168' => 'max',
    'specs/attrs/70' => 'poids',
    'specs/attrs/70/71' => 'sec',
    'specs/attrs/70/72' => 'vide',
    'specs/attrs/70/73' => 'brut',
    'specs/attrs/22' => 'moteur',
    'specs/attrs/22/100' => 'nom',
    'specs/attrs/22/98' => 'carburant',
    'specs/attrs/22/98/options/28' => 'essence',
    'specs/attrs/22/98/options/29' => 'hydrogène',
    'specs/attrs/22/98/options/30' => 'bioéthanol',
    'specs/attrs/22/98/options/31' => 'électricité',
    'specs/attrs/22/98/options/32' => 'gaz',
    'specs/attrs/22/98/options/33' => 'diesel',
    'specs/attrs/22/98/options/84' => 'Flex-fuel',
    'specs/attrs/22/98/options/36' => 'RON 66',
    'specs/attrs/22/98/options/37' => 'RON 70',
    'specs/attrs/22/98/options/38' => 'RON 72',
    'specs/attrs/22/98/options/39' => 'RON 76',
    'specs/attrs/22/98/options/40' => 'RON 78',
    'specs/attrs/22/98/options/41' => 'RON 80',
    'specs/attrs/22/98/options/42' => 'RON 92',
    'specs/attrs/22/98/options/43' => 'RON 93',
    'specs/attrs/22/98/options/44' => 'RON 95',
    'specs/attrs/22/98/options/45' => 'RON 98',
    'specs/attrs/22/98/options/34' => 'CNG',
    'specs/attrs/22/98/options/35' => 'LPG',
    'specs/attrs/22/19' => 'disposition',
    'specs/attrs/22/19/20' => 'disposition',
    'specs/attrs/22/19/20/options/1' => 'avant',
    'specs/attrs/22/19/20/options/2' => 'arrière',
    'specs/attrs/22/19/20/options/3' => 'centre',
    'specs/attrs/22/19/21' => 'orientation',
    'specs/attrs/22/19/21/options/4' => 'longitudinalement',
    'specs/attrs/22/19/21/options/5' => 'transversalement',
    'specs/attrs/22/23' => "Système d'alimentation",
    'specs/attrs/22/23/options/6' => 'injecteur',
    'specs/attrs/22/23/options/24' => 'carburateur',
    'specs/attrs/22/23/options/25' => '2 carburateurs',
    'specs/attrs/22/23/options/55' => '3 carburateurs',
    'specs/attrs/22/23/options/26' => '4 carburateurs',
    'specs/attrs/22/23/options/27' => '6 carburateurs',
    'specs/attrs/22/24' => 'cylindres / soupapes',
    'specs/attrs/22/24/25' => 'nombre de cylindres',
    'specs/attrs/22/24/26' => 'Dispositif de cylindres',
    'specs/attrs/22/24/26/options/7' => 'l',
    'specs/attrs/22/24/26/options/8' => 'V',
    'specs/attrs/22/24/26/options/9' => 'W',
    'specs/attrs/22/24/26/options/10' => 'O',
    'specs/attrs/22/24/26/options/101' => 'U',
    'specs/attrs/22/24/27' => 'soupapes par cylindre',
    'specs/attrs/22/24/28' => 'diamètre du cylindre',
    'specs/attrs/22/24/29' => 'course du piston',
    'specs/attrs/22/24/159' => "angle d'ouverture",
    'specs/attrs/22/30' => 'taux de compression',
    'specs/attrs/22/31' => 'volume',
    'specs/attrs/22/32' => 'puissance',
    'specs/attrs/22/32/33' => 'puissance',
    'specs/attrs/22/32/33/description' => 'Métrique (PS)
1 HP = 1.014 métrique PS ou CV',
    'specs/attrs/22/32/34' => 'dans la gamme de',
    'specs/attrs/22/32/35' => 'dans la gamme a',
    'specs/attrs/22/32/171' => 'puissance max (DIN)',
    'specs/attrs/22/32/171/description' => 'Puissance DIN 70020. Norme Européenne',
    'specs/attrs/22/32/172' => 'puissance max (SAE certified)',
    'specs/attrs/22/32/172/description' => 'SAE Certified puissance. Norme pour les états-UNIS avec 2005-06',
    'specs/attrs/22/32/173' => 'puissance power (SAE net)',
    'specs/attrs/22/32/173/description' => "Norme pour les états-UNIS avec 1971-72.
La transmission n'est pas pris en compte. Les pièces jointes compte",
    'specs/attrs/22/32/174' => 'max puissance (SAE gross)',
    'specs/attrs/22/32/174/description' => "La norme des états-unis de 1972, une technologie de mesure de la puissance jusqu'en 1972.
La transmission n'est pas pris en compte.",
    'specs/attrs/22/32/177' => 'max puissance (JIS D 1001)',
    'specs/attrs/22/32/178' => 'max puissance (GOST)',
    'specs/attrs/22/32/178/description' => "La norme de l'URSS et de la Russie",
    'specs/attrs/22/36' => 'сouple',
    'specs/attrs/22/36/37' => 'сouple',
    'specs/attrs/22/36/38' => 'dans la gamme de',
    'specs/attrs/22/36/39' => 'dans la gamme я',
    'specs/attrs/22/99' => 'turbo',
    'specs/attrs/22/99/options/46' => 'non',
    'specs/attrs/22/99/options/47' => 'il y a',
    'specs/attrs/22/99/options/48' => '×2',
    'specs/attrs/22/99/options/54' => '×3',
    'specs/attrs/22/99/options/49' => '×4',
    'specs/attrs/engine/turbo/options/x6' => '×6',
    'specs/attrs/22/156' => 'matériau de la culasse',
    'specs/attrs/22/156/options/68' => 'fonte',
    'specs/attrs/22/156/options/69' => "alliage d'aluminium",
    'specs/attrs/22/156/options/70' => 'acier',
    'specs/attrs/22/156/options/83' => 'alliage de magnésium',
    'specs/attrs/22/179' => 'refroidissement',
    'specs/attrs/22/179/options/81' => 'air',
    'specs/attrs/22/179/options/82' => 'liquid',
    'specs/attrs/22/179/options/liquid-air' => 'liquid-air',
    'specs/attrs/22/206' => 'Distribution',
    'specs/attrs/22/206/options/88' => 'Arbre à cames dans le bloc-cylindres',
    'specs/attrs/22/206/options/89' => 'Arbre à cames dans la culasse',
    'specs/attrs/22/206/options/90' => 'Commande desmodromique',
    'specs/attrs/22/206/options/91' => 'Distribution sans arbre à cames',
    'specs/attrs/22/206/options/92' => 'Moteur sans soupapes',
    'specs/attrs/22/206/options/93' => 'Moteur à soupapes latérales',
    'specs/attrs/22/206/options/94' => "Moteur avec un mélange de l'emplacement des soupapes",
    'specs/attrs/22/206/options/95' => 'Moteur à soupapes en tête',
    'specs/attrs/22/206/options/100' => 'Moteur à soupapes en tête avec un système de calage variable des soupapes',
    'specs/attrs/22/206/options/96' => 'ACT',
    'specs/attrs/22/206/options/97' => 'ACT avec un système de calage variable des soupapes',
    'specs/attrs/22/206/options/98' => 'DACT',
    'specs/attrs/22/206/options/99' => 'DACT avec un système de calage variable des soupapes',
    'specs/attrs/22/207' => 'type',
    'specs/attrs/22/207/options/102' => 'Machine à vapeur',
    'specs/attrs/22/207/options/103' => 'Moteur à combustion',
    'specs/attrs/22/207/options/104' => 'Moteur électrique',
    'specs/attrs/22/207/options/105' => 'Moteur à pistons',
    'specs/attrs/22/207/options/106' => 'Moteur à piston rotatif',
    'specs/attrs/22/207/options/107' => 'Turbine à gaz de combustion',
    'specs/attrs/40' => 'transmission',
    'specs/attrs/40/41' => 'traction',
    'specs/attrs/40/41/options/14' => 'avant',
    'specs/attrs/40/41/options/15' => 'arrière',
    'specs/attrs/40/41/options/16' => 'traction intégrale ',
    'specs/attrs/40/41/options/56' => 'avant sur une roue',
    'specs/attrs/40/41/options/57' => 'arrière sur une roue',
    'specs/attrs/40/41/options/17' => 'traction intégrale avec prise arrière',
    'specs/attrs/40/41/options/18' => 'traction intégrale avec prise avant',
    'specs/attrs/40/41/options/19' => 'traction intégrale permanente',
    'specs/attrs/40/42' => 'boîte de vitesses',
    'specs/attrs/40/42/43' => 'type',
    'specs/attrs/40/42/43/options/20' => 'Boîte manuelle',
    'specs/attrs/40/42/43/options/21' => 'Boîte automatique',
    'specs/attrs/40/42/43/options/22' => 'CVT',
    'specs/attrs/40/42/43/options/23' => 'Boîte mécanique pilotée',
    'specs/attrs/40/42/43/options/50' => 'Boîte robotisée',
    'specs/attrs/40/42/43/options/87' => 'Boîte séquentielle',
    'specs/attrs/40/42/43/options/51' => 'DSG',
    'specs/attrs/40/42/43/options/52' => 'DCT',
    'specs/attrs/40/42/139' => 'nom',
    'specs/attrs/40/42/44' => 'nombre de rapports ',
    'specs/attrs/40/83' => 'embrayage',
    'specs/attrs/15' => 'Suspension et direction',
    'specs/attrs/15/208' => 'suspension avant',
    'specs/attrs/15/208/209' => "types d'amortissement",
    'specs/attrs/15/208/209/options/108' => 'ressorts hélicoïdaux',
    'specs/attrs/15/208/209/options/109' => 'ressorts à lames',
    'specs/attrs/15/208/209/options/110' => 'pneumatique',
    'specs/attrs/15/208/209/options/111' => 'oléopneumatique',
    'specs/attrs/15/208/209/options/112' => 'barre de torsion',
    'specs/attrs/15/208/209/options/113' => 'sur des éléments élastiques en caoutchouc',
    'specs/attrs/15/208/209/options/178' => 'absent',
    'specs/attrs/15/208/209/options/114' => 'ressorts hélicoïdaux avec tige de poussée (Push-Rod)',
    'specs/attrs/15/208/209/options/115' => 'ressorts hélicoïdaux avec tige de traction (Pull-Rod)',
    'specs/attrs/15/208/209/options/116' => 'ressorts à lames transversaux',
    'specs/attrs/15/208/209/options/117' => 'ressorts à lames longitudinales',
    'specs/attrs/15/208/209/options/124' => 'barre de torsion avec tige de poussée (Push-Rod)',
    'specs/attrs/15/208/209/options/125' => 'barre de torsion avec tige de traction (Pull-Rod)',
    'specs/attrs/15/208/209/options/118' => 'ressorts à lames elliptiques longitudinales',
    'specs/attrs/15/208/209/options/119' => 'ressorts à lames ¾-elliptiques longitudinaux',
    'specs/attrs/15/208/209/options/120' => 'ressorts à lames ¼-elliptiques longitudinaux',
    'specs/attrs/15/208/209/options/121' => 'ressorts à lames semi-elliptiques longitudinaux',
    'specs/attrs/15/208/209/options/122' => 'ressorts à lames cantilever longitudinaux',
    'specs/attrs/15/208/209/options/123' => 'ressorts à lames équilibrage longitudinales',
    'specs/attrs/15/208/210' => 'types de suspension',
    'specs/attrs/15/208/210/options/126' => 'dépendant',
    'specs/attrs/15/208/210/options/127' => 'indépendante',
    'specs/attrs/15/208/210/options/128' => 'semidependent',
    'specs/attrs/15/208/210/options/129' => 'dépendant sans bras',
    'specs/attrs/15/208/210/options/130' => 'dépendant à bras longitudinaux',
    'specs/attrs/15/208/210/options/131' => 'dépendant (couple-tube ou couple-tige)',
    'specs/attrs/15/208/210/options/132' => 'indépendante avec des demi-essieux oscillants',
    'specs/attrs/15/208/210/options/133' => 'indépendante à bras longitudinaux',
    'specs/attrs/15/208/210/options/134' => 'indépendante Dubonnet',
    'specs/attrs/15/208/210/options/135' => 'indépendante à bras obliques',
    'specs/attrs/15/208/210/options/136' => 'indépendante à deux bras transversaux',
    'specs/attrs/15/208/210/options/137' => 'indépendante à bras multiples ',
    'specs/attrs/15/208/210/options/138' => 'indépendante bougie',
    'specs/attrs/15/208/210/options/139' => 'semidependent de Dion',
    'specs/attrs/15/208/210/options/140' => 'semidependent à bras tirés et barres de torsion',
    'specs/attrs/15/208/210/options/141' => 'dépendant à bras longitudinaux avec mécanisme de Watt',
    'specs/attrs/15/208/210/options/142' => 'dépendant à bras longitudinaux avec mécanisme de Scott-Russell',
    'specs/attrs/15/208/210/options/143' => 'dépendant à bras longitudinaux avec barre Panhard',
    'specs/attrs/15/208/210/options/144' => 'dépendant (couple-tube ou couple-tige) avec mécanisme de Watt',
    'specs/attrs/15/208/210/options/145' => 'dépendant (couple-tube ou couple-tige) avec mécanisme de Scott-Russell',
    'specs/attrs/15/208/210/options/146' => 'dépendant (couple-tube ou couple-tige) avec barre Panhard',
    'specs/attrs/15/208/210/options/147' => 'MacPherson',
    'specs/attrs/15/208/210/options/149' => 'semidependent à bras tirés et barres de torsion avec mécanisme de Watt',
    'specs/attrs/15/208/210/options/150' => 'semidependent à bras tirés et barres de torsion avec mécanisme de Scott-Russell',
    'specs/attrs/15/208/210/options/151' => 'semidependent à bras tirés et barres de torsion avec barre Panhard',
    'specs/attrs/15/208/210/options/148' => 'MacPherson à bras longitudinaux et transversaux',
    'specs/attrs/15/208/211' => 'amortisseurs',
    'specs/attrs/15/208/211/213' => 'disponibilité',
    'specs/attrs/15/208/211/214' => "nature de l'action",
    'specs/attrs/15/208/211/214/options/152' => 'à simple effet',
    'specs/attrs/15/208/211/214/options/153' => 'à double effet',
    'specs/attrs/15/208/211/215' => "principe de l'action",
    'specs/attrs/15/208/211/215/options/154' => 'à friction',
    'specs/attrs/15/208/211/215/options/155' => 'hydraulique',
    'specs/attrs/15/208/211/215/options/159' => 'oléopneumatique',
    'specs/attrs/15/208/211/215/options/156' => 'hydraulique à bras',
    'specs/attrs/15/208/211/215/options/157' => 'hydraulique bitube',
    'specs/attrs/15/208/211/215/options/158' => 'hydraulique monotube',
    'specs/attrs/15/208/211/216' => 'adjustability',
    'specs/attrs/15/208/212' => 'barre anti-roulis',
    'specs/attrs/15/8' => 'type de suspension avant (désuet)',
    'specs/attrs/15/9' => 'type de suspension arrière (désuet)',
    'specs/attrs/15/10' => 'type de direction',
    'specs/attrs/15/217' => 'suspension arrière',
    'specs/attrs/15/217/218' => "types d'amortissement",
    'specs/attrs/15/217/218/options/160' => 'ressorts hélicoïdaux',
    'specs/attrs/15/217/218/options/163' => 'ressorts à lames',
    'specs/attrs/15/217/218/options/172' => 'pneumatique',
    'specs/attrs/15/217/218/options/173' => 'oléopneumatique',
    'specs/attrs/15/217/218/options/174' => 'barre de torsion',
    'specs/attrs/15/217/218/options/177' => 'sur des éléments élastiques en caoutchouc',
    'specs/attrs/15/217/218/options/179' => 'absent',
    'specs/attrs/15/217/218/options/161' => 'ressorts hélicoïdaux avec tige de poussée (Push-Rod)',
    'specs/attrs/15/217/218/options/162' => 'ressorts hélicoïdaux avec tige de traction (Pull-Rod)',
    'specs/attrs/15/217/218/options/164' => 'ressorts à lames transversaux',
    'specs/attrs/15/217/218/options/165' => 'ressorts à lames longitudinales',
    'specs/attrs/15/217/218/options/166' => 'ressorts à lames elliptiques longitudinales',
    'specs/attrs/15/217/218/options/167' => 'ressorts à lames ¾-elliptiques longitudinales',
    'specs/attrs/15/217/218/options/168' => 'ressorts à lames ¼-elliptiques longitudinales',
    'specs/attrs/15/217/218/options/169' => 'ressorts à lames semi-elliptiques longitudinaux',
    'specs/attrs/15/217/218/options/170' => 'ressorts à lames cantilever longitudinaux',
    'specs/attrs/15/217/218/options/171' => 'ressorts à lames équilibrage longitudinales',
    'specs/attrs/15/217/218/options/175' => 'barre de torsion avec tige de poussée (Push-Rod)',
    'specs/attrs/15/217/218/options/176' => 'barre de torsion avec tige de traction (Pull-Rod)',
    'specs/attrs/15/217/219' => 'types de suspension',
    'specs/attrs/15/217/219/options/180' => 'dépendant',
    'specs/attrs/15/217/219/options/190' => 'indépendante',
    'specs/attrs/15/217/219/options/200' => 'semidependent',
    'specs/attrs/15/217/219/options/181' => 'dépendant sans bras',
    'specs/attrs/15/217/219/options/182' => 'dépendant à bras longitudinaux',
    'specs/attrs/15/217/219/options/186' => 'dépendant (couple-tube ou couple-tige)',
    'specs/attrs/15/217/219/options/183' => 'dépendant à bras longitudinaux avec mécanisme de Watt',
    'specs/attrs/15/217/219/options/184' => 'dépendant à bras longitudinaux avec mécanisme de Scott-Russell',
    'specs/attrs/15/217/219/options/185' => 'dépendant à bras longitudinaux avec barre Panhard',
    'specs/attrs/15/217/219/options/187' => 'dépendant (couple-tube ou couple-tige) avec mécanisme de Watt',
    'specs/attrs/15/217/219/options/188' => 'dépendant (couple-tube ou couple-tige) avec mécanisme de Scott-Russell',
    'specs/attrs/15/217/219/options/189' => 'dépendant (couple-tube ou couple-tige) avec barre Panhard',
    'specs/attrs/15/217/219/options/191' => 'indépendante avec des demi-essieux oscillants',
    'specs/attrs/15/217/219/options/192' => 'indépendante à bras longitudinaux',
    'specs/attrs/15/217/219/options/193' => 'indépendante Dubonnet',
    'specs/attrs/15/217/219/options/194' => 'indépendante à bras obliques',
    'specs/attrs/15/217/219/options/195' => 'indépendante à deux bras transversaux',
    'specs/attrs/15/217/219/options/196' => 'indépendante à bras multiples',
    'specs/attrs/15/217/219/options/197' => 'indépendante bougie',
    'specs/attrs/15/217/219/options/198' => 'MacPherson',
    'specs/attrs/15/217/219/options/199' => 'MacPherson à bras longitudinaux et transversaux',
    'specs/attrs/15/217/219/options/201' => 'semidependent De Dion',
    'specs/attrs/15/217/219/options/202' => 'semidependent à bras tirés et barres de torsion',
    'specs/attrs/15/217/219/options/203' => 'semidependent à bras tirés et barres de torsion avec mécanisme de Watt',
    'specs/attrs/15/217/219/options/204' => 'semidependent à bras tirés et barres de torsion avec mécanisme de Scott-Russell',
    'specs/attrs/15/217/219/options/205' => 'semidependent à bras tirés et barres de torsion avec barre Panhard',
    'specs/attrs/15/217/220' => 'amortisseurs',
    'specs/attrs/15/217/220/222' => 'disponibilité',
    'specs/attrs/15/217/220/223' => "nature de l'action",
    'specs/attrs/15/217/220/223/options/206' => 'à simple effet',
    'specs/attrs/15/217/220/223/options/207' => 'à double effet',
    'specs/attrs/15/217/220/224' => "principe de l'action",
    'specs/attrs/15/217/220/224/options/208' => 'à friction',
    'specs/attrs/15/217/220/224/options/209' => 'hydraulique',
    'specs/attrs/15/217/220/224/options/213' => 'oléopneumatique',
    'specs/attrs/15/217/220/224/options/210' => 'hydraulique à bras',
    'specs/attrs/15/217/220/224/options/211' => 'hydraulique bitube',
    'specs/attrs/15/217/220/224/options/212' => 'hydraulique monotube',
    'specs/attrs/15/217/220/225' => 'adjustability',
    'specs/attrs/15/217/221' => 'barre anti-roulis',
    'specs/attrs/74' => 'brake system',
    'specs/attrs/74/77' => 'ABS',
    'specs/attrs/74/142' => 'front brakes',
    'specs/attrs/74/142/75' => 'description',
    'specs/attrs/74/142/144' => 'type',
    'specs/attrs/74/142/144/options/58' => 'drum',
    'specs/attrs/74/142/144/options/59' => 'disc',
    'specs/attrs/74/142/146' => 'diameter',
    'specs/attrs/74/142/148' => 'thickness',
    'specs/attrs/74/142/150' => 'material',
    'specs/attrs/74/142/150/options/62' => 'metal',
    'specs/attrs/74/142/150/options/63' => 'carbon',
    'specs/attrs/74/142/150/options/64' => 'ceramics',
    'specs/attrs/74/142/152' => 'ventilated',
    'specs/attrs/74/142/153' => 'perforated',
    'specs/attrs/74/143' => 'rear brakes',
    'specs/attrs/74/143/76' => 'description',
    'specs/attrs/74/143/145' => 'type',
    'specs/attrs/74/143/145/options/60' => 'drum',
    'specs/attrs/74/143/145/options/61' => 'disc',
    'specs/attrs/74/143/147' => 'diameter',
    'specs/attrs/74/143/149' => 'thickness',
    'specs/attrs/74/143/151' => 'material',
    'specs/attrs/74/143/151/options/65' => 'metal',
    'specs/attrs/74/143/151/options/66' => 'carbon',
    'specs/attrs/74/143/151/options/67' => 'ceramics',
    'specs/attrs/74/143/154' => 'ventilated',
    'specs/attrs/74/143/155' => 'perforated',
    'specs/attrs/181' => 'electric',
    'specs/attrs/181/182' => 'onboard voltage',
    'specs/attrs/46' => 'dynamic properties',
    'specs/attrs/46/47' => 'max speed',
    'specs/attrs/46/180' => 'acceleration to 60 km/h',
    'specs/attrs/46/48' => 'acceleration to 100 km/h',
    'specs/attrs/46/175' => 'acceleration to 60 mph',
    'specs/attrs/46/49' => 'acceleration to 200 km/h',
    'specs/attrs/46/50' => 'acceleration to 300 km/h',
    'specs/attrs/46/51' => '400m run time',
    'specs/attrs/46/52' => '1000m run time',
    'specs/attrs/46/53' => 'speed limiter',
    'specs/attrs/46/160' => 'braking time at 100 km/h',
    'specs/attrs/46/161' => 'stopping distance from 100 km/h',
    'specs/attrs/54' => 'эксплуатационные характеристики',
    'specs/attrs/54/55' => 'approach angle',
    'specs/attrs/54/56' => 'departure angle',
    'specs/attrs/54/57' => 'fuel tank capacity',
    'specs/attrs/54/57/58' => 'main',
    'specs/attrs/54/57/59' => 'additional',
    'specs/attrs/54/60' => 'boot volume',
    'specs/attrs/54/60/61' => 'min',
    'specs/attrs/54/60/62' => 'max',
    'specs/attrs/54/78' => 'fuel consumption',
    'specs/attrs/54/78/183' => 'Unknown method',
    'specs/attrs/54/78/183/79' => 'city',
    'specs/attrs/54/78/183/80' => 'highway',
    'specs/attrs/54/78/183/81' => 'mixed',
    'specs/attrs/54/78/184' => 'ECE',
    'specs/attrs/54/78/184/185' => '90 km/h',
    'specs/attrs/54/78/184/186' => '120 km/h',
    'specs/attrs/54/78/184/187' => 'city',
    'specs/attrs/54/78/184/188' => 'combined',
    'specs/attrs/54/78/189' => 'EPA (to 2008)',
    'specs/attrs/54/78/189/190' => 'city',
    'specs/attrs/54/78/189/191' => 'highway',
    'specs/attrs/54/78/192' => 'EPA (from 2008)',
    'specs/attrs/54/78/192/193' => 'city',
    'specs/attrs/54/78/192/194' => 'highway',
    'specs/attrs/54/78/199' => 'EU 93/116/EC',
    'specs/attrs/54/78/199/200' => 'urban',
    'specs/attrs/54/78/199/201' => 'extra urban',
    'specs/attrs/54/78/199/202' => 'combined',
    'specs/attrs/54/78/199/202/description' => '36.8% urban + 63.2% extra urban',
    'specs/attrs/54/138' => '"start-stop" system',
    'specs/attrs/54/158' => 'capacity',
    'specs/attrs/54/205' => 'towing weight',
    'specs/attrs/54/226' => 'body volume',
    'specs/attrs/54/195' => 'spread',
    'specs/attrs/54/195/11' => 'diameter',
    'specs/attrs/54/195/11/description' => 'axis. 2×radius',
    'specs/attrs/54/195/196' => 'wall-to-wall',
    'specs/attrs/54/195/196/description' => '',
    'specs/attrs/54/195/197' => 'curb-to-curb',
    'specs/attrs/54/195/197/description' => '',
    'specs/attrs/54/198' => 'turns of steering wheel',
    'specs/attrs/84' => 'wheels and tyres',
    'specs/attrs/84/85' => 'front',
    'specs/attrs/84/85/87' => 'tyre width',
    'specs/attrs/84/85/90' => 'tyre height',
    'specs/attrs/84/85/88' => 'diameter',
    'specs/attrs/84/85/89' => 'rim width',
    'specs/attrs/84/85/162' => 'rim offset (ET)',
    'specs/attrs/84/86' => 'rear',
    'specs/attrs/84/86/91' => 'tyre width',
    'specs/attrs/84/86/94' => 'tyre height',
    'specs/attrs/84/86/92' => 'diameter',
    'specs/attrs/84/86/93' => 'rim width',
    'specs/attrs/84/86/163' => 'rim offset (ET)',
    'specs/attrs/84/164' => 'rims model',
    'specs/attrs/84/165' => 'rims material',
    'specs/attrs/84/165/options/78' => 'steel',
    'specs/attrs/84/165/options/79' => 'aluminum alloy (molding)',
    'specs/attrs/84/165/options/80' => 'aluminum alloy (forging)',
    'specs/attrs/82' => 'emissions',
    'specs/attrs/157' => 'emission standard',
    'specs/attrs/157/options/71' => 'Euro 1',
    'specs/attrs/157/options/72' => 'Euro 2',
    'specs/attrs/157/options/73' => 'Euro 3',
    'specs/attrs/157/options/74' => 'Euro 4',
    'specs/attrs/157/options/75' => 'Euro 5',
    'specs/attrs/157/options/76' => 'Euro 5+',
    'specs/attrs/157/options/77' => 'Euro 6',
    'specs/attrs/170' => 'production place',

    'specs/unit/1/abbr' => 'mm',
    'specs/unit/1/name' => 'millimeter',
    'specs/unit/2/abbr' => 'kg',
    'specs/unit/2/name' => 'kilogram',
    'specs/unit/3/abbr' => 'm',
    'specs/unit/3/name' => 'meter',
    'specs/unit/4/abbr' => 'cc',
    'specs/unit/4/name' => 'cubic centimeter',
    'specs/unit/5/abbr' => 'hp',
    'specs/unit/5/name' => 'horsepower',
    'specs/unit/6/abbr' => 'rpm',
    'specs/unit/6/name' => 'rotates per minute',
    'specs/unit/7/abbr' => 'Nm',
    'specs/unit/7/name' => 'Newton-meter',
    'specs/unit/8/abbr' => 'km/h',
    'specs/unit/8/name' => 'kilometers per hour',
    'specs/unit/9/abbr' => 's',
    'specs/unit/9/name' => 'seconds',
    'specs/unit/10/abbr' => '%',
    'specs/unit/10/name' => 'percent',
    'specs/unit/11/abbr' => '°',
    'specs/unit/11/name' => 'degree',
    'specs/unit/12/abbr' => 'l',
    'specs/unit/12/name' => 'liter',
    'specs/unit/13/abbr' => 'l/100km',
    'specs/unit/13/name' => 'liters per 100 kilometers',
    'specs/unit/14/abbr' => 'CO2 g/km',
    'specs/unit/14/name' => 'grams of CO2 per kilometer',
    'specs/unit/15/abbr' => '″',
    'specs/unit/15/name' => 'inch',
    'specs/unit/16/abbr' => 'y.',
    'specs/unit/16/name' => 'year',
    'specs/unit/17/abbr' => 'PS',
    'specs/unit/17/name' => 'Pferdestärke',
    'specs/unit/18/abbr' => 'kW',
    'specs/unit/18/name' => 'kilowatt',
    'specs/unit/19/abbr' => 'V',
    'specs/unit/19/name' => 'volt',
    'specs/unit/20/abbr' => 'm³',
    'specs/unit/20/name' => 'cubic meter',

    'telegram/info' => '[Telegram](https://telegram.org/) - is primarily a system of instant messaging, like whatsapp or viber.

In addition to the messaging and file transfer, there are many other possibilities, which will go to the description of a lot of time.

We only use two of them: a public groups and bots.

# Public group

There is an public english-language and russian-language groups for all site visitors.

Join them is very simple - just click the link and follow the instructions:

* [English-language](https://t.me/joinchat/AAAAAAvxJESUMQcUM-I5YA)
* [Russian-language](https://t.me/joinchat/AAAAAA0NvB5g7SEsWv61Rw)

# Bot

To make you WheelsAge surfing more convenient we just created a "bot" for [telegram](https://telegram.org/) app. We hope it could be useful and easy to use.

The bot name: [@autowp_bot](tg:msg).

In short, bot is a kind of telegram user, which can send you notifications from site as a messages.

Learn more about telegram bots: <https://core.telegram.org/bots/faq>

Currently supported features:
* Notifications about newly personal messages. [Details ...](#messages)
* Notifications about newly accepted pictures. [Details ...](#new)
* Notifications about newly uploaded (but still waiting for accept) pictures (this feature works only authorized users). [Details ...](#inbox)

## Bot commands

### Start: `/start`

By sending this command you will receive list of currently supported commands.

### <a name="messages"></a> Personal messages: `/messages`

Send `/messages on` for subscribe to notifications about new personal messages.

Send `/messages off` to unsubscribe.

### <a name="new"></a> New pictures: `/new`

Send `/new BMW` for subscribe to new photos of BMW.

Send `/new BMW` again to unsubscribe.

### <a name="inbox"></a> Inbox: `/inbox`

Authorization by `/me` is requried.

Send `/inbox BMW` for subscribe to notifications about new uploads to BMW.

Send `/inbox BMW` again to unsubscribe.

### Me (authorization): `/me`

This command allows you to associate themselves (telegram user) with an account on our site.

Send `/me` to receive instructions for autorization.

Send `/me 123456789` to recevice confirmation code.

Where 123456789 - is your account number, which you can find on your profile page. For example, [Juliano Scotini](/users/juliano-scotini) has number 17322

Confirmation code will be sent to you via [the private messaging system](/ng/account/messages?folder=system)

Send `/me 123456789 ХХХХХХХХХХХХХХХХ` to finish process of association your telegram account with out website user.

Where `ХХХХХХХХХХХХХХХХ` - code that will be sent to you via [the private messaging system](/ng/account/messages?folder=system)',

    'personal-message-dialog/title' => 'Send personal message',
    'personal-message-dialog/send' => 'send',
    'personal-message-dialog/sending' => 'sending ...',
    'personal-message-dialog/sent' => 'sent',
    'personal-message-dialog/cancel' => 'cancel',
    'personal-message-dialog/placeholder' => 'Message',

    'crop-dialog/title' => 'Cropper',
    'crop-dialog/close' => 'Close',
    'crop-dialog/select-all' => 'Select all',
    'crop-dialog/save' => 'Save changes',
    'crop-dialog/resolution-%s-aspect-%s' => '%s (aspect is about %s)',
    'crop-dialog/resolution-x-aspect-y' => '{resolution} (aspect is about {aspect})',

    'who-online/title' => 'Online',
    'who-online/refresh' => 'Refresh',
    'who-online/close' => 'Close',

    'picture-moder-vote/custom/title' => 'Custom reason',
    'picture-moder-vote/custom/sending' => 'Sending',
    'picture-moder-vote/custom/send' => 'Send',
    'picture-moder-vote/custom/cancel' => 'Cancel',
    'picture-moder-vote/custom/save' => 'Save as template',

    'moder/statistics/photos-with-copyrights' => 'Photos with copyrights',
    'moder/statistics/vehicles-with-4-or-more-photos' => 'Vehicles with 4 or more photos',
    'moder/statistics/specifications-values' => 'Specifications values',
    'moder/statistics/brand-logos' => 'Brand logotypes',
    'moder/statistics/from-years' => 'Years of begin production',
    'moder/statistics/from-and-to-years' => 'Years of begin and end production',
    'moder/statistics/from-and-to-years-and-months' => 'Years and months of begin and end production'
]);
