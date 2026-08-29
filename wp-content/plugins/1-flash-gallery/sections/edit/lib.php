<?php

//Config

class fgallery_edit_page_lib {
    
    private $FGALLERY_AVAILABLE_COMMON_GALLERIES;
    private $FGALLERY_AVAILABLE_FPGROUP_GALLERIES;
    
    function __construct(){
    
        //Config
        
        $this->FGALLERY_AVAILABLE_COMMON_GALLERIES = array(
            1 => 'acosta',
            2 => 'airion',
            3 => 'arai',
            4 => 'pax',
            5 => 'pazin',
            6 => 'postma',
            7 => 'pageflip',
            8 => 'nilus',
            9 => 'nusl',
            10 => 'kranjk',
            11 => 'perona',
            12 => 'ables'
        );
        $this->FGALLERY_AVAILABLE_FPGROUP_GALLERIES = array(
            13 => 'flipbook'
        );
    }
    
    function init_album($id){
        
        $album = fgallery_get_album($id);
        if (empty($album)) {
            $album['gall_id'] = 0;
            $album['gall_width'] = 450;
            $album['gall_height'] = 385;
            $album['gall_bgcolor'] = "ffffff";
            $album['gall_type'] = 3;
            $album['gall_transparentbg'] = 0;
        }
        
        return $album;
                
    }
    
    function get_galleries_types($flipbook = false){
        
        if($flipbook == false) return $this->FGALLERY_AVAILABLE_COMMON_GALLERIES;
        else return $this->FGALLERY_AVAILABLE_FPGROUP_GALLERIES;
        
    }
    
}


?>
