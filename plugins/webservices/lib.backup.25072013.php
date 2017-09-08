<?
function splitXML($text){
  preg_match("#<xml(.*)</xml>(.*)#is",$text,$xmlmatch);
  return array(
               "xml"=>"<xml".$xmlmatch[1]."</xml>",
               "text"=>$xmlmatch[2]
               );
}
function split_project_description($Description) {
  $description_arr = splitXML($Description);
  $xmlfromarray = xml2array($description_arr["xml"]);
  if(is_array($xmlfromarray)) {
    $arr["params"] = $xmlfromarray["xml"];
    $arr["text"] = $description_arr["text"];
  } else {
    $arr["text"] = $Description;
  }
  return $arr;
}
function get_notes($id,$lang,$text=false, $fetch_themes){
        $buff = opengoo_get_project_notes($id,false);
        $len = count($buff);
	$name = 'name';
	$id = 'id';
	$textxml = 'text';
	$result1 = array(); 
        for($i=0;$i<=$len;$i++){
                if(!$buff[$i])
                        continue;

                $buff[$i]['name']=fos_bbCodeByLang($buff[$i]['title'],$lang);
		if($fetch_themes) {
	                $buff[$i]['parentid']=get_content_from_xml_tag($buff[$i]['text'], $id);
                	$buff[$i]['parentname']=get_content_from_xml_tag($buff[$i]['text'], $name);
		}
                $buff[$i]['text']=get_content_from_xml_tag($buff[$i]['text'], $textxml);

		if(!$text)
                	unset($buff[$i]['text']);
                unset($buff[$i]['title']);
        }
	if($fetch_themes) {
		$j=1;
		for($i=1; $i<=count($buff); $i++) {
			if(empty($result1)) {
				$result1[$j]['parentid']=$buff[$i]['parentid'];
				$result1[$j]['parentname']=$buff[$i]['parentname'];
				$j++;
			}
			else {
				$flag = 0;
				for($k=1; $k<=count($result1); $k++) {
					if(($result1[$k]['parentid']==$buff[$i]['parentid'])&&($result1[$k]['parentname']==$buff[$i]['parentname'])) {
						break;
					}
                                       $result1[$j]['parentid']=$buff[$i]['parentid'];
	                               $result1[$j]['parentname']=$buff[$i]['parentname'];
                	               $j++;
				}
			}
		}
		$faqs = array();
		for($i=1; $i<=count($result1); $i++) {
			$j=1;
			foreach($buff as $buffitem) {
				if(($buffitem['parentid']==$result1[$i]['parentid'])&&($buffitem['parentname']==$result1[$i]['parentname'])) {
					$faqs[$j]['id']=$buffitem['id'];
					$faqs[$j]['name']=$buffitem['name'];
					$faqs[$j]['text']=$buffitem['text'];
					$j++;
				}
			}
			$result1[$i]['faqs']=$faqs;
			$faqs=array();
		}
		return $result1;
	}

        return $buff;
}

function get_content_from_xml_tag($text, $tagname){

$xml = <<< XML
<?xml version="1.0" encoding="utf-8"?>
$text
XML;

$dom = new DOMDocument;
$dom->loadXML($xml);
$contents = $dom->getElementsByTagName($tagname);
foreach ($contents as $content) {
      $res = $content->nodeValue;
}
return $res;
}

function filter_params($data,$lang){
        foreach($data as $key=>$value){
                if($key!=="fieldtype" && $key!=="isrequired" && $key!=="fieldinput" && $key!=="maxim"){
                        $data[$key]=(is_array($value))?filter_params($value,$lang):fos_bbCodeByLang($value,$lang);
                }
        }
        return $data;
}
function FOS($id,$lang){
        $result = array();
        $level = opengoo_list_subprojects($id);
        if(!is_array($level)){
                $level = array();
        }
	$dom = new DOMDOcument('1.0','UTF-8');
        foreach($level as $key => $value){
                $result[$key]["id"] = $value["id"];
                $result[$key]["name"] = fos_bbCodeByLang($value["name"],$lang);
		@$dom->LoadXML('<form>'.trim($value["description"]).'</form>');
                $result[$key]['fields']=xmlToArr($dom->getElementsByTagName("form")->item(0),'ru');
		$result[$key]['maxim']=getMaxim($dom->getElementsByTagName("maxim"));
                $result[$key]["description"] = fos_bbCodeByLang($description_arr["text"],$lang);
                $result[$key]["faq"] = (get_notes($value["id"],$lang))?true:false;
                $description_arr = NULL;
                if($buff = FOS($value["id"],$lang))
                        $result[$key]["childs"] = $buff;
                $buff = NULL;
        }
        return $result;
}
function UpdateCahce($cacheFile,$lang){
        try{
                $result=file_put_contents(
                                $cacheFile,
                                json_encode(
                                        FOS(2026,$lang)
                                )
                        );
        }catch(Exception $e){
                var_dump($e->getMessage());
        }
        return (bool)$result;
}

function xmlToArr($node,$lang){
        $groupId=0;
        $fieldsets=array();
        $elements=array();
        $groups=$node->getElementsByTagName("group");
        foreach($groups as $group){
                $groupId++;
                if( $group->hasChildNodes() ){
                        $childs=$group->childNodes;
                        foreach($childs as $child){
				switch($child->nodeName){
					case "name":{
						$fieldsets[$groupId]["name"] = trim($child->textContent);
					}break;
					case "order":{
						 $fieldsets[$groupId]["order"]= trim($child->textContent);
					}break;
					default:{
						if($buff=parseElement($child,$groupId))
                                                	$elements[]=$buff;
					}break;
				}
                        }
                }
        }
        if($fieldsets || $elements)
                return array(
                        'fieldsets' => $fieldsets,
                        'elements'  => $elements
                );
        else
                return null;
}

function parseElement($node,$groupId){
        if( !$node->hasChildNodes() )
                return null;
	if( $node->nodeName=='group' ){
		return xmlToArr($node,$groupId);
	}
        $out=parseProperty($node->childNodes);
        $out["fieldset"] = $groupId;
        switch( $node->nodeName ){
                case "textarea":{
                        $out["element"]="textarea";
                }break;
                case "select":{
                        $out["element"]="select";
                }break;
                default:{
                        $out["element"]="input";
                        $out["type"]   =$node->nodeName;
                }
        }
        return $out;
}
function parseProperty($nodes){
        $out=array();
        foreach($nodes as $node){
		switch($node->nodeName){
			case 'name':{
				$out['name']=trim($node->textContent);
				$out['respName']=trim($node->textContent);
			}break;
			case 'option':{
                                $out['options'][]=parseProperty($node->childNodes);
                        }
                        break;
			case 'hint':{
				$out['hint']=trim($node->textContent);
			}
			break;
			case '#text':{
				continue;
			}
			break;
			default:{
				$out[$node->nodeName]=trim($node->textContent);
			}
		}
        }
        return $out;
}
function getFields($id,$level){
        $sql="SELECT id,name,description FROM og_projects where p".$level."='".$id."' and p".($level+1)."=0";
        $handle=mysql_query($sql);
        if($row=mysql_fetch_assoc($handle)){
                $dom = new DOMDOcument('1.0','UTF-8');
                @$dom->LoadXML('<form>'.trim($row['description']).'</form>');
                return xmlToArr($dom->getElementsByTagName("form")->item(0),'ru');
        }else
                return null;
}
function getMaxim($nodes){
        if($nodes && $nodes->length){
                return (int)trim($nodes->item(0)->textContent);
        }
        return 0;
}
?>
