<?
include_once "statusage.php"; //by almaz - usage control
function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();

    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }

    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

/**
 * XmlToMassive Generator Class
 * @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
 *             Moderator, phpResource (LINK1http://groups.yahoo.com/group/phpresource/LINK1)
 *             URL: LINK2http://www.rupom.infoLINK2
 * @version :  1.0
 * @date       06/05/2006
 * Purpose  : Creating Hierarchical Array from XML Data
 * Released : Under GPL
 */

class XmlToMassive {

  var $xml='';

  /**
   * Default Constructor
   * @param $xml = xml data
   * @return none
   */

  function XmlToMassive($xml)
  {
    $this->xml = $xml;
  }

  /**
   * _struct_to_array($values, &$i)
   *
   * This is adds the contents of the return xml into the array for easier processing.
   * Recursive, Static
   *
   * @access    private
   * @param    array  $values this is the xml data in an array
   * @param    int    $i  this is the current location in the array
   * @return    Array
   */
  function _struct_to_array($values, &$i)
  {
    $child = array();
    if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']);

    while ($i++ < count($values)) {
      switch ($values[$i]['type']) {
      case 'cdata':
        array_push($child, $values[$i]['value']);
        break;

      case 'complete':
        $name = $values[$i]['tag'];
        if(!empty($name)){
          $child[$name]= ($values[$i]['value'])?($values[$i]['value']):'';
          if(isset($values[$i]['attributes'])) {
            $child[$name] = $values[$i]['attributes'];
          }
        }
        break;

      case 'open':
        $name = $values[$i]['tag'];
        $size = isset($child[$name]) ? sizeof($child[$name]) : 0;
        $child[$name][$size] = $this->_struct_to_array($values, $i);
        break;

      case 'close':
        return $child;
        break;
      }
    }
    return $child;
  }//_struct_to_array
  /**
   * createArray($data)
   *
   * This is adds the contents of the return xml into the array for easier processing.
   *
   * @access    public
   * @param    string    $data this is the string of the xml data
   * @return    Array
   */
  function createArray()
  {
    $xml    = $this->xml;
    $values = array();
    $index  = array();
    $array  = array();
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parse_into_struct($parser, $xml, $values, $index);
    xml_parser_free($parser);
    $i = 0;
    $name = $values[$i]['tag'];
    $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
    $array[$name] = $this->_struct_to_array($values, $i);
    return $array;
  }//createArray

}//XmlToMassive


function xml2array($XML,$RootNodeIsRequired=true) {
  $xmlObj = new XmlToMassive($XML);
  $array = $xmlObj->createArray();
  if($RootNodeIsRequired) {
    return $array;
  } else {
    if(is_array($array)) {
      foreach($array as $v) {
        return $v;
      }
    }
  }
}



class Array2XML {
   
  private $writer;
  private $version = '1.0';
  private $encoding = 'UTF-8';
  private $rootName = 'root';
   
 
  function __construct() {
    $this->writer = new XMLWriter();
  }
   
  public function convert($data) {
    $this->writer->openMemory();
    $this->writer->startDocument($this->version, $this->encoding);
    $this->writer->startElement($this->rootName);
    if (is_array($data)) {
      $this->getXML($data);
    }
    $this->writer->endElement();
    return $this->writer->outputMemory();
  }
  public function setVersion($version) {
    $this->version = $version;
  }
  public function setEncoding($encoding) {
    $this->encoding = $encoding;
  }
  public function setRootName($rootName) {
    $this->rootName = $rootName;
  }
  private function getXML($data) {
    foreach ($data as $key => $val) {
      if (is_numeric($key)) {
	$key = 'key'.$key;
      }
      if (is_array($val)) {
	$this->writer->startElement($key);
	$this->getXML($val);
	$this->writer->endElement();
      }
      else {
	$this->writer->writeElement($key, $val);
      }
    }
  }
}

function array2xml($Array) {
  $converter = new Array2XML();
  $xmlStr = $converter->convert($Array);
  return $xmlStr;
}

?>