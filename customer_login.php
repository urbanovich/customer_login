<?php

/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*         DISCLAIMER   *
* *************************************** */

/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* *****************************************************
* @category   Belvg
* @package    customer_login.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

if(!defined('_PS_VERSION_'))
    exit;

class customer_login extends Module
{

    public function __construct()
    {
        $this->name = 'customer_login';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'BelVG';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Customer login');
        $this->description = $this->l('Customer login allow the administrator is authorized as a buyer.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('Customer login'))
            $this->warning = $this->l('No name provided');

    }

    public function install()
    {

        if (!parent::install()
            || !$this->installModuleTab('AdminCustomerLogin', $this->l('Login'), 0, 0, 0))
            return false;

        return true;
    }

    public function uninstall()
    {

        if (!parent::uninstall()
            || !$this->uninstallModuleTab('AdminCustomerLogin'))
            return false;

        return true;
    }

    /**
     * Add new page in back office
     *
     * @param type $class_name
     * @param type $tab_name
     * @param type $id_parent
     * @param type $position
     *
     * @return type
     */
    public function installModuleTab($class_name, $tab_name, $id_parent = 0, $position = 0, $active = 0)
    {

        $tab = new Tab();
        $tab->active = $active;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        $tab->id_parent = $id_parent;
        $tab->position = $position;
        $tab->module = $this->name;

        return $tab->save();
    }

    /**
     * Delete custom page of back office
     *
     * @param type $class_name
     *
     * @return type
     */
    public function uninstallModuleTab($class_name)
    {

        $id_tab = Tab::getIdFromClassName($class_name);

        if ($id_tab) {

            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }

        return false;
    }
}