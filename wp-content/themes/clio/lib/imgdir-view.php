<?php
/**
 * Overview for Components
 * Version 2.0
 */

// Create a components admin page
add_action('admin_menu', 'imgdir_view_menu');
function imgdir_view_menu(){
    add_submenu_page( 'upload.php', 'Imágenes importadas', 'Importadas', 'edit_posts', 'imgdir', 'imgdir_view_content' );
}

if(!function_exists('dirToArray')){
    function dirToArray($dir) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                $result[] = $value;
                }
            }
        }
        return $result;
    }
}

function imgdir_view_content() {
    $uploads = wp_upload_dir();
    $imgdir = dirToArray($uploads['basedir'].'/img/');
    ?>
    <style type="text/css">
        a.imported {
            padding: 5px;
            display: inline-block;
            position: relative;
            width: 150px;
            height: 150px;
        }
        a.imported img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid white;
            outline: 1px solid lightgrey;
        }
        a.imported:hover img {
            position: absolute;
            background: white;
            transform: scale(2);
            z-index: 5;
            outline: 1px solid #007cba;
            object-fit: contain;
        }
    </style>
    <div class="wrap" id="imgdir-view-wrap">
        <h1>Imágenes importadas de Instagram
            <?php $components_group = get_posts(array('name'=>'group_components','post_type'=>'acf-field-group')); ?>
            <a href="upload.php" class="page-title-action"><?php _e('Library'); ?></a>
        </h1>
        <?php //reveal($uploads); ?>
        <div class="media-frame wp-core-ui mode-grid mode-edit hide-menu aligncenter">
            
            <?php foreach($imgdir as $folder => $contents) {
                $output = '<div class="media-toolbar wp-filter">'. $folder .'</div>';
                $folderUrl = $uploads['baseurl'].'/img/'.$folder.'/';
                if(!empty($contents) && is_array($contents)) foreach($contents as $file) {
                    if(in_array(pathinfo($file, PATHINFO_EXTENSION),array('jpg','jpeg','png'))) {
                        $output.= '<a class="imported" href="'. $folderUrl.$file .'" target="_blank">';
                        $output.= '<img src="'. $folderUrl.$file .'" alt=""/>';
                        $output.= '</a>';
                    }
                }
                echo $output;
            } ?>
        </div>
    </div>
<?php }