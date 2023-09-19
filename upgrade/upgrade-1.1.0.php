<?php
/**
* 2007-2023 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_1_1_0($module)
{
    $defaultValues = [
        'APLACE_AUTOCOMPLETE_NAME_ADDRESS' => 'address1',
        'APLACE_AUTOCOMPLETE_NAME_CITY' => 'city',
        'APLACE_AUTOCOMPLETE_NAME_POSTCODE' => 'postcode',
        'APLACE_AUTOCOMPLETE_NAME_COUNTRY' => 'id_country',
        'APLACE_AUTOCOMPLETE_NAME_STATE' => 'id_state',
        'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES' => 'ps_country',
        'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES' => 'ps_state',
    ];
    Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_ADDRESS', $defaultValues['APLACE_AUTOCOMPLETE_NAME_ADDRESS']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_CITY', $defaultValues['APLACE_AUTOCOMPLETE_NAME_CITY']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_POSTCODE', $defaultValues['APLACE_AUTOCOMPLETE_NAME_POSTCODE']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_STATE', $defaultValues['APLACE_AUTOCOMPLETE_NAME_STATE']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_COUNTRY', $defaultValues['APLACE_AUTOCOMPLETE_NAME_COUNTRY']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES', $defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES']);
    Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES', $defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES']);

    return true;
}
