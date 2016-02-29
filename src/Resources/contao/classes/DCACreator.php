<?php namespace FModule;

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

use Contao\Backend;
use Contao\Config;
use Contao\Environment;
use Contao\Files;
use Contao\Input;
use Contao\Database;
use Contao\BackendUser;


/**
 * Class DCACreator
 * @package FModule
 */
class DCACreator
{


    public $modules = array();

    /**
     *
     */
    public function index()
    {


        if (TL_MODE == 'BE') {
            Config::getInstance();
            Environment::getInstance();
            Input::getInstance();
            BackendUser::getInstance();
            Database::getInstance();

            /**
             * Boot BE Modules
             */
            if (Database::getInstance()->tableExists('tl_fmodules')) {
                $logLanguage = $_SESSION['fm_language'] ? $_SESSION['fm_language'] : 'de';
                Backend::loadLanguageFile('tl_fmodules_language_pack', $logLanguage);
                $this->loadDynDCA();
                $this->setDynLanguagePack();
            }


        }

    }


    public function setDynLanguagePack()
    {

        if (!Input::get('do') && !in_array(Input::get('do'), $this->modules)) {
            return;
        }

        $languages = &$GLOBALS['TL_LANG']['tl_fmodules_language_pack'];

        foreach ($languages as $key => $value) {
            foreach ($this->modules as $module => $name) {
                if ($key == 'new') {
                    $GLOBALS['TL_LANG'][$module]['new'] = $value[0];
                    $GLOBALS['TL_LANG'][$module . '_data']['new'] = array(sprintf($value[1][0], $name), $value[1][1]);
                    continue;
                }

                if ($key == 'fm_legend') {
                    $GLOBALS['TL_LANG'][$module] = $value;
                    $GLOBALS['TL_LANG'][$module . '_data'] = $value;
                    continue;
                }

                $GLOBALS['TL_LANG'][$module][$key] = $value;
                $GLOBALS['TL_LANG'][$module . '_data'][$key] = $value;

            }
        }
    }

    /**
     * @return array
     */
    private function getModulesObj()
    {
        $db = Database::getInstance();
        $modulesDB = $db->prepare("SELECT * FROM tl_fmodules")->execute();
        $modules = [];

        while ($modulesDB->next()) {

            $module = [];
            $module['name'] = $modulesDB->row()['name'];
            $module['tablename'] = $modulesDB->row()['tablename'];
            $module['info'] = $modulesDB->row()['info'];
            $module['sorting'] = $modulesDB->row()['sorting'];
            $module['sortingType'] = $modulesDB->row()['sortingType'];
            $module['orderBy'] = $modulesDB->row()['orderBy'];
            $module['paletteBuilder'] = $modulesDB->row()['paletteBuilder'];
            $id = $modulesDB->row()['id'];
			
			//backwards compatible
			$orderBy = 'sorting';
			if( !$db->fieldExists('sorting','tl_fmodules_filters') )
			{
				$orderBy = 'id';
			}
			
            $fieldsDB = $db->prepare("SELECT * FROM tl_fmodules_filters WHERE pid = ? ORDER BY ".$orderBy."")->execute($id);
            $fields = [];

            while ($fieldsDB->next()) {
                $field = [];
                $field['type'] = $fieldsDB->row()['type'];
                $field['fieldID'] = $fieldsDB->row()['fieldID'];
                $field['title'] = $fieldsDB->row()['title'];
                $field['description'] = $fieldsDB->row()['description'];
                $field['dataFromTable'] = $fieldsDB->row()['dataFromTable'];
                $field['widgetTemplate'] = $fieldsDB->row()['widgetTemplate'];
                $field['isInteger'] = $fieldsDB->row()['isInteger'];
                $field['autoPage'] = $fieldsDB->row()['autoPage'];
                $field['addTime'] = $fieldsDB->row()['addTime'];
                $field['from_field'] = $fieldsDB->row()['from_field'];
                $field['to_field'] = $fieldsDB->row()['to_field'];
                $field['widget_type'] = $fieldsDB->row()['widget_type'];
                $field['evalCss'] = $fieldsDB->row()['evalCss'];
                $field['isMandatory'] = $fieldsDB->row()['isMandatory'];
                $field['fieldAppearance'] = $fieldsDB->row()['fieldAppearance'];
                $fields[] = $field;
            }

            $module['fields'] = $fields;
            $modules[] = $module;

        }

        return $modules;
    }

    /**
     * @param $moduleObj
     */
    private function initDCA($moduleObj)
    {
        /**
         * tablename
         */
        $tablename = $moduleObj['tablename'];

        if ($tablename == '') {
            return;
        }

        $this->modules[$tablename] = $moduleObj['name'];

        /**
         * parent
         */
        $dcaSettings = new DCAModuleSettings();
        $dcaSettings->init($tablename);
        $childname = $dcaSettings->getChildName();
        $modulename = substr($tablename, 3, strlen($tablename));
        $GLOBALS['BE_MOD']['fmodules'][$modulename] = $this->getBEMod($tablename, $childname);
        $GLOBALS['TL_DCA'][$tablename] = array(

            'config' => $dcaSettings->setConfig(),
            'list' => $dcaSettings->setList(),
            'palettes' => $dcaSettings->setPalettes($moduleObj),
            'subpalettes' => $dcaSettings->setSubPalettes(),
            'fields' => $dcaSettings->setFields($moduleObj['fields'])

        );
        $GLOBALS['TL_LANG']['MOD'][$modulename] = array($moduleObj['name'], $moduleObj['info']);
        $dcaSettings->createTable();

        /**
         * child
         */
        $dcaData = new DCAModuleData();
        $dcaData->init($childname, $tablename);
        $palette = $dcaData->setPalettes($moduleObj);
        $GLOBALS['TL_DCA'][$childname] = array(

            'config' => $dcaData->setConfig($moduleObj['detailPage']),
            'list' => $dcaData->setList($moduleObj),
            'palettes' => array(
                '__selector__' => $palette['__selector__'],
                'default' => $palette['default']
            ),
            'subpalettes' => $palette['subPalettes'],
            'fields' => $dcaData->setFields($moduleObj['fields'])
        );

        $modname = substr($tablename, 3, strlen($tablename));
        $GLOBALS['TL_PERMISSIONS'][] = $modname;
        $GLOBALS['TL_PERMISSIONS'][] = $modname . 'p';
        $dcaData->createTable();


    }


    /**
     *
     */
    private function getBEMod($tablename, $childname)
    {
        $icon = $GLOBALS['FM_AUTO_PATH'] . 'fmodule.png';
        $path = $this->getModuleIcon($tablename);

        if (is_string($path)) {
            $icon = $path;
        }

        return [
            'icon' => $icon,
            'tables' => array($tablename, $childname, 'tl_content')
        ];
    }

    /**
     * @param $tablename
     * @return bool|string
     */
    public function getModuleIcon($tablename)
    {

        $path = TL_ROOT . '/' . 'files/fmodule/assets/' . $tablename . '_icon';
        $file = Files::getInstance();
        $allowedFormat = array('gif', 'png', 'svg');

        if (!file_exists(TL_ROOT . '/' . 'files/fmodule')) {
            $file->mkdir('files/fmodule');
            $file->mkdir('files/fmodule/assets');
        }

        foreach($allowedFormat as $format)
        {
            if (file_exists($path.'.'.$format)) {
                return (version_compare(VERSION, '4.0', '>=') ? '../files/fmodule/assets/' : 'files/fmodule/assets/') . $tablename . '_icon'.'.'.$format;
            }
        }

        return false;

    }

    /**
     *
     */
    private function loadDynDCA()
    {
        foreach ($this->getModulesObj() as $moduleObj) {
            $this->initDCA($moduleObj);
        }
    }

}