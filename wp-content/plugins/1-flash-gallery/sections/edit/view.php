<?php


class fgallery_edit_page{

    public $album;
    public $available_galleries;
 
    
    function __construct (){
        
    }
    
    function view($id){

    echo '<div class="wrap fgallery">';

        if ($id != 0) {
          echo '<h2>'.__('Edit Gallery', 'fgallery').' "'.$this->album['gall_name'].'"</h2>';
        } else {
          echo '<h2>'.__('Add New Gallery', 'fgallery').'</h2>';
        }
        
        echo '<div id="fgallery_settings_form">';
        
        if (isset($this->album['gall_published']) && $id != 0) {
            echo '<p>'.__('Embed Code', 'fgallery').'</p>';
            echo '<div id="shortcode_view">'.fgallery_do_shortcode($this->album['gall_id']).'</div>';
            echo '<button id="shortcode" rel="'.FGALLERY_PATH.'/swf/ZeroClipboard.swf">'.__('Copy to Clipboard', 'fgallery').'</button>';
        }
        echo '<div id="configurator_wrap">';
            echo sc_params_pane($this->album, $this->available_galleries);
        echo '</div>';
        if ($id != 0):
            echo '<div class="edit_gallery_urls">';
                    
                        $title = __('Add Images', 'fgallery');
                        echo '<a id="add_images" href="'.fgallery_get_addimages_page_url($id).'" 
                                title="'.$title.'" class="thickbox fgallery_action">'.$title.
                              '</a>';
                        /*$title = __('Batch Adding Images', 'fgallery');
                        echo '<a href="'.fgallery_get_album_images_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';*/
                        $title = __('Images List', 'fgallery');
                        echo '<a href="'.fgallery_get_album_images_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';
                    if ($this->album['gall_type'] == 13) {
                        $title = __('Add Galleries', 'fgallery');
                        echo '<a id="add_images" href="'.fgallery_get_addgalleries_page_url($id).'" 
                                title="'.$title.'" class="thickbox fgallery_action">'.$title.
                              '</a>';
                        $title = __('Galleries List', 'fgallery');
                        echo '<a href="'.fgallery_get_album_galleries_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';    
                    }
                    $title = __('Save Settings', 'fgallery');
                    echo '<a href="'.fgallery_save_template_page_url($id).'" 
                            title="'.$title.'" class="thickbox fgallery_action">'.$title.
                          '</a>';
                    $title = __('Export Settings', 'fgallery');
                    echo '<a href="'.fgallery_get_export_settings_url($id).'" 
                            title="'.$title.'" class="fgallery_action">'.$title.
                          '</a>';
                    $title = __('Load Settings', 'fgallery');
                    echo '<a href="'.fgallery_load_template_page_url($id).'" 
                            title="'.$title.'" class="thickbox fgallery_action">'.$title.
                          '</a>';
            echo '</div>';
            if (isset($_POST['fgallery_just_added']) && is_numeric($_POST['fgallery_just_added'])) {
                echo '<div id="add_images_box"></div>';
                if ($this->album['gall_type'] == 13) {
                    $url = fgallery_get_addgalleries_page_url($id);
                } else {
                    $url = fgallery_get_addimages_page_url($id);
                }
                echo '<script type="text/javascript">
                        jQuery(document).ready(function(){
                            jQuery("#add_images_box").load("'.$url.'");
                        });
                    </script>';
            } else {
                echo do_shortcode('[fgallery id='.$id.' w='.$this->album['gall_width'].' h='.$this->album['gall_height'].' bg='.$this->album['gall_bgcolor'].' tbg='.$this->album['gall_transparentbg'].' t=0 conf=1]');
            }
        endif;
        echo '</div>';
    echo '</div>';
    

    }
  
    
}

?>