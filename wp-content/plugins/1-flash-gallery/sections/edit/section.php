<?php

function fgallery_create_section(){

    fgallery_edit_section(0);

}

function fgallery_create_fp_section(){

    fgallery_edit_section(0, true);

}

function fgallery_edit_section($id, $flipbook = false){

    require_once FGALLERY_ABSPATH.'/includes/render/configurator.php';
    require_once 'lib.php';
    require_once 'view.php';
    
    $page = new fgallery_edit_page;
    $data = new fgallery_edit_page_lib;
    
    $page->album = $data->init_album($id);

    if ($page->album['gall_type'] == 13) $flipbook = true;
    
    $page->available_galleries = $data->get_galleries_types($flipbook);
    
    $page->view($id); 
}

?>