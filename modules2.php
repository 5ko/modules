<?php if (!defined('PmWiki')) exit();
/**
  Module management for PmWiki
  Written by (c) Petko Yotov 2023   www.pmwiki.org/Petko

  This text is written for PmWiki; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version. See pmwiki.php for full details
  and lack of warranty.
  
  This file loads modules after config.php, Group.Page.php and Group.php.
*/


$list = LoadModuleList($pagename, 1000);
foreach($list as $Module) {
  $f = "$ModuleDir/$Module/$Module.php";
  if(file_exists($f)) {
    include_once($f);
    $fn = "{$Module}_loaded";
    if(function_exists($fn)) $fn($pagename);
  }
}
