<?php

require_once 'hrcore.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function hrcore_civicrm_config(&$config) {
  _hrcore_civix_civicrm_config($config);
  $smarty = CRM_Core_Smarty::singleton();
  array_push($smarty->plugins_dir, __DIR__ . '/CRM/Smarty/plugins');
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function hrcore_civicrm_xmlMenu(&$files) {
  _hrcore_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function hrcore_civicrm_install() {
  _hrcore_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function hrcore_civicrm_uninstall() {
  _hrcore_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function hrcore_civicrm_enable() {
  _hrcore_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function hrcore_civicrm_disable() {
  _hrcore_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function hrcore_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _hrcore_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function hrcore_civicrm_managed(&$entities) {
  _hrcore_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function hrcore_civicrm_caseTypes(&$caseTypes) {
  _hrcore_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function hrcore_civicrm_angularModules(&$angularModules) {
  _hrcore_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function hrcore_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _hrcore_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_apiWrappers
 */
function hrcore_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if ($apiRequest['action'] === 'get') {
    $wrappers[] = new CRM_HRCore_APIWrapper_DefaultLimitRemover();
  }
}

/**
 * Implementation of hook_civicrm_pre hook.
 *
 * @param string $op
 *   Operation being done
 * @param string $objectName
 *   Name of the object on which the operation is being done
 * @param int $objectId
 *   ID of the record the object instantiates
 * @param array $params
 *   Parameter array being used to call the operation
 */
function hrcore_civicrm_pre($op, $objectName, $objectId, &$params) {
  $listeners = [
    new CRM_HRCore_Hook_Pre_PrimaryAddressSetter(),
  ];

  foreach ($listeners as $currentListener) {
    $currentListener->handle($op, $objectName, $objectId, $params);
  }
}

/**
 * Implements hrcore_civicrm_pageRun.
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_pageRun/
 */
function hrcore_civicrm_pageRun($page) {
  _hrcore_add_js_session_vars();
}

/**
 * This function adds the session variable to CRM.vars object.
 */
function _hrcore_add_js_session_vars() {
  CRM_Core_Resources::singleton()->addVars('session', [
    'contact_id' => CRM_Core_Session::getLoggedInContactID()
  ]);
}
