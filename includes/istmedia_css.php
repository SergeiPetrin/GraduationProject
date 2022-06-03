<?
$baseUrl = JURI::base();
$cssPath = JPATH_BASE.DS.'templates'.DS.'jblank'.DS.'css'.DS.'ext'.DS;
$cssUrl  = DS.'templates'.DS.'jblank'.DS.'css'.DS.'ext'.DS;
if( is_dir($cssPath) ){
	$dh = @opendir($cssPath);
	while ( ($file = readdir($dh)) !== false ){
		if( $file != '.' && $file != '..' && $file != 'thumb' && $file != 'index.html' && stristr($file, '.css')) {
			echo '<link href="'.$cssUrl.$file.'" rel="stylesheet" type="text/css" />';
		}
	}
}
?>