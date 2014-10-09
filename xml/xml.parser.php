<?php
// from http://www.koders.com/php/fidF0F7315DC026AF681ECE7727C1F44804EFD10925.aspx
// constants that we use when parsing the XML
define ('FILETYPES_EXTENSIONS_CONTAINER','extensions');
define ('FILETYPES_EXTENSION_TAG', 'extension');
define ('FILETYPES_MIMETYPES_CONTAINER','mimetypes');
define ('FILETYPES_MIMETYPE_TAG','mimetype');
define ('FILETYPES_VIEWER_URL_TAG','viewer_url');
define ('FILETYPES_VIEWER_TEXT_TAG','viewer_text');

/**
* Function used by XML_GetXMLTree() to help process return array from xml_parse_into_struct.
* Code by gdemartini@bol.com.br
*/
function XML_GetChildren($vals, &$i)
{
	$children = array();
	if ( isset($vals[$i]['value']) )
    {
		array_push($children, $vals[$i]['value']);
    }

	while (++$i < count($vals))
	{
		switch ($vals[$i]['type'])
		{
			case 'cdata':
				array_push($children, $vals[$i]['value']);
				break;
			case 'complete':
                $child = array();
                
                if ( isset( $vals[$i]['tag'] ) )
                {
                    $child['tag'] = $vals[$i]['tag'];
                }
                
                if ( isset( $vals[$i]['attributes'] ) )
                {
                    $child['attributes'] = $vals[$i]['attributes'];
                }
                
                if ( isset( $vals[$i]['value'] ) )
                {
                    $child['value'] = $vals[$i]['value'];
                }
                
				array_push($children, $child);
				break;
            case 'open':
                $child = array();
                
                if ( isset( $vals[$i]['tag'] ) )
                {
                    $child['tag'] = $vals[$i]['tag'];
                }
                
                if ( isset( $vals[$i]['attributes'] ) )
                {
                    $child['attributes'] = $vals[$i]['attributes'];
                }
                
                $child['children'] = XML_GetChildren($vals, $i);
            
                array_push($children, $child);                
                break;
            case 'close':
                return $children;
		}
	}
}

/**
* Function to complement native xml_parse_into_struct to make the return array a little more reasonable.
*Code by gdemartini@bol.com.br with addition by nyk@cowham.net
*/
function XML_GetXMLTree($data)
{

	$p = xml_parser_create_ns();
	xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
	xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);

	xml_parse_into_struct($p, $data, $vals, $index);
	xml_parser_free($p);
	$tree = array();
	$i = 0;
    
    $child = array();    
    if ( isset( $vals[$i]['tag'] ) )
    {
        $child['tag'] = $vals[$i]['tag'];
    }
    
    if ( isset( $vals[$i]['attributes'] ) )
    {
        $child['attributes'] = $vals[$i]['attributes'];
    }
    
    if ( isset( $vals[$i]['value'] ) )
    {
        $child['value'] = $vals[$i]['value'];
    }
    
    $child['children'] = XML_GetChildren( $vals, $i );
    
	array_push($tree, $child);
	return $tree;
}

/**
* Function specific to the document_info application that takes array generated from the XML document and creates the simplified arrays we really want.
* Returns a multi-dim array contaning 3 main arrays:
* ['complete'] is the full array indexed by filetype code
* ['by_extension'] is indexed by extension with the filetype codes as values.
* ['by_mimeteyp'] is indexed by mimetype with the filetype codes as values.
* @return array | returns a multi-dim array containing 3 main arrays
* @access public
* @author Jason Roberts
*/
function XML_filetypes_from_XML($filetypes_path)
{
	$tree=XML_GetXMLTree($filetypes_path);
	$filetree = $tree[0]['children']; // strip off the outer layer I don't need
	foreach ($filetree as $filetype)
	{
		$type=$filetype['attributes']['TYPE']; // readability only
		
		// create the top level aray elements in our central filetypes array
		$filetypes['complete'][$type]=array();

		// fill in the attributes for each filetype
		foreach ($filetype['children'] as $attrib)
		{
			if (!isset($attrib['children'])) // this is a single-value attribute
            {
                if ( isset( $attrib['value'] ) )
                {
                    $filetypes['complete'][$type][strtolower($attrib['tag'])]=$attrib['value'];
                }
                else
                {
                    $filetypes['complete'][$type][strtolower($attrib['tag'])]='';
                }
            }
			else // it's an array of values (e.g. extensions)
			{
				$filetypes['complete'][$type][strtolower($attrib['tag'])]=array();
				foreach ($attrib['children'] as $value)
				{
					// record it in the comprehensive array no matter what
                    if ( isset( $value['value'] ) )
                    {
                        $filetypes['complete'][$type][strtolower($attrib['tag'])][]=$value['value'];
                    }

					// now create specific arrays for extensions and mimetypes
					switch (strtolower($value['tag']))
					{
                        
						case FILETYPES_MIMETYPE_TAG:                
                            if ( isset( $value['value'] ) )
                            {
                                $filetypes['by_mimetype'][$value['value']]=$type;
                            }
                            else
                            {
                                $filetypes['by_mimetype'][]=$type;
                            }
							break;
						case FILETYPES_EXTENSION_TAG:
                            if ( isset( $value['value'] ) )
                            {
                                $filetypes['by_extension'][$value['value']]=$type;
                            }
                            else
                            {
                                $filetypes['by_extension'][]=$type;
                            }
							break;
						default: // do nothing
							break;
					} // end switch
				}
			}
		}
	}
	return $filetypes;
}
?>
