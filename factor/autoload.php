<?php
function loadClass($className) {
    $fileArray = explode(DIRECTORY_SEPARATOR, $className);
    $searchFile = $fileArray[count($fileArray)-1].".php";
    if( fileSearch(getcwd(), $searchFile) == null)  fileSearch(dirname(getcwd()), $searchFile);
    

}

function fileSearch($dirName, $searchFile) {
    $dirArray = array();
   // echo $dirName." ".is_dir($dirName);
    $trouve = "";
   foreach(scandir($dirName) as $key => $dirElement) {
    $path =  @realpath($dirName.DIRECTORY_SEPARATOR.$dirElement);
   // echo $path. "<br />";
       if( !is_dir($path) ) {
          //echo "isfile : ".$dirElement." || ";
          if ($dirElement == $searchFile ) {
              //echo " trouve : ";
           // echo $path."-----";
            $trouve =  $path;
            require_once($path);
            break;
          }
       }
       elseif (  $dirElement != "." && $dirElement != ".." ) {
        //echo "isdir ";
        //echo $path. "<br />";
        $dirArray[] = $path;
        fileSearch($path, $searchFile);
       }
   }
  /* foreach($dirArray as $other) {
        fileSearch($other, $searchFile);
   } */
   //echo $trouve;
   return $trouve;
}

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            //echo "isdir";
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
spl_autoload_register('loadClass');
?>