<?php

class PluginMreportingDownload extends CommonDBTM {

   public function __construct() {
      $this->forceTable("glpi_plugin_mreporting_configs");
      parent::__construct();
   }

}
