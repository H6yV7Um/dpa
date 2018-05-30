<?php
if(!empty($response['html_content'])){
  header('Content-type: text/xml;charset='.$GLOBALS['cfg']['db_character_contype']);
  $serializer_options = array (
    'addDecl' => TRUE,
    'encoding' => $GLOBALS['cfg']['db_character_contype'],
    'indent' => '	',
    'rootName' => 'root',
    'defaultTagName' => 'item',
    //'rootAttributes' => array('source' => '1'),
  );
  echo serialize_xml($response['html_content'], $serializer_options);
}else { echo ""; }