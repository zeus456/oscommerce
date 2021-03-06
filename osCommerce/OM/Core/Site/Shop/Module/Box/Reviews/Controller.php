<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Reviews;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Reviews',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_reviews_heading');
    }

    public function initialize() {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Cache = Registry::get('Cache');
      $OSCOM_Product = ( Registry::exists('Product') ) ? Registry::get('Product') : null;
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Image = Registry::get('Image');

      $this->_title_link = OSCOM::getLink(null, 'Products', 'Reviews');

      if ( $OSCOM_Service->isStarted('Reviews') ) {
        if ( (BOX_REVIEWS_CACHE > 0) && $OSCOM_Cache->read('box-reviews' . (isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ? '-' . $OSCOM_Product->getID() : '') . '-' . $OSCOM_Language->getCode(), BOX_REVIEWS_CACHE) ) {
          $data = $OSCOM_Cache->getCache();
        } else {
          $data = array();

          $Qreview = $OSCOM_Database->query('select r.reviews_id, r.reviews_rating, p.products_id, pd.products_name, pd.products_keyword, i.image from :table_reviews r, :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd where r.products_id = p.products_id and p.products_status = 1 and r.languages_id = :language_id and p.products_id = pd.products_id and pd.language_id = :language_id and r.reviews_status = 1');
          $Qreview->bindInt(':default_flag', 1);
          $Qreview->bindInt(':language_id', $OSCOM_Language->getID());
          $Qreview->bindInt(':language_id', $OSCOM_Language->getID());

          if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) {
            $Qreview->appendQuery('and p.products_id = :products_id');
            $Qreview->bindInt(':products_id', $OSCOM_Product->getID());
          }

          $Qreview->appendQuery('order by r.reviews_id desc limit :max_random_select_reviews');
          $Qreview->bindInt(':max_random_select_reviews', BOX_REVIEWS_RANDOM_SELECT);
          $Qreview->executeRandomMulti();

          if ( $Qreview->numberOfRows() ) {
            $Qtext = $OSCOM_Database->query('select substring(reviews_text, 1, 60) as reviews_text from :table_reviews where reviews_id = :reviews_id and languages_id = :languages_id');
            $Qtext->bindInt(':reviews_id', $Qreview->valueInt('reviews_id'));
            $Qtext->bindInt(':languages_id', $OSCOM_Language->getID());
            $Qtext->execute();

            $data = array_merge($Qreview->toArray(), $Qtext->toArray());
          }

          $OSCOM_Cache->write($data);
        }

        $this->_content = '';

        if ( empty($data) ) {
          if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) {
            $this->_content = '<div style="float: left; width: 55px;">' . osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&Write&' . $OSCOM_Product->getKeyword()), osc_image(DIR_WS_IMAGES . 'box_write_review.gif', OSCOM::getDef('button_write_review'))) . '</div>' .
                              osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&Write&' . $OSCOM_Product->getKeyword()), OSCOM::getDef('box_reviews_write')) .
                              '<div style="clear: both;"></div>';
          }
        } else {
          if ( !empty($data['image']) ) {
            $this->_content = '<div align="center">' . osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&View=' . $data['reviews_id'] . '&' . $data['products_keyword']), $OSCOM_Image->show($data['image'], $data['products_name'])) . '</div>';
          }

          $this->_content .= osc_link_object(OSCOM::getLink(null, 'Products', 'Reviews&View=' . $data['reviews_id'] . '&' . $data['products_keyword']), wordwrap(osc_output_string_protected($data['reviews_text']), 15, '&shy;') . ' ..') . '<br /><div align="center">' . osc_image(DIR_WS_IMAGES . 'stars_' . $data['reviews_rating'] . '.png' , sprintf(OSCOM::getDef('box_reviews_stars_rating'), $data['reviews_rating'])) . '</div>';
        }
      }
    }

    public function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Random Review Selection', 'BOX_REVIEWS_RANDOM_SELECT', '10', 'Select a random review from this amount of the newest reviews available', '6', '0', now())");
      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_REVIEWS_CACHE', '1', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    public function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_REVIEWS_RANDOM_SELECT', 'BOX_REVIEWS_CACHE');
      }

      return $this->_keys;
    }
  }
?>
