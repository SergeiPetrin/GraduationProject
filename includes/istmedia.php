<?
if( !function_exists('translitIt') ){
	function translitIt($str) 
	{
	    $tr = array(
	        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
	        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
	        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
	        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
	        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
	        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
	        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
	        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
	        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
	        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
	        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
	        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
	        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya", " " => "_"
	    );
	    return strtr($str,$tr);
	}
}

if( !function_exists('addOrder') ){
	function addOrder($post,$convertedBody){
		$params = array();
		$params['name']  = $post['name'];
		$params['phone'] = $post['phone'];
		$params['email'] = $post['email'];
		$params['mytxt'] = $post['text'];
		$params['other'] = array($convertedBody);
		$params['f']     = array();
		//foreach($post as $k => $v){
		//	if( !in_array($k, array('name','phone', 'email', 'text','sended', '1') ) )
		//	$params['other'][] = $k.': '.$v;
		//}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$db->setQuery("SELECT params FROM #__extensions WHERE extension_id = '13'");
		$result = $db->loadAssoc();
		$ext_params = json_decode($result['params']);
		$allowed = explode(',',$ext_params->upload_extensions);
		$max_size = $ext_params->upload_maxsize*1000000;//Mb

		$uploads_dir = '/images/form_files/';
		if( !is_dir($_SERVER['DOCUMENT_ROOT'].$uploads_dir) ){
			@mkdir($_SERVER['DOCUMENT_ROOT'].$uploads_dir,0755);
		}
		$f_names = Array();
		foreach ($_FILES as $f_name => $f_ar) {
	        $tmp_name = $f_ar["tmp_name"];
	        $name = translitIt($f_ar["name"]);
	        $ext = pathinfo($name, PATHINFO_EXTENSION);
			if( !in_array($ext,$allowed) ){
				//echo "Запрещенное расширение $ext";
				continue;
			}elseif($f_ar['size'] > $max_size ) {
				//echo "Файл слишком велик";
				continue;
			}
	       	while( file_exists($_SERVER['DOCUMENT_ROOT']."$uploads_dir/$name") ){
	       		$name = rand().'_'.$name;
	       	}
	       	move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."$uploads_dir/$name");
		    $params['f'][]  = $uploads_dir.$name;
		}
		
		$db->setQuery("SELECT MAX(ordering) maxordering FROM #__istmedia_forms_messages");
		$result = $db->loadAssoc();
		$maxordering = $result['maxordering'] + 1;

		$db->setQuery("INSERT INTO #__istmedia_forms_messages
						SET state = 1,
							status = 1,
							name = '".$params['name']."',
						    phone = '".$params['phone']."',
						    email = '".$params['email']."',
						    mytxt = '".$params['mytxt']."',
						    other = '".implode("\n",$params['other'])."',
						    f = '".implode('\n',$params['f'])."',
						    ordering = '".$maxordering."'");
		$db->execute();
	}
}
?>