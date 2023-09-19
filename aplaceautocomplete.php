<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Muesli tech team
 *  @copyright 2023 Muesli SASU
 *  @license   GNU General Public License version 3
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

class Aplaceautocomplete extends Module
{
    protected $config_form = false;
    private $defaultValues = [
        'APLACE_AUTOCOMPLETE_LIVE_MODE' => false,
        'APLACE_AUTOCOMPLETE_NAME_ADDRESS' => 'address1',
        'APLACE_AUTOCOMPLETE_NAME_CITY' => 'city',
        'APLACE_AUTOCOMPLETE_NAME_POSTCODE' => 'postcode',
        'APLACE_AUTOCOMPLETE_NAME_COUNTRY' => 'id_country',
        'APLACE_AUTOCOMPLETE_NAME_STATE' => 'id_state',
        'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES' => 'ps_country',
        'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES' => 'ps_state',
    ];

    public function __construct()
    {
        $this->name = 'aplaceautocomplete';
        $this->tab = 'shipping_logistics';
        $this->module_key = '894aa3d5fffb5e1c99d1dc75576c71b6';
        $this->version = '1.6.0';
        $this->author = 'Muesli';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('APlace Autocomplete');
        $this->description = $this->l('Easily add address autocompletion in your customers\' shopping cart form');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_MODULE_NAME', 'aplaceautocomplete_module')
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_LIVE_MODE', false)
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_INIT', false)
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_ADDRESS', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_ADDRESS'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_CITY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_CITY'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_POSTCODE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_POSTCODE'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_STATE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_STATE'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_COUNTRY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_COUNTRY'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES'])
        && $this->registerHook('header');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('APLACE_AUTOCOMPLETE_MODULE_NAME')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_LIVE_MODE')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_INIT')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_NAME_ADDRESS')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_NAME_CITY')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_NAME_POSTCODE')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_NAME_STATE')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_NAME_COUNTRY')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES')
        && Configuration::deleteByName('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES')
        && parent::uninstall();
    }

    public function reset()
    {
        return Configuration::updateValue('APLACE_AUTOCOMPLETE_MODULE_NAME', 'aplaceautocomplete_module')
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_LIVE_MODE', false)
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_INIT', false)
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_ADDRESS', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_ADDRESS'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_CITY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_CITY'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_POSTCODE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_POSTCODE'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_STATE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_STATE'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_NAME_COUNTRY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_COUNTRY'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES'])
        && Configuration::updateValue('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES'])
        && $this->registerHook('header');
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        Configuration::updateValue('APLACE_AUTOCOMPLETE_INIT', true);
    }

    protected function checkErrorInValues()
    {
        $form_values = $this->getConfigFormValues();
        $errors = $this->validateValues($form_values);
        if (count($errors) > 0) {
            $this->context->controller->errors = $errors;
        }
    }

    public function getContent()
    {
        if (((bool) Tools::isSubmit('submitAplaceautocompleteModule')) == true) {
            $this->postProcess();
        }
        if (Configuration::get('APLACE_AUTOCOMPLETE_INIT') === true) {
            $this->checkErrorInValues();
        }
        $this->context->smarty->assign('module_dir', $this->_path);

        $this->context->smarty->assign('apiKey', Configuration::get('APLACE_AUTOCOMPLETE_API_KEY'));
        $this->context->smarty->assign('encryptionKey', Configuration::get('APLACE_AUTOCOMPLETE_ENCRYPTION_KEY'));

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAplaceautocompleteModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your fields */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        $formContent = [];
        $buttons = [];
        $submit = [];
        $formContent = [
            [
                'type' => 'switch',
                'label' => $this->l('Live mode'),
                'name' => 'APLACE_AUTOCOMPLETE_LIVE_MODE',
                'is_bool' => true,
                'desc' => $this->l('Use this module in live mode'),
                'values' => [
                    [
                        'id' => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled'),
                    ],
                    [
                        'id' => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => $this->l('API key'),
                'name' => 'APLACE_AUTOCOMPLETE_API_KEY',
                'desc' => $this->l('Get your API key from https://aplace.io/en/dashboard/tokens'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Encryption key'),
                'name' => 'APLACE_AUTOCOMPLETE_ENCRYPTION_KEY',
                'desc' => $this->l('Get your Encryption key from https://aplace.io/en/dashboard/tokens'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Address field names'),
                'name' => 'APLACE_AUTOCOMPLETE_NAME_ADDRESS',
                'desc' => $this->l('Set the "name" attributes of the address fields in the forms where there is an address input (e.g. "address1"). You can set several values separated by a comma (e.g. "address1, address2").'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('City field names'),
                'name' => 'APLACE_AUTOCOMPLETE_NAME_CITY',
                'desc' => $this->l('Set the "name" attributes of the city fields in the forms where there is a city input (e.g. "city"). You can set several values separated by a comma (e.g. "city, city2")'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Post code field names'),
                'name' => 'APLACE_AUTOCOMPLETE_NAME_POSTCODE',
                'desc' => $this->l('Set the "name" attributes of the post code fields in the forms where there is a post code input (e.g. "postcode"). You can set several values separated by a comma (e.g. "postcode, postcode2")'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('State select name'),
                'name' => 'APLACE_AUTOCOMPLETE_NAME_STATE',
                'desc' => $this->l('Set the "name" attributes of the state fields in the forms where there is a state input (e.g. "id_state"). You can set several values separated by a comma (e.g. "id_state, id_state2")'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Country select name'),
                'name' => 'APLACE_AUTOCOMPLETE_NAME_COUNTRY',
                'desc' => $this->l('Set the "name" attributes of the country fields in the forms where there is a country input (e.g. "id_country"). You can set several values separated by a comma (e.g. "id_country, id_country2")'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Name of the Countries table'),
                'name' => 'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES',
                'desc' => $this->l('Table name in MySQL database (default: "ps_country")'),
                'size' => 20,
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Name of the States table'),
                'name' => 'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES',
                'desc' => $this->l('Table name in MySQL database (default: "ps_state")'),
                'size' => 20,
                'required' => true,
            ],
        ];
        $buttons = [
            [
                'type' => 'button',
                'target' => '_blank',
                'href' => ' https://aplace.io/en/dashboard/home',
                'class' => '',
                'name' => 'APLACE_AUTOCOMPLETE_DASHBOARD',
                'title' => $this->l('Aplace Dashboard'),
            ],
            [
                'type' => 'button',
                'target' => '_blank',
                'href' => ' https://aplace.io/en/dashboard/tokens',
                'class' => '',
                'name' => 'APLACE_AUTOCOMPLETE_DASHBOARD',
                'title' => $this->l('Generate API keys'),
            ],
        ];
        $submit = [
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
        ];

        $conf = [
            'form' => [
                'legend' => [
                    'title' => $this->l('APlace Address Autocomplete Settings'),
                ],
                'input' => $formContent,
                'buttons' => $buttons,
                'submit' => $submit,
            ],
        ];
        return $conf;
    }

    protected function getConfigFormValues()
    {
        return [
            'APLACE_AUTOCOMPLETE_LIVE_MODE' => Configuration::get('APLACE_AUTOCOMPLETE_LIVE_MODE', null),
            'APLACE_AUTOCOMPLETE_API_KEY' => Configuration::get('APLACE_AUTOCOMPLETE_API_KEY', null),
            'APLACE_AUTOCOMPLETE_ENCRYPTION_KEY' => Configuration::get('APLACE_AUTOCOMPLETE_ENCRYPTION_KEY', null),
            'APLACE_AUTOCOMPLETE_NAME_ADDRESS' => Configuration::get('APLACE_AUTOCOMPLETE_NAME_ADDRESS', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_ADDRESS']),
            'APLACE_AUTOCOMPLETE_NAME_CITY' => Configuration::get('APLACE_AUTOCOMPLETE_NAME_CITY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_CITY']),
            'APLACE_AUTOCOMPLETE_NAME_POSTCODE' => Configuration::get('APLACE_AUTOCOMPLETE_NAME_POSTCODE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_POSTCODE']),
            'APLACE_AUTOCOMPLETE_NAME_COUNTRY' => Configuration::get('APLACE_AUTOCOMPLETE_NAME_COUNTRY', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_COUNTRY']),
            'APLACE_AUTOCOMPLETE_NAME_STATE' => Configuration::get('APLACE_AUTOCOMPLETE_NAME_STATE', $this->defaultValues['APLACE_AUTOCOMPLETE_NAME_STATE']),
            'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES' => Configuration::get('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES']),
            'APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES' => Configuration::get('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES', $this->defaultValues['APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES']),
        ];
    }

    protected function validateValues($formContent)
    {
        // var_dump($formContent);
        // exit();
        $validationChecks = [
            'APLACE_AUTOCOMPLETE_LIVE_MODE' => [
                'type' => 'boolean',
                'name' => $this->l('Live mode'),
            ],
            'APLACE_AUTOCOMPLETE_API_KEY' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-fA-F0-9_\-]{32,}$/',
                'name' => $this->l('API key'),
            ],
            'APLACE_AUTOCOMPLETE_ENCRYPTION_KEY' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-fA-F0-9_\-]{32}$/',
                'name' => $this->l('Encryption key'),
            ],
            'APLACE_AUTOCOMPLETE_NAME_ADDRESS' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-zA-Z0-9_\-]{1,}$/',
                'name' => $this->l('Address field name'),
            ],
            'APLACE_AUTOCOMPLETE_NAME_CITY' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-zA-Z0-9_\-]{1,}$/',
                'name' => $this->l('City field name'),
            ],
            'APLACE_AUTOCOMPLETE_NAME_POSTCODE' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-zA-Z0-9_\-]{1,}$/',
                'name' => $this->l('Postcode field name'),
            ],
            'APLACE_AUTOCOMPLETE_NAME_COUNTRY' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-zA-Z0-9_\-]{1,}$/',
                'name' => $this->l('Country field name'),
            ],
            'APLACE_AUTOCOMPLETE_NAME_STATE' => [
                'type' => 'string',
                'required' => true,
                'regex' => '/^[a-zA-Z0-9_\-]{1,}$/',
                'name' => $this->l('State field name'),
            ],
        ];
        $errors = [];

        foreach ($validationChecks as $key => $validationCheck) {
            $value = $formContent[$key];
            $validationCheck = $validationChecks[$key];
            if ($validationCheck) {
                // set default value if empty
                if (empty($value)) {
                    $value = $this->defaultValues[$key];
                    Configuration::updateValue($key, $value);
                }  
            } else {
                $errors[] = $this->l('The field ') . $key . $this->l(' should not exist');
                continue;
            }
        }
        return $errors;
    }

    public function hookDisplayHeader()
    {
        if ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'address') {
            $liveMode = Configuration::get('APLACE_AUTOCOMPLETE_LIVE_MODE');
            if ($liveMode) {
                $aplaceToken = Configuration::get('APLACE_AUTOCOMPLETE_API_KEY');
                if (!$aplaceToken) {
                    return;
                }
                $this->context->controller->addCSS($this->_path . 'views/css/styles.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/script.js', 'all');
                // one hour
                $ttl = round(time() + 60 * 60);
                $messageData = $aplaceToken . '|' . $ttl;
                $aplaceEncryptionKey = Configuration::get('APLACE_AUTOCOMPLETE_ENCRYPTION_KEY');
                try {
                    $aes = new phpseclib3\Crypt\AES('gcm');
                    $aes->setKey($aplaceEncryptionKey);
                    $iv = phpseclib3\Crypt\Random::string(12);
                    $aes->setNonce($iv);
                    $encrypted = $aes->encrypt($messageData);
                    $combinedEncrypted = $iv . $encrypted . $aes->getTag();
                    $accessToken = urlencode(substr($aplaceToken, 0, 5) . base64_encode($combinedEncrypted));
                    $aplaceLang = 'en';
                    try {
                        $sqlLang = 'SELECT id_lang, iso_code FROM `' . _DB_PREFIX_ . 'lang`';
                        $langData = Db::getInstance()->executeS($sqlLang);
                        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
            
                        foreach ($langData as &$langValue) {
                            if ($defaultLang == $langValue['id_lang']) {
                                $aplaceLang = $langValue['iso_code'];
                            }
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                    if (method_exists($this->context->controller, 'registerJavascript')) {
                        $this->context->controller->registerJavascript(
                            'aplace-autocomplete',
                            'https://aplace.io/' . $aplaceLang . '/scripts/autocomplete.js?key=' . $accessToken . '&from=pss&v=' . $this->version,
                            [
                                'server' => 'remote',
                                'position' => 'bottom',
                                'priority' => 150,
                            ]
                        );
                    } else {
                        $this->context->controller->addJS('https://aplace.io/' . $aplaceLang . '/scripts/autocomplete.js?key=' . $accessToken . '&from=pss&v=' . $this->version);
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                $this->context->smarty->assign('aplace_autocomplete_field_address', Configuration::get('APLACE_AUTOCOMPLETE_NAME_ADDRESS'));
                $this->context->smarty->assign('aplace_autocomplete_field_city', Configuration::get('APLACE_AUTOCOMPLETE_NAME_CITY'));
                $this->context->smarty->assign('aplace_autocomplete_field_postcode', Configuration::get('APLACE_AUTOCOMPLETE_NAME_POSTCODE'));
                $this->context->smarty->assign('aplace_autocomplete_field_country', Configuration::get('APLACE_AUTOCOMPLETE_NAME_COUNTRY'));
                $this->context->smarty->assign('aplace_autocomplete_field_state', Configuration::get('APLACE_AUTOCOMPLETE_NAME_STATE'));
                try {
                    $sqlCountries = 'SELECT id_country, iso_code, active FROM `' . pSQL(Configuration::get('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_COUNTRIES')) . '`';
                    $countriesData = Db::getInstance()->executeS($sqlCountries);
                    if ($countriesData) {
                        $this->context->smarty->assign('countries_data', $countriesData);
                    } else {
                        $sqlCountries = 'SELECT id_country, iso_code, active FROM `' . _DB_PREFIX_ . 'country`';
                        $countriesData = Db::getInstance()->executeS($sqlCountries);
                        if ($countriesData) {
                            $this->context->smarty->assign('countries_data', $countriesData);
                        }                                
                    }
                    $sqlStates = 'SELECT id_state, name FROM `' . pSQL(Configuration::get('APLACE_AUTOCOMPLETE_DB_TABLE_NAME_STATES')) . '`';
                    $statesData = Db::getInstance()->ExecuteS($sqlStates);
                    if ($statesData) {
                        $this->context->smarty->assign('states_data', $statesData);
                    } else {
                        $sqlStates = 'SELECT id_state, name FROM `' . _DB_PREFIX_ . 'state`';
                        $statesData = Db::getInstance()->ExecuteS($sqlStates);
                        if ($statesData) {
                            $this->context->smarty->assign('states_data', $statesData);
                        }    
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                return $this->context->smarty->fetch($this->local_path . 'views/templates/front/vars.tpl');
            }
        }
        return '';
    }
}
