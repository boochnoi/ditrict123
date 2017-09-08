<?php
$op = isset($argv[1]) ? $argv[1] : 'usage';
if( $op == 'usage' )
{
	printf("Usage: normalize.php [normalize|build-css]\n");
}
elseif( $op == 'normalize' )
{
	$icons_dir = dirname(__FILE__);
	$dh = opendir($icons_dir);
	while( ($file = readdir($dh)) !== false )
	{
		if( $file{0} == '.') continue;
		if( strstr($file, '.php') ) continue;
	
		$icon = $icons_dir . '/' . $file;
		$new_icon = preg_replace('/\s+/', '-', $file);
		$new_icon = $icons_dir . '/' . preg_replace('/[^a-zA-Z0-9.-]/', '-', $new_icon);
		rename($icon, $new_icon);
	}
	closedir($dh);
}
elseif( $op = 'build-css' )
{
	$css_dir = dirname(dirname(dirname(__FILE__))) . '/css';
	$css_file = $css_dir . '/icons.css';
	$icons_dir = dirname(__FILE__);
	$fh = fopen($css_file, 'w+');
	$dh = opendir($icons_dir);
	$def_icon = '';
	while( ($file = readdir($dh)) !== false )
	{
		if( $file{0} == '.') continue;
		if( strstr($file, '.php') ) continue;
		
		$icon = $icons_dir . '/' . $file;
		$css_class = substr($file, 0, strrpos($file, '.'));
		fwrite($fh, sprintf(".icon-%s{background-image:url('../images/icons-16/%s') !important;background-repeat:no-repeat !important;background-position:center center !important;}\n", $css_class, $file));
		if( empty($def_icon) && rand(1, 10) == 6 )
		{
			$def_icon = $file;
		}
	}
	fwrite($fh, ".icon-16{width:26px !important;height:26px !important;padding:0 !important;overflow:hidden;text-indent: -1000px;}\n");
	fwrite($fh, ".default-action-icon-selector{background-image:url('../images/icons-16/$def_icon') !important;background-repeat:no-repeat !important;background-position:center center !important;}\n");
	fclose($fh);
	closedir($dh);
}