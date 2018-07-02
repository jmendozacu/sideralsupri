<?php
class Adroll_Liquidads_Block_Adminhtml_Liquidads extends Mage_Adminhtml_Block_Page
{
  public function __construct()
  {
    parent::__construct();
    $this->setTemplate('liquidads.phtml');
  }
}
