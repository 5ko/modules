<?php if (!defined('PmWiki')) exit();
/**
  Module management for PmWiki
  Written by (c) Petko Yotov 2023   www.pmwiki.org/Petko

  This text is written for PmWiki; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version. See pmwiki.php for full details
  and lack of warranty.
  
  This file loads modules after scripts/stdconfig.php, 
  and adds any scripts and styles to the HTML output.
*/

$list = LoadModuleList($pagename, 2000);
foreach($list as $Module) {
  $f = "$ModuleDir/$Module/$Module.php";
  if(file_exists($f)) {
    include_once($f);
    $fn = "{$Module}_loaded";
    if(function_exists($fn)) $fn($pagename);
  }
}

foreach($ModuleHeaderFmt as $a) {
  ModuleHeaderFooter($a, $HTMLHeaderFmt);
}

foreach($ModuleFooterFmt as $a) {
  ModuleHeaderFooter($a, $HTMLFooterFmt);
}

