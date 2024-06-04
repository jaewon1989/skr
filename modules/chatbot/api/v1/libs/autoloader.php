<?php
function getSlimClass($className)
{
      global $g,$m,$version;

      $path= $g['path_module'].$m.'/api/'.$version.'/libs/Slim/';

      $className = ltrim($className, '\\');
      $fileName  = '';
      $namespace = '';
      if ($lastNsPos = strrpos($className, '\\')) {
          $namespace = substr($className, 0, $lastNsPos);
          $className = substr($className, $lastNsPos + 1);
          $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
      }
      $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

      require_once $path.$fileName;
}

spl_autoload_register('getSlimClass');
