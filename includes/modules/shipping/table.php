<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Shipping_table extends osC_Shipping {
    var $icon;

    var $_title,
        $_code = 'table',
        $_status = false,
        $_sort_order;

// class constructor
    function osC_Shipping_table() {
      global $osC_Language;

      $this->icon = '';

      $this->_title = $osC_Language->get('shipping_table_title');
      $this->_description = $osC_Language->get('shipping_table_description');
      $this->_status = (defined('MODULE_SHIPPING_TABLE_STATUS') && (MODULE_SHIPPING_TABLE_STATUS == 'True') ? true : false);
      $this->_sort_order = (defined('MODULE_SHIPPING_TABLE_SORT_ORDER') ? MODULE_SHIPPING_TABLE_SORT_ORDER : null);
    }

// class methods
    function initialize() {
      global $osC_Database, $osC_ShoppingCart;

      $this->tax_class = MODULE_SHIPPING_TABLE_TAX_CLASS;

      if ( ($this->_status === true) && ((int)MODULE_SHIPPING_TABLE_ZONE > 0) ) {
        $check_flag = false;

        $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and zone_country_id = :zone_country_id order by zone_id');
        $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_TABLE_ZONE);
        $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getShippingAddress('country_id'));
        $Qcheck->execute();

        while ($Qcheck->next()) {
          if ($Qcheck->valueInt('zone_id') < 1) {
            $check_flag = true;
            break;
          } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getShippingAddress('zone_id')) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->_status = false;
        }
      }
    }

    function quote() {
      global $osC_Language, $osC_ShoppingCart, $osC_Weight;

      if (MODULE_SHIPPING_TABLE_MODE == 'price') {
        $order_total = $osC_ShoppingCart->getSubTotal();
      } else {
        $order_total = $osC_Weight->convert($osC_ShoppingCart->getWeight(), SHIPPING_WEIGHT_UNIT, MODULE_SHIPPING_TABLE_WEIGHT_UNIT);
      }

      $table_cost = split("[:,]" , MODULE_SHIPPING_TABLE_COST);
      $size = sizeof($table_cost);
      for ($i=0, $n=$size; $i<$n; $i+=2) {
        if ($order_total <= $table_cost[$i]) {
          $shipping = $table_cost[$i+1];
          break;
        }
      }

      if (MODULE_SHIPPING_TABLE_MODE == 'weight') {
        $shipping = $shipping * $osC_ShoppingCart->numberOfShippingBoxes();
      }

      $this->quotes = array('id' => $this->_code,
                            'module' => $this->_title,
                            'methods' => array(array('id' => $this->_code,
                                                     'title' => $osC_Language->get('shipping_table_method'),
                                                     'cost' => $shipping + MODULE_SHIPPING_TABLE_HANDLING)),
                            'tax_class_id' => $this->tax_class);

      if (!empty($this->icon)) $this->quotes['icon'] = osc_image($this->icon, $this->_title);

      return $this->quotes;
    }
  }
?>
