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

$RecipeInfo['Modules']['Version'] = '20230212';


SDVA($PmModules, array(
  'dir' => dirname(dirname(__file__)),
  'dirurl' => preg_replace('#/[^/]*$#', '/modules', $ScriptUrl, 1),
  'ModuleConfPageFmt' => '{$SiteAdminGroup}.ModuleConf',
));

if($action=='moduleconf') {
  include_once(dirname(__file__) . '/moduleconf.php');
}

$ModuleHeaderFmt = $ModuleFooterFmt = array();

# This needs to be injected before local.css (skins.php)
$HTMLHeaderFmt['Modules'] = $HTMLFooterFmt['Modules'] = array();


$PostConfig["{$PmModules['dir']}/modules/modules2.php"] = 25;
$PostConfig["{$PmModules['dir']}/modules/modules3.php"] = 125;


$list = LoadModuleList($pagename, 0);
foreach($list as $Module) {
  $ModuleDir = "{$PmModules['dir']}/$Module";
  $ModuleDirUrl = "{$PmModules['dirurl']}/$Module";
  $f = "$ModuleDir/$Module.php";
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
  return $a[0] - $b[0];
}

function LoadModuleList($pagename, $min) {
  global $PmModules, $Modules, $action;
  foreach($Modules as $k=>$a) {
    if(is_numeric($a)) $Modules[$k] = array($a);
  }
  uasort($Modules, 'ModuleSort');
  @list($g, $n) = preg_split('![/.]!', $pagename);
  
  $list = array();
  
  $max = $min+1000;
  foreach($Modules as $Module=>$a) {
    if($a[0]<$min || $a[0]>=$max) continue;
    if(isset($a['action'])  && !MatchNames($action, $a['action'])) continue;
    if(isset($a['name'])&& !MatchNames($pagename, $a['name'])) continue;
    if(isset($a['group'])&& !MatchNames($g, $a['group'])) continue;
    $list[] = $Module;
  }
  return $list;
}

function ModuleHeaderFooter($a, &$fmt) {
  global $PmModules;
  $a = (array)$a;
  $fname = array_shift($a);
  $dataset = '';

  foreach($a as $k=>$v) {
    if(is_null($v)||$v===''||!preg_match('/^\\w([\\w-]*\\w)*$/', $k)) continue;
    
    if(is_string($v)) $v = PHSC($v);
    elseif(is_numeric($v)) {} # leave as is
    elseif(is_array($v)) $v = PHSC(json_encode($a, JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT 
      | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES, 4096));
    else continue;
    $dataset .= " $k=\"$v\"";
  }
  
  if(preg_match('!^(\\S*/)[^/\\s]+(\\s+\\S+)!', $fname, $m)) {
    $fname = preg_replace('/\\s+/', "$0$m[1]", $fname);
  }
  $fnames = preg_split('/\\s+/', $fname, -1, PREG_SPLIT_NO_EMPTY);

  
  foreach($fnames as $fname) {
    if(preg_match('/\\.js$/', $fname)) {
      $x = "<script src=\"$fname\" $dataset></script>\n";
      $fmt['Modules'][] = $x;
    }
    elseif(preg_match('/\\.css$/', $fname)) {
      $x = "<link rel=\"stylesheet\" href=\"$fname\" />\n";
      $fmt['Modules'][] = $x;
    }
  }
}

