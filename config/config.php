<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   F Modul
 * @author    Alexander Naumov http://www.alexandernaumov.de
 * @license   commercial
 * @copyright 2016 Alexander Naumov
 */

$GLOBALS['FM_AUTO_PATH'] = 'system/modules/fmodule/assets/';

if ( ( version_compare( VERSION, '4.0', '>=' ) && !$GLOBALS['FM_NO_COMPOSER'] && $GLOBALS['FM_NO_COMPOSER'] != true ) ) {

    $GLOBALS['FM_AUTO_PATH'] = 'bundles/fmodule/';
}


$GLOBALS['BE_MOD']['system']['fmodule'] = [

    'icon' => $GLOBALS['FM_AUTO_PATH'] . 'icon.png',
    'name' => 'F Module',

    'tables' => [

        'tl_fmodules',
        'tl_fmodules_filters',
        'tl_fmodules_feed',
        'tl_fmodules_license'
    ]
];
$GLOBALS['BE_MOD']['system']['taxonomy'] = [

    'icon' => $GLOBALS['FM_AUTO_PATH'] . 'tag.png',
    'name' => 'Taxonomy',
    'tables' => [ 'tl_taxonomies' ]
];


array_insert($GLOBALS['FE_MOD'], 5, [

    'fmodule' => [

        'fmodule_fe_list' => 'ModuleListView',
        'fmodule_fe_detail' => 'ModuleDetailView',
        'fmodule_fe_formfilter' => 'ModuleFormFilter',
        'fmodule_fe_taxonomy' => 'ModuleFModuleTaxonomy',
        'fmodule_fe_registration' => 'ModuleFModuleRegistration'
    ]
]);

$GLOBALS['BE_FFL']['modeSettings'] = 'ModeSettings';
$GLOBALS['BE_FFL']['filterFields'] = 'FilterFields';
$GLOBALS['BE_FFL']['optionWizardExtended'] = 'OptionWizardExtended';
$GLOBALS['BE_FFL']['keyValueWizardCustom'] = 'KeyValueWizardCustom';
$GLOBALS['BE_FFL']['catalogOrderByWizard'] = 'FModuleOrderByWizard';


if (TL_MODE == 'BE') {

    $GLOBALS['TL_CSS'][] = $GLOBALS['FM_AUTO_PATH'] . 'stylesheet.css';
}

$GLOBALS['loadGoogleMapLibraries'] = false;

$GLOBALS['TL_HOOKS']['postLogin'][] = [ 'FModule', 'setLanguage' ];
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = [ 'FModule', 'purgeOldFeeds' ];
$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = [ 'FModule', 'generateFeeds' ];
$GLOBALS['TL_HOOKS']['initializeSystem'][] = [ 'Initialize', 'getClasses' ];
$GLOBALS['TL_HOOKS']['autoComplete'][] = [ 'FModule', 'getAutoCompleteAjax' ];
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = ['FModule', 'createUserGroupDCA' ];
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = [ 'FModule', 'getSearchablePages' ];
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = [ 'CleanUrls', 'getPageIdFromUrlStr' ];

$GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = [ 'FModuleTranslation', 'translateUrlParameters' ];
$GLOBALS['TL_HOOKS']['translateUrlParameters'][] = [ 'FModuleTranslation', 'translateUrlParametersBackwardsCompatible' ];

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [ 'FModuleInsertTags', 'setHooks' ];

if ( TL_MODE == 'BE' ) {

    $GLOBALS['TL_JAVASCRIPT']['FModuleJS'] = $GLOBALS['TL_CONFIG']['debugMode']
        ? $GLOBALS['FM_AUTO_PATH'] . 'FModule.js'
        : $GLOBALS['FM_AUTO_PATH'] . 'FModule.js';
}

$GLOBALS['TL_PERMISSIONS'][] = 'fmodules';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesp';

$GLOBALS['TL_PERMISSIONS'][] = 'taxonomies';
$GLOBALS['TL_PERMISSIONS'][] = 'taxonomiesp';

$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfeed';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfeedp';

$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfilters';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfiltersp';


$GLOBALS['PS_SEARCHABLE_MODULES']['fmodule'] = [

    'title' => [ 'name', 'title' ],
    'tables' => [ 'tl_fmodules', 'tl_fmodules_filters' ],
    'setCustomIcon' => [ [ 'ProSearchApi', 'setCustomIcon' ] ],
    'setCustomShortcut' => [ [ 'ProSearchApi', 'setCustomShortcut' ] ],
    'searchIn' => [ 'name', 'tablename', 'info', 'title', 'type', 'fieldID' ]
];

$GLOBALS['TL_WRAPPERS']['start'][] = 'legend_start';
$GLOBALS['TL_WRAPPERS']['stop'][] = 'legend_end';


if (TL_MODE == 'FE') {

    $arrValidSums = array(

        'ace73f5761f137a394516b1c4e4e2d9f',
        'ed34fd458b6bc5445f449118fb4e538c',
        '5b5739c14b95ed61acaad8470d9a127a',
        'b4694b9f70c86e72e77b730d5a99c66e',
        '88a60c2ab6be50b7de232bc63ea0eb57',
        '9171af5b32cd85d30992b37dc2ec7e04',
        '45648e4e598aa9526370e8a4c17b6be9',
        'fd9b1a37dd21c8d554deb27aa3986e75',
        'a633005b52284d23fc84d993670cb679',
        '7c386d224477717a261e1189ce732f1d',
        '61522559db862e895c1d99f5fe6b57bf',
        'dda5b1de99c476d3e8891ffce5a05bea',
        '9d815fe15d183e69e72d069a8d5f2700',
        'd12c7a51abfd6c11eadfad9bc16ea75d',
        '0c717d749710a9f81c01d7ce0eed5f84',
        'aa427e7a22231e8970e5d4d81a3ef193',
        'aa0ce817cd4a954e44cf7b55c3d47440',
        '2297d4d539db8cb975ebc6dc0a1e1e45',
        'dd1885c7d95d00343bb3edc3d8490563',
        '952a9a390aab497cd595dd2df576185c',
        'da1548a6c36aacff29cc5c8ccc3eab04',
        'dae0479dedb21ec54ac72a3e8d010025',
        '36d8a0457be757b18390d824bde23c6a',
        'af4846d50d1bf211dcfe7ff6d2bdc075',
        '5798e05453f4c36a0fc29fdbe3b5f712',
        '6545cf85edcebe8021454500f447ffe4',
        'ce54d8b93763d2bb0c740eafa27fa2c6',
        '96f66989b6b1aaf165b7ec9babdca73a',
        '0db41153e571f068c23d2bb8aa75cdc7',
        'e9bc100319609f53978b57a000826fd4',
        'dcaf3b7b556c50b9833be71f84de1745',
        'c60d176c44c566f7ccc7dc2954d7c81d',
        '6f897c8c297273784027285265ed37ee',
        'e56dfc9fcaaa275dda065e170e0334a8',
        '657b3fc877b61f6000a788dd84ade55e',
        '4eb3301c6ab1804170b91886177cf8ee',
        'b69ff655d208e345dccab9cdb2104b9e',
        'df60957c8ccf1e0c09da5f2971ae1c3f',
        'efa17853bd56a40d650f919542163eb1',
        'c06b0936ef1cfe5ef33b98766337fe28',
        'f118697ed80da1280982bc0f8d3de87a',
        '163d3a51d03f030b42907aedb555529f',
        '748248bcf59261ee660cd60d5a577f7b',
        '66e710a17d8f97547b3606ebf6a793b0',
        'c0482b203d6f8d5c11fcd6b80b170549',
        '5827b7b948dc7408bfef9685c016e800',
        '02d45cbbf7e47822d73ebf1801af7969',
        'e1bacf40933324da2fd7494ff92b8bb9',
        'bff143674c71fe88301cf48ec092bfec',
        '6063d5b265ea1bc0a714f5b957004868',
    );

    $strLicense = Contao\Config::get('fmodule_license');

    if ( !isset( $strLicense ) || !in_array( md5( $strLicense ), $arrValidSums, true ) ) {

        $GLOBALS['TL_HEAD'][] = '<link title="F Module | Buy license" rel="license" href="http://fmodul.alexandernaumov.de/kaufen.html" />';
        $GLOBALS['TL_HEAD'][] = '<link title="F Module | Documentation" rel="help" href="http://fmodul.alexandernaumov.de/ressourcen.html" />';
    }
}