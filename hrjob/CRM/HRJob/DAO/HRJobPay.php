<?php
/*
+--------------------------------------------------------------------+
| CiviHR version 1.4                                                 |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2014                                |
+--------------------------------------------------------------------+
| This file is a part of CiviCRM.                                    |
|                                                                    |
| CiviCRM is free software; you can copy, modify, and distribute it  |
| under the terms of the GNU Affero General Public License           |
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
*/
/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * Generated from xml/schema/CRM/HRJob/HRJobPay.xml
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_HRJob_DAO_HRJobPay extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_hrjob_pay';
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   * @static
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   * @static
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   * @static
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   * @static
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = true;
  /**
   * Unique HRJobPay ID
   *
   * @var int unsigned
   */
  public $id;
  /**
   * FK to Job
   *
   * @var int unsigned
   */
  public $job_id;
  /**
   * NJC pay scale, JNC pay scale, Soulbury Pay Agreement
   *
   * @var string
   */
  public $pay_scale;
  /**
   * Paid, Unpaid, etc
   *
   * @var string
   */
  public $pay_grade;
  /**
   * Amount of currency paid for each unit of work (eg 40 per hour, 400 per day)
   *
   * @var float
   */
  public $pay_amount;
  /**
   * Unit for expressing pay rate (e.g. amount per hour, amount per week)
   *
   * @var string
   */
  public $pay_unit;
  /**
   * Unit for expressing pay currency
   *
   * @var string
   */
  public $pay_currency;
  /**
   * Estimated Annual Pay
   *
   * @var float
   */
  public $pay_annualized_est;
  /**
   * Is the estimate automatically calculated
   *
   * @var boolean
   */
  public $pay_is_auto_est;
  /**
   * class constructor
   *
   * @access public
   * @return civicrm_hrjob_pay
   */
  function __construct()
  {
    $this->__table = 'civicrm_hrjob_pay';
    parent::__construct();
  }
  /**
   * return foreign keys and entity references
   *
   * @static
   * @access public
   * @return array of CRM_Core_Reference_Interface
   */
  static function getReferenceColumns()
  {
    if (!self::$_links) {
      self::$_links = array(
        new CRM_Core_Reference_Basic(self::getTableName() , 'job_id', 'civicrm_hrjob', 'id') ,
      );
    }
    return self::$_links;
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Job Pay Id') ,
          'required' => true,
        ) ,
        'job_id' => array(
          'name' => 'job_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Job Id') ,
          'required' => true,
          'FKClassName' => 'CRM_HRJob_DAO_HRJob',
        ) ,
        'hrjob_pay_scale' => array(
          'name' => 'pay_scale',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Pay Scale') ,
          'maxlength' => 63,
          'size' => CRM_Utils_Type::BIG,
          'import' => true,
          'where' => 'civicrm_hrjob_pay.pay_scale',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => true,
          'pseudoconstant' => array(
            'optionGroupName' => 'hrjob_pay_scale',
          )
        ) ,
        'hrjob_pay_grade' => array(
          'name' => 'pay_grade',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Paid / Unpaid') ,
          'maxlength' => 63,
          'size' => CRM_Utils_Type::BIG,
          'import' => true,
          'where' => 'civicrm_hrjob_pay.pay_grade',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => true,
          'pseudoconstant' => array(
            'optionGroupName' => 'hrjob_pay_grade',
          )
        ) ,
        'hrjob_pay_amount' => array(
          'name' => 'pay_amount',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Pay Amount') ,
          'import' => true,
          'where' => 'civicrm_hrjob_pay.pay_amount',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => true,
        ) ,
        'hrjob_pay_unit' => array(
          'name' => 'pay_unit',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Pay Unit') ,
          'maxlength' => 63,
          'size' => CRM_Utils_Type::BIG,
          'import' => true,
          'where' => 'civicrm_hrjob_pay.pay_unit',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => true,
          'pseudoconstant' => array(
            'callback' => 'CRM_HRJob_SelectValues::payUnit',
          )
        ) ,
        'hrjob_pay_currency' => array(
          'name' => 'pay_currency',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Pay Currency') ,
          'maxlength' => 63,
          'size' => CRM_Utils_Type::BIG,
          'import' => true,
          'where' => 'civicrm_hrjob_pay.pay_currency',
          'headerPattern' => '',
          'dataPattern' => '',
          'export' => true,
          'pseudoconstant' => array(
            'optionGroupName' => 'currencies_enabled',
          )
        ) ,
        'hrjob_pay_annualized_est' => array(
          'name' => 'pay_annualized_est',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Estimated Annual Pay') ,
          'export' => true,
          'where' => 'civicrm_hrjob_pay.pay_annualized_est',
          'headerPattern' => '',
          'dataPattern' => '',
        ) ,
        'pay_is_auto_est' => array(
          'name' => 'pay_is_auto_est',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'title' => ts('Estimated Auto Pay') ,
          'default' => '1',
        ) ,
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'job_id' => 'job_id',
        'pay_scale' => 'hrjob_pay_scale',
        'pay_grade' => 'hrjob_pay_grade',
        'pay_amount' => 'hrjob_pay_amount',
        'pay_unit' => 'hrjob_pay_unit',
        'pay_currency' => 'hrjob_pay_currency',
        'pay_annualized_est' => 'hrjob_pay_annualized_est',
        'pay_is_auto_est' => 'pay_is_auto_est',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the names of this table
   *
   * @access public
   * @static
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * returns the list of fields that can be imported
   *
   * @access public
   * return array
   * @static
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['hrjob_pay'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['hrjob_pay'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}
