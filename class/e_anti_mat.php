<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<?php
//setlocale (LC_ALL, "ru_RU.CP1251");
/*<=====================Describing anti_mate class==============================>*/
class e_anti_mat {
	//latin equivalents for russian letters
	var $let_matches = array (
    "a" => "а",
    "c" => "с",
    "e" => "е",
    "k" => "к",
    "m" => "м",
    "o" => "о",
    "x" => "х",
    "y" => "у",
    "ё" => "е"
    );
    //bad words array. Regexp's symbols are readable !
    var $bad_words = array (".*ху(й|и|я|е|л(и|е)).*", ".*пи(з|с)д.*", "бля.*", ".*бля(д|т|ц).*", "(с|сц)ук(а|о|и).*", "еб.*", ".*уеб.*", "заеб.*", ".*еб(а|и)(н|с|щ|ц).*", ".*ебу(ч|щ).*", ".*пид(о|е)р.*", "(по|на)?хер.*", "г(а|о)ндон", ".*залуп.*");

    function rand_replace (){
    	$output = " <font color=red>[censored]</font> ";
    	$output = " <font color=red>***</font> ";
    	return $output;
    }
    function filter ($string){
    	//	$string=iconv('utf-8','cp1251', $string);
    	$counter = 0;
    	$elems = explode (" ", $string); //here we explode string to words
    	$count_elems = count($elems);
    	for ($i=0; $i<$count_elems; $i++)
    	{
    		$blocked = 0;
    		/*formating word...*/
    		$str_rep=$elems[$i];
    		//$str_rep = preg_replace ("'[^a-zа-я]'i", "", strtolower(strip_tags($elems[$i])));
    		//if ($elems[$i]=='керхером)..')

    		//exit;
    		for ($j=0; $j<strlen($str_rep); $j++)
    		{
    			foreach ($this->let_matches as $key => $value)
    			{
    				if ($str_rep[$j] == $key)
    				$str_rep[$j] = $value;

    			}
    		}
    		/*done*/

    		/*here we are trying to find bad word*/
    		/*match in the special array*/
    		for ($k=0; $k<count($this->bad_words); $k++)	{
    			if (preg_match("'\*$'i", $this->bad_words[$k])){
    				//var_dump($str_rep);
    				if (preg_match("'^{$this->bad_words[$k]}'i", $str_rep)){
    					$elems[$i] = $this->rand_replace();
    					$blocked = 1;
    					$counter++;
    					break;
    				}

    			}
    			if ($str_rep == $this->bad_words[$k]){
    				$elems[$i] = $this->rand_replace();
    				$blocked = 1;
    				$counter++;
    				break;
    			}

    		}
    	}
    	if ($counter != 0)
    	$string = implode (" ", $elems); //here we implode words in the whole string
    	return $string;
    }
}

$anti_mate = new anti_mat();
$str='писд бля, пошли все на. сцуки хер похер нахер';
//$str=file_get_contents('http://otdyh-ua.net/items/310-skazka');
//$str=iconv('utf-8','cp1251', $str);
echo $anti_mate->filter($str);