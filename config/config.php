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

// path
$GLOBALS['FM_AUTO_PATH'] = 'system/modules/fmodule/assets/';

if ((version_compare(VERSION, '4.0', '>=') && !$GLOBALS['FM_NO_COMPOSER'] && $GLOBALS['FM_NO_COMPOSER'] != true)) {
    $GLOBALS['FM_AUTO_PATH'] = 'bundles/fmodule/';
}

// back end modules
$GLOBALS['BE_MOD']['system']['fmodule'] = array(
    'icon' => $GLOBALS['FM_AUTO_PATH'] . 'icon.png',
    'name' => 'F Module',
    'tables' => array(
        'tl_fmodules',
        'tl_fmodules_filters',
        'tl_fmodules_feed',
        'tl_fmodules_license'
    )
);
$GLOBALS['BE_MOD']['system']['taxonomy'] = array(
    'icon' => $GLOBALS['FM_AUTO_PATH'] . 'tag.png',
    'name' => 'Taxonomy',
    'tables' => array('tl_taxonomies')
);

// font end modules
array_insert($GLOBALS['FE_MOD'], 5, array(
    'fmodule' => array(
        'fmodule_fe_list' => 'ModuleListView',
        'fmodule_fe_detail' => 'ModuleDetailView',
        'fmodule_fe_formfilter' => 'ModuleFormFilter',
        'fmodule_fe_registration' => 'ModuleFModuleRegistration',
        'fmodule_fe_taxonomy' => 'ModuleFModuleTaxonomy'
    )
));

// widgets
$GLOBALS['BE_FFL']['optionWizardExtended'] = 'OptionWizardExtended';
$GLOBALS['BE_FFL']['modeSettings'] = 'ModeSettings';
$GLOBALS['BE_FFL']['filterFields'] = 'FilterFields';
$GLOBALS['BE_FFL']['keyValueWizardCustom'] = 'KeyValueWizardCustom';

// files
if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = $GLOBALS['FM_AUTO_PATH'] . 'stylesheet.css';
}

// google Maps
$GLOBALS['loadGoogleMapLibraries'] = false;

// hooks
$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('DCACreator', 'index');
$GLOBALS['TL_HOOKS']['postLogin'][] = array('FModule', 'setLanguage');
$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('FModule', 'getSearchablePages');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('FModule', 'createUserGroupDCA');
$GLOBALS['TL_HOOKS']['autoComplete'][] = array('FModule', 'getAutoCompleteAjax');
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('FModule', 'purgeOldFeeds');
$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = array('FModule', 'generateFeeds');
$GLOBALS['TL_HOOKS']['getPageIdFromUrl'][] = array('CleanUrls', 'getPageIdFromUrlStr');

// change language module hooks
$GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = array('FModuleTranslation', 'translateUrlParameters'); // v3
$GLOBALS['TL_HOOKS']['translateUrlParameters'][] = array('FModuleTranslation', 'translateUrlParametersBackwardsCompatible'); // backwards compatibility



// insertTags
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('FModuleInsertTags', 'setHooks');

// ajax
$GLOBALS['TL_MOOTOOLS'][] =
    "<script>
        if(typeof AjaxRequest != 'undefined')
        {
            AjaxRequest.toggleFMField = function(el)
            {
                el.blur();
                var image = $(el).getFirst('img');
                var href = $(el).get('href');
                var tempSrc = image.get('src');
                var src = image.get('data-src');

                var featured = (image.get('data-state') == 1);

		        if (!featured) {
                    image.src = src;
                    image.set('data-src', tempSrc);
                    image.set('data-state', 1);
                    new Request({'url': href}).get({'rt': Contao.request_token});
                } else {
                    image.src = src;
                    image.set('data-src', tempSrc);
                    image.set('data-state', 0);
                    new Request({'url': href}).get({'rt':Contao.request_token});
                }

                return false;
            }
        }
    </script>
    <script>
    	if(typeof Backend != 'undefined')
    	{
			Backend.keyValueWizardCustom = function(el, command, id) {
			var table = $(id),
				tbody = table.getElement('tbody'),
				parent = $(el).getParent('tr'),
				rows = tbody.getChildren(),
				tabindex = tbody.get('data-tabindex'),
				input, childs, i, j;

			Backend.getScrollOffset();

			switch (command) {
					case 'copy':
					var tr = new Element('tr');
					childs = parent.getChildren();
					for (i=0; i<childs.length; i++) {
						var next = childs[i].clone(true).inject(tr, 'bottom');
						if (input = childs[i].getFirst('input')) {
							next.getFirst().value = input.value;
						}
						if (select = childs[i].getFirst('select')) {
							next.getFirst('select').value = select.value;
						}
					}
					tr.inject(parent, 'after');
					$$(tr.getElement('.chzn-container')).destroy();
					$$(tr.getElement('.tl_select_column')).destroy();
					new Chosen(tr.getElement('select.tl_chosen'));
					Stylect.convertSelects();

					break;
				case 'up':
					if (tr = parent.getPrevious('tr')) {
						parent.inject(tr, 'before');
					} else {
						parent.inject(tbody, 'bottom');
					}
					break;
				case 'down':
					if (tr = parent.getNext('tr')) {
						parent.inject(tr, 'after');
					} else {
						parent.inject(tbody, 'top');
					}
					break;
				case 'delete':
					if (rows.length > 1) {
						parent.destroy();
					}
					break;
			}

			rows = tbody.getChildren();

			for (i=0; i<rows.length; i++) {
				childs = rows[i].getChildren();
				for (j=0; j<childs.length; j++) {
					if (input = childs[j].getFirst('input')) {
						input.set('tabindex', tabindex++);
						input.name = input.name.replace(/\[[0-9]+]/g, '[' + i + ']')
					}
					if (input = childs[j].getFirst('select')) {
						input.set('tabindex', tabindex++);
						input.name = input.name.replace(/\[[0-9]+]/g, '[' + i + ']')
					}
				}
			}

			new Sortables(tbody, {
				constrain: true,
				opacity: 0.6,
				handle: '.drag-handle'
			});
		}
    }
	</script>";

// permissions
$GLOBALS['TL_PERMISSIONS'][] = 'fmodules';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesp';

$GLOBALS['TL_PERMISSIONS'][] = 'taxonomies';
$GLOBALS['TL_PERMISSIONS'][] = 'taxonomiesp';

$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfeed';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfeedp';

$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfilters';
$GLOBALS['TL_PERMISSIONS'][] = 'fmodulesfiltersp';

// proSearch
$GLOBALS['PS_SEARCHABLE_MODULES']['fmodule'] = array(
    'tables' => array('tl_fmodules', 'tl_fmodules_filters'),
    'searchIn' => array('name', 'tablename', 'info', 'title', 'type', 'fieldID'),
    'title' => array('name', 'title'),
    'setCustomIcon' => array(array('ProSearchApi', 'setCustomIcon')),
    'setCustomShortcut' => array(array('ProSearchApi', 'setCustomShortcut'))
);

// wrapper
$GLOBALS['TL_WRAPPERS']['start'][] = 'legend_start';
$GLOBALS['TL_WRAPPERS']['stop'][] = 'legend_end';

//
if (TL_MODE == 'FE')
{
    $validSums = new FModule\FModule();
    $strLicense = Contao\Config::get('fmodule_license');
    if (!isset($strLicense) || !in_array(md5($strLicense), $validSums->validSums, true)) {
        $GLOBALS['TL_HEAD'][] = '<link title="F Module | Buy license" rel="license" href="http://fmodul.alexandernaumov.de/kaufen.html" />';
        $GLOBALS['TL_HEAD'][] = '<link title="F Module | Documentation" rel="help" href="http://fmodul.alexandernaumov.de/ressourcen.html" />';
    }
}