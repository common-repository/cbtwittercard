<?php

/**
 *
 * @package   cbtwittercardaddon
 * @author    WPBoxr <info@wpboxr.com>
 * @license   GPL-2.0+
 * @link      http://wpboxr.com
 * @copyright 2014-2016 WPBoxr.com
 */


class InputTagHelper
{
	function __construct()
    {
        
    }

	function cb_generate_input($tagarray){
		$tag ='<input ';

        foreach ($tagarray as $key => $value) {
            if($value!='')
                $tag .= $key.' = "'.$value.'" ';
        }
        $tag .= ' />'."\n";
        
        return $tag;
	}

	function cb_add_input($name, $type, $id, $value, $radiocheck='', $placeholder=''){

        $tagarray = array('name'=> $name, 'type' => $type, 'id' => $id );
        if ($type != 'checkbox') $tagarray['value'] = $value;
        if ($type != 'hidden') $tagarray['value'] = $value;
        if ($type == 'checkbox' && $value == 'on') $tagarray['checked'] = 'checked';
        if ($type == 'radio' && $value == $radiocheck) $tagarray['checked'] = 'checked';
        if (($type == 'text' || $type == 'number') && $placeholder != '') $tagarray['placeholder'] = $placeholder;
        echo $this->cb_generate_input($tagarray);
    }

    function cb_add_label($for, $value, $id='', $class=''){
        $tag ='<label for="'.$for.'">'.$value.'</label>'."\n";

        echo $tag;
    }

    function cb_add_input_tag($input){
        $type   = $input['type'];
        $tagarray = array('name'=> $input['name'], 'type' => $input['type'], 'id' => $input['id'] );
        if ($type != 'checkbox') $tagarray['value'] = $value;
        //if ($type != 'hidden') $tagarray['value'] = $value;
        if ($type == 'checkbox' && $value != '') $tagarray['checked'] = 'checked';
        if ($type == 'radio' && $value == $radiocheck) $tagarray['checked'] = 'checked';
        if (($type == 'text' || $type == 'number') && $placeholder != '') $tagarray['placeholder'] = $placeholder;
        echo $this->cb_generate_input($tagarray);
    }
}