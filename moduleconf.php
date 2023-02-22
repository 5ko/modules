<?php if (!defined('PmWiki')) exit();
/**
  Module management for PmWiki
  Written by (c) Petko Yotov 2023   www.pmwiki.org/Petko

  This text is written for PmWiki; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version. See pmwiki.php for full details
  and lack of warranty.
  
*/


SDVA($HandleActions, array(
  'moduleconf' => 'HandleModuleConf',
));

function HandleModuleConf($pagename) {
  global $PmModules;
  
  $confpagename = FmtPageName($PmModules['ModuleConfPageFmt'], $pagename);
  
  xmp($confpagename);

}
