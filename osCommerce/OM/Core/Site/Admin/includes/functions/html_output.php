<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * Generate an internal URL address for the administration side
 *
 * @param string $page The page to link to
 * @param string $parameters The parameters to pass to the page (in the GET scope)
 * @access public
 */

  function osc_href_link_admin($page = null, $parameters = null) {
    $link = OSCOM::getLink('Admin', $parameters);
    return $link;
  }

/**
 * Display an icon from a template set
 *
 * @param string $image The icon to display
 * @param string $title The title of the icon
 * @param string $group The size group of the icon
 * @param string $parameters The parameters to pass to the image
 * @access public
 */

  function osc_icon($image, $title = null, $group = null, $parameters = null) {
    if ( is_null($title) ) {
      $title = OSCOM::getDef('icon_' . substr($image, 0, strpos($image, '.')));
    }

    if ( is_null($group) ) {
      $group = '16x16';
    }

    return osc_image(OSCOM::getPublicSiteLink('templates/' . Registry::get('Template')->getCode() . '/images/icons/' . (!empty($group) ? $group . '/' : null) . $image), $title, null, null, $parameters);
  }

/**
 * Get the raw URL to an icon from a template set
 *
 * @param string $image The icon to display
 * @param string $group The size group of the icon
 * @access public
 */

  function osc_icon_raw($image, $group = '16x16') {
    return 'public/sites/Admin/templates/' . Registry::get('Template')->getCode() . '/images/icons/' . (!empty($group) ? $group . '/' : null) . $image;
  }

////
// javascript to dynamically update the states/provinces list when the country is changed
// TABLES: zones
  function osc_js_zone_list($country, $form, $field) {
    $OSCOM_Database = Registry::get('Database');

    $num_country = 1;
    $output_string = '';

    $Qcountries = $OSCOM_Database->query('select distinct zone_country_id from :table_zones order by zone_country_id');
    $Qcountries->execute();

    while ( $Qcountries->next() ) {
      if ( $num_country == 1 ) {
        $output_string .= '  if (' . $country . ' == "' . $Qcountries->valueInt('zone_country_id') . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $Qcountries->valueInt('zone_country_id') . '") {' . "\n";
      }

      $num_state = 1;

      $Qzones = $OSCOM_Database->query('select zone_name, zone_id from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('zone_country_id'));
      $Qzones->execute();

      while ( $Qzones->next() ) {
        if ( $num_state == '1' ) {
          $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . OSCOM::getDef('all_zones') . '", "");' . "\n";
        }

        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $Qzones->value('zone_name') . '", "' . $Qzones->valueInt('zone_id') . '");' . "\n";

        $num_state++;
      }

      $num_country++;
    }

    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . OSCOM::getDef('all_zones') . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }
?>
