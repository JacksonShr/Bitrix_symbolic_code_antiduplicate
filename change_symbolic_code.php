/**
This script changes symbolic code of items in Bitrix shop.
It can add numbers to the end of the symbolic code to avoid duplicate entries.
*/
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');
$IBLOCK_ID = 8;
$fileElement = $_SERVER['DOCUMENT_ROOT'].'/scripts/elements.txt';
$fileSection = $_SERVER['DOCUMENT_ROOT'].'/scripts/section.txt';

$rsSections = CIBlockSection::GetList(
    array('SORT' => 'asc'),
    array('IBLOCK_ID' => $IBLOCK_ID),
    false,
    array('ID', 'NAME', 'CODE'),
    false
);
$currentSect = file_get_contents($fileSection);
$currentSect .= "-----------------\r\n";
while ($arSction = $rsSections->GetNext(false, false))
{
    echo '<pre>';
    print_r($arSction);
    echo '</pre>';
    $arParamsSect = array("replace_space"=>"-","replace_other"=>"-");
    $transSect = Cutil::translit($arSction['NAME'],"ru",$arParamsSect);
    echo '<pre>';
    print_r($transSect);
    echo '</pre>';
    $currentSect .= $arSction['CODE'].' - '.$transSect."\r\n";
    file_put_contents($fileSection, $currentSect);
    $bs = new CIBlockSection;
    $arFields = Array(
        'CODE' => $transSect
    );
    $res = $bs->Update($arSction['ID'], $arFields);
    
   
}




    $elDB = CIBlockElement::GetList(
        array('SORT' => 'asc'),
        array('IBLOCK_ID'=> $IBLOCK_ID),
        false,
        false,
        array('ID', 'NAME', 'CODE')
    );
    $currentEl = file_get_contents($fileElement);
    $currentEl .= "-----------------\r\n";
    
    $tArray = array();
    $k = array();

    while($arEl = $elDB->GetNext(false, false))
    {
    
    	
        echo '<pre>';
        print_r($arEl);
        echo '</pre>';
        $arParams = array("replace_space"=>"-","replace_other"=>"-");
        $trans = Cutil::translit($arEl['NAME'],"ru",$arParams);
        
        
        foreach($tArray as $value){
        	
        	if(!isset($k[$value])) $k[$value] = 0;
        	        
        	if($value == $trans ) {
        	
        		$k[$value]++;
        		$trans = $trans . "-" . $k[$value];
        		
        	}
        	
        	
        }
        
        echo '<pre>';
        print_r($trans);
        echo '</pre>';
        $tArray[] = $trans;

        print_r($currentEl);
        $currentEl .= $arEl['CODE'].' - '.$trans."\r\n";
        file_put_contents($fileElement, $currentEl);
        $el = new CIBlockElement;
        $arLoadProductArray = Array(
            'CODE' =>$trans
        );
        $res = $el->Update($arEl['ID'], $arLoadProductArray);
        
         
    }
    	echo "<hr />";
    	var_dump($tArray);
	echo "<hr />";
