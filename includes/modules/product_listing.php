<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Product;

// create column list
  $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);

  asort($define_list);

  $column_list = array();
  reset($define_list);
  while (list($key, $value) = each($define_list)) {
    if ($value > 0) $column_list[] = $key;
  }

  if ( ($Qlisting->numberOfRows() > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>

  <?php echo $Qlisting->getBatchTotalPages(OSCOM::getDef('result_set_number_of_products')); ?>
</div>

<?php
  }
?>

<div>
  
<?php
  if ($Qlisting->numberOfRows() > 0) {
?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>

<?php
    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      $lc_key = false;
      $lc_align = '';

      switch ($column_list[$col]) {
        case 'PRODUCT_LIST_MODEL':
          $lc_text = OSCOM::getDef('listing_model_heading');
          $lc_key = 'model';
          break;
        case 'PRODUCT_LIST_NAME':
          $lc_text = OSCOM::getDef('listing_products_heading');
          $lc_key = 'name';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $lc_text = OSCOM::getDef('listing_manufacturer_heading');
          $lc_key = 'manufacturer';
          break;
        case 'PRODUCT_LIST_PRICE':
          $lc_text = OSCOM::getDef('listing_price_heading');
          $lc_key = 'price';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $lc_text = OSCOM::getDef('listing_quantity_heading');
          $lc_key = 'quantity';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $lc_text = OSCOM::getDef('listing_weight_heading');
          $lc_key = 'weight';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_IMAGE':
          $lc_text = OSCOM::getDef('listing_image_heading');
          $lc_align = 'center';
          break;
        case 'PRODUCT_LIST_BUY_NOW':
          $lc_text = OSCOM::getDef('listing_buy_now_heading');
          $lc_align = 'center';
          break;
      }

      if ($lc_key !== false) {
        $lc_text = osc_create_sort_heading($lc_key, $lc_text);
      }

      echo '      <td align="' . $lc_align . '" class="productListing-heading">&nbsp;' . $lc_text . '&nbsp;</td>' . "\n";
    }
?>

    </tr>

<?php
    $rows = 0;

    while ($Qlisting->next()) {
      $OSCOM_Product = new Product($Qlisting->valueInt('products_id'));

      $rows++;

      echo '    <tr class="' . ((($rows/2) == floor($rows/2)) ? 'productListing-even' : 'productListing-odd') . '">' . "\n";

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $OSCOM_Product->getModel() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($_GET['manufacturers'])) {
              $lc_text = osc_link_object(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $OSCOM_Product->getTitle());
            } else {
              $lc_text = '&nbsp;' . osc_link_object(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . ($OSCOM_Category->getID() > 0 ? '&cPath=' . $OSCOM_Category->getPath() : '')), $OSCOM_Product->getTitle()) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;';

            if ( $OSCOM_Product->hasManufacturer() ) {
              $lc_text = '&nbsp;' . osc_link_object(OSCOM::getLink(null, 'Index', 'manufacturers=' . $OSCOM_Product->getManufacturerID()), $OSCOM_Product->getManufacturer()) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getPriceFormated() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getQuantity() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $OSCOM_Product->getWeight() . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_align = 'center';
            if (isset($_GET['manufacturers'])) {
              $lc_text = osc_link_object(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle()));
            } else {
              $lc_text = '&nbsp;' . osc_link_object(OSCOM::getLink(null, 'Products', $OSCOM_Product->getKeyword() . ($OSCOM_Category->getID() > 0 ? '&cPath=' . $OSCOM_Category->getPath() : '')), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle())) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_BUY_NOW':
            $lc_align = 'center';
            $lc_text = osc_link_object(OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), osc_draw_image_button('button_buy_now.gif', OSCOM::getDef('button_buy_now'))) . '&nbsp;';
            break;
        }

        echo '      <td ' . ((empty($lc_align) === false) ? 'align="' . $lc_align . '" ' : '') . 'class="productListing-data">' . $lc_text . '</td>' . "\n";
      }

      echo '    </tr>' . "\n";
    }
?>

  </table>

<?php
  } else {
    echo OSCOM::getDef('no_products_in_category');
  }
?>

</div>

<?php
  if ( ($Qlisting->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qlisting->getBatchPageLinks('page', osc_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></span>

  <?php echo $Qlisting->getBatchTotalPages(OSCOM::getDef('result_set_number_of_products')); ?>
</div>

<?php
  }
?>
