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

$RecipeInfo['Modules']['Version'] = '20230131';

SDV($ModuleDir, "$FarmD/modules");
SDV($ModuleDirUrl, preg_replace('#/[^/]*$#', '/modules', $ScriptUrl, 1));
$ModuleHeaderFmt = $ModuleFooterFmt = array();

# This needs to be injected before local.css (skins.php)
$HTMLHeaderFmt['Modules'] = $HTMLFooterFmt['Modules'] = array();


$PostConfig["$ModuleDir/modules/modules2.php"] = 25;
$PostConfig["$ModuleDir/modules/modules3.php"] = 125;

$list = LoadModuleList($pagename, 0);
foreach($list as $Module) {
  $f = "$ModuleDir/$Module/$Module.php";
  if(file_exists($f)) {
    include_once($f);
    $fn = "{$Module}_loaded";
    if(function_exists($fn)) $fn($pagename);
  }
}


function ModuleData($name) {
  return array();
}


function ModuleSort($a, $b) {
  return $a['order'] - $b['order'];
}

function LoadModuleList($pagename, $min) {
  global $Modules, $ModuleDir, $action;
  uasort($Modules, 'ModuleSort');
  
  $list = array();
  
  $max = $min+1000;
  foreach($Modules as $Module=>$a) {
    if($a['order']<$min || $a['order']>=$max) continue;
    if(isset($a['action'])  && !MatchNames($action, $a['action'])) continue;
    if(isset($a['pagename'])&& !MatchNames($pagename, $a['pagename'])) continue;
    $list[] = $Module;
  }
  return $list;
}

function ModuleHeaderFooter($a, &$fmt) {
  global $ModuleDirUrl;
  $a = (array)$a;
  $fname = array_shift($a);
  $dataset = '';

  foreach($a as $k=>$v) {
    if(is_null($v)||$v===''||!preg_match('/^\\w([\\w-]*\\w)*$/', $k)) continue;
    
    if(is_string($v)) $v = PHSC($v);
    elseif(is_numeric($v)) {}
    elseif(is_array($v)) $v = PHSC(json_encode($a, JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT 
      | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES, 4096));
    else continue;
    $dataset .= " $k=\"$v\"";
  }
  
  if(preg_match('/\\.js$/', $fname)) {
    $x = "<script src=\"$ModuleDirUrl/$fname\" $dataset></script>\n";
    $fmt['Modules'][] = $x;
  }
  elseif(preg_match('/\\.css$/', $fname)) {
    $x = "<link rel=\"stylesheet\" href=\"$ModuleDirUrl/$fname\" />\n";
    $fmt['Modules'][] = $x;
  }
  
}

