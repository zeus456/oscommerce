<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Site\Shop\Reviews;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  $Qreviews = Reviews::getEntry($_GET['View']);
?>

<h1 style="float: right;"><?php echo $OSCOM_Product->getPriceFormated(true); ?></h1>

<h1><?php echo $OSCOM_Template->getPageTitle() . ($OSCOM_Product->hasModel() ? '<br /><span class="smallText">' . $OSCOM_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Reviews') ) {
    echo $OSCOM_MessageStack->get('Reviews');
  }

  if ( $OSCOM_Product->hasImage() ) {
?>

<div style="float: right; text-align: center;">
  <?php echo osc_link_object(OSCOM::getLink(null, null, 'Images&' . $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle(), 'hspace="5" vspace="5"', 'thumbnail'), 'target="_blank" onclick="window.open(\'' . OSCOM::getLink(null, null, 'Images&' . $OSCOM_Product->getKeyword()) . '\', \'popUp\', \'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=' . (($OSCOM_Product->numberOfImages() > 1) ? $OSCOM_Image->getWidth('large') + ($OSCOM_Image->getWidth('thumbnails') * 2) + 70 : $OSCOM_Image->getWidth('large') + 20) . ',height=' . ($OSCOM_Image->getHeight('large') + 20) . '\'); return false;"'); ?>
  <?php echo '<p>' . osc_link_object(OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), osc_draw_image_button('button_in_cart.gif', OSCOM::getDef('button_add_to_cart'))) . '</p>'; ?>
</div>

<?php
  }
?>

<p><?php echo osc_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.png', sprintf(OSCOM::getDef('rating_of_5_stars'), $Qreviews->valueInt('reviews_rating'))) . '&nbsp;' . sprintf(OSCOM::getDef('reviewed_by'), $Qreviews->valueProtected('customers_name')) . '; ' . DateTime::getLong($Qreviews->value('date_added')); ?></p>

<p><?php echo nl2br(wordwrap($Qreviews->valueProtected('reviews_text'), 60, '&shy;')); ?></p>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_link_object(OSCOM::getLink(null, null, 'Reviews&Write&' . $OSCOM_Product->getKeyword()), osc_draw_image_button('button_write_review.gif', OSCOM::getDef('button_write_review'))); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getKeyword()), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>
