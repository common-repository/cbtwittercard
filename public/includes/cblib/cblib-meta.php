<?php    
/**
 *
 * @package   cbtwittercardaddon
 * @author    WPBoxr <info@wpboxr.com>
 * @license   GPL-2.0+
 * @link      http://wpboxr.com
 * @copyright 2014-2016 WPBoxr.com
 */

/**
* Meta field input helper
*/
class CBTwitterCardMetaTagHelper
{
    
    /**
     * Pargse associative array and output as regular seo meta 
     * 
     * 
     * @param type $metaarray
     * @return string
     */
    function cbtwittercard_addmeta($metaarray){
    	$tag='<meta ';
    	foreach ($metaarray as $key => $value) {
    		$tag .= "$key = \"$value\" ";
    	}
    	$tag .= " />\n";
    	return $tag;
    }

    function cbtwittercard_addogmeta($metaarray){
        //print $metaarray;
        //exit();
        $tag = '';
        foreach ($metaarray as $key => $value) {
            $tag .= '<meta ';
            $tag .= 'property="'.$key.'" content="'.$value.'" ';
            $tag .= "/>\n";
//            $tag .= "$key = \"$value\" ";
        }
        
        return $tag;
    }

    /**
     * Pargse associative array and output as twitter meta 
     * 
     * @since 1.0.7
     * 
     * @param type array
     * @return string
     */
    function cbtwittercard_addtcmeta($metaarray){
        $tag = '';
        foreach ($metaarray as $key => $value) {
            $tag .= '<meta ';
            $tag .= 'name="twitter:'.$key.'" content="'.$value.'" ';
            $tag .= "/>\n";
//            $tag .= "$key = \"$value\" ";
        }
        
        return $tag;
    }
}

?>