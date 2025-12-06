<?php
/**
 * Overview for Components
 * Version 2.0
 */

// Create a components admin page
add_action('admin_menu', 'components_setup_menu');
function components_setup_menu(){
    add_submenu_page( 'edit.php?post_type=acf-field-group', 'Components setup page', 'Componentes', 'manage_options', 'components', 'components_setup_content' );
}

function components_setup_notice($message, $type = 'success', $close = 'admin.php?page=components') { ?>
    <div class="notice-<?php echo $type; ?> notice is-dismissible"><p><?php echo $message; ?></p>
    <?php if($close) echo '<a class="notice-dismiss" href="'.$close.'" style="text-decoration:none"></a>'; ?>
    </div><?php
}

function components_setup_content() {
    /** 1. Load component data **/
    // get components directory structure
    $dir = get_template_directory().'/components/';
    // get components json and decode the fields
    $components_json = json_decode(file_get_contents($dir.'group_components.json'),true);
    // get components xml manifesto
    $components_manifest = simplexml_load_file($dir.'manifest.xml');

    /** 2. Components actions: import and export
     * */
    if(isset($_GET['generate'])) {
        if (preg_match('/^[\w\-]{3,20}$/i', $_GET['generate'])) {
            $compname = preg_replace('/[^\w\-]/', '', $_GET['generate']);
            if(!is_dir($dir.$compname)) mkdir($dir.$compname);
            if(!file_exists($dir.$compname.'/'.$compname.'.php')) {
                $result = file_put_contents($dir.$compname.'/'.$compname.'.php',
                    "<section class=\"row component component-$compname\">\r\n\t<div class=\"col-12\">"
                    ."\r\n\t\t<h2>Component name: $compname</h2>\r\n\t\t"
                    .'<textarea style="width:100%;height:300px;padding:10px;border:0"><?php print_r($field);?></textarea>'
                    ."\r\n\t</div>\r\n</section>");
                if(isset($result) && !file_exists($dir.$compname.'/'.$compname.'.scss')) $result = file_put_contents($dir.$compname.'/'.$compname.'.scss',
                    ".component-$compname {\r\n  \r\n  @include sm {\r\n    \r\n  }\r\n}");
                if($result) components_setup_notice('The "'.$compname.'" component\'s view and stylesheet has been generated.');
                else components_setup_notice('The file "'.$dir.$compname.'/'.$compname.'.php" could not be generated.','error');
            }
        } else components_setup_notice('The component name "'.htmlspecialchars($_GET['generate']).'" contains unsafe characters or is too long.','error');
    }
    if(isset($_GET['setblock'])) {
        if (preg_match('/^[\w\-]{3,20}$/i', $_GET['setblock'])) {
            $compname = preg_replace('/[^\w\-]/', '', $_GET['setblock']);
            $new = $components_manifest->addChild("component");
            $new->addChild("name",$compname);
            $new->addChild("title",ucfirst(str_replace('-',' ',$compname)));
            $new->addChild("block",'pegasus');
            $new->addChild("icon",'plus-alt');
            if($components_manifest->asXml($dir.'manifest.xml')) {
                $content = ['key'=>'group_'.str_replace('-','',$compname),'title'=>ucfirst($compname),'modified'=>date('U'),
                    'location' => [[['param' => 'block', 'operator' => '==','value' => 'acf/'.$compname]]]];
                if(file_put_contents($dir.'group_'.str_replace('-','',$compname).'.json', json_encode($content,JSON_PRETTY_PRINT))) {
                    components_setup_notice(ucfirst($compname).' block created! <a href="edit.php?post_type=acf-field-group&post_status=sync">Synchronize now</a>');
                } else components_setup_notice('Added block to manifest but creating json file for ACF fields failed.','error');
            } else components_setup_notice('Adding block to manifest failed.','error');
        } else components_setup_notice('The component name "'.htmlspecialchars($_GET['generate']).'" contains unsafe characters or is too long.','error');
    }
    if(isset($_GET['delete'])) {
        if (preg_match('/^[\w\-]{3,20}$/i', $_GET['delete'])) {
            $compname = preg_replace('/[^\w\-]/', '', $_GET['delete']);
            $groupname = 'group_'.str_replace('-','',$compname).'.json';
            if(empty($_GET['deleteconfirm'])) {
                components_setup_notice('Component "'.$compname.'" set to be deleted. <br/><span class="trash"><span class="dashicons dashicons-trash"></span> '
                    .'<a style="color:brown" href="admin.php?page=components&deleteconfirm=yes&delete='.$compname.'">'.__('Confirm').'</a></span>'
                , 'warning');
            } else {
                if(is_dir($dir.$compname)) {
                    delete_files($dir.$compname.'/');
                    $log = '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Deleted "/'.$compname.'/" directory and files.</strong>';
                } else $log = '<strong style="color:brown"><span class="dashicons dashicons-no"></span> Not found "/components/'.$compname.'/" directory".</strong>';
                if(file_exists($dir.$groupname)) {
                    if(unlink($dir.$groupname)) {
                        $log .= '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Deleted "'.$groupname.'" ACF file.</strong>';
                    } else $log .= '<strong style="color:brown"><span class="dashicons dashicons-no"></span> ACF file "'.$groupname.'" could not be deleted".</strong>';
                }
                for($i=0;$i<count($components_manifest); $i++) { //  $components_manifest as $key=>$compx
                    if($components_manifest->component[$i]->name == $compname ) {
                        unset($components_manifest->component[$i]);
                        if($components_manifest->asXml($dir.'manifest.xml')) {
                            $log .= '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Removed "'.$compname.'" XML entry.</strong>';
                        } else $log .= '<strong style="color:brown"><span class="dashicons dashicons-no"></span> The modified XML manifest could not be saved.</strong>';
                    }
                }
                components_setup_notice('Removing component "'.$compname.'".<br/>'. $log);
            }
        } else components_setup_notice('The component name "'.htmlspecialchars($_GET['delete']).'" contains unsafe characters or is too long.','error');
    }
    elseif(isset($_GET['import_fields'])) {
        if (preg_match('/^[\w\-]{3,20}$/i', $_GET['import_fields'])) {
            $compname = preg_replace('/[^\w\-]/', '', $_GET['import_fields']);
            $seed = json_decode(file_get_contents($dir.$compname.'/seed.json'),true);
            foreach($components_json['fields'][0]['layouts'] as $index => $comp) {
                if($comp['name'] == $compname) $existing = $index;
            }   ;
            if(empty($_GET['overwrite']) && isset($existing)) {
                components_setup_notice('The backend of '.get_bloginfo('name').' already contains a component called "'.$compname.'". '
                    .'<br> Do you want to replace it with a json created '.human_time_diff( filemtime($dir.$compname .'/seed.json'), date('U') ). ' ago?'
                    .'<br><a href="admin.php?page=components&amp;import_fields=' . $compname . '&amp;overwrite=yes" class="button button-primary">Import and overwrite</a>','warning');
            } else {
                if(isset($existing)) $components_json['fields'][0]['layouts'][$existing] = $seed;
                else array_push($components_json['fields'][0]['layouts'],$seed);
                $components_json['modified'] = date('U');
                $result = file_put_contents($dir.'/group_components.json',json_encode($components_json, JSON_PRETTY_PRINT));
                if($result) components_setup_notice('The backend fields of "'.$compname.'" have been '.(isset($existing)?'modified':'imported').' successfully.');
                else components_setup_notice('An error happened while importing "'.$dir.$compname.'/seed.json".','error');
            }
        } else components_setup_notice('The component name "'.htmlspecialchars($_GET['import_fields']).'" contains unsafe characters or is too long.','error');
    }
    elseif(isset($_GET['export_fields'])) {
        if (preg_match('/^[\w\-]{3,20}$/i', $_GET['export_fields'])) {
            $compname = preg_replace('/[^\w\-]/', '', $_GET['export_fields']);
            foreach($components_json['fields'][0]['layouts'] as $comp)
                if($comp['name'] == $compname) $seed = $comp;
            if(isset($seed)) {
                if(!is_dir($dir.$compname)) mkdir($dir.$compname);
                if(empty($_GET['overwrite']) && file_exists($dir.$compname.'/seed.json')) {
                    components_setup_notice('The existing seed for "'.$compname.'" was created '.human_time_diff( filemtime($dir.$compname .'/seed.json'), date('U') ). ' ago ('.date('j M Y',filemtime($dir.$compname.'/seed.json')).').'
                        .'<br><a href="admin.php?page=components&amp;export_fields='.$compname.'&amp;overwrite=yes" class="button button-primary">Export and update seed</a>','warning');
                } else {
                    $seed['key'] = 'key-'.$compname;
                    $result = file_put_contents($dir.$compname.'/seed.json',json_encode($seed, JSON_PRETTY_PRINT));
                    if($result) components_setup_notice('Fields exported successfully! Find them in <code>components/'.$compname.'/seed.json</code>');
                    else components_setup_notice('An error happened while exporting "'.$dir.$compname.'/seed.json".','error');
                }
            } else components_setup_notice('There is no data for a component called "'.$compname.'".','error');
        } else components_setup_notice('The component name "'.htmlspecialchars($_GET['export_fields']).'" contains unsafe characters or is too long.','error');
    }
    elseif(0&& pegasus_sync_groups()) {
        components_setup_notice('<i class="acf-icon -sync grey small"></i>&nbsp; Modifications found, one component has been synchronized.</a>','warning');
    } ?>

    <div class="wrap" id="components-setup-wrap">
        <h1>Components setup
            <?php $components_group = get_posts(array('name'=>'group_components','post_type'=>'acf-field-group')); ?>
            <a href="post.php?post=<?php echo $components_group[0]->ID; ?>&action=edit" class="page-title-action">Edit Fields</a>
        </h1>
        <?php
        // build a components array to hold the relevant info
        $components = array();
        $components_dir = scandir($dir);
        if($components_dir) foreach($components_dir as $dirname){
            //if is a dir and not a file, add the front end to the array
            if(strpos($dirname,'.')===false) $components[$dirname] = array ('icon' => '', 'title' => ucfirst($dirname));
        }
        if($components_manifest) foreach ($components_manifest as $xml) {
            $xmlname = (string)$xml->name;
            if(empty($components[$xmlname]['title'])) $components[$xmlname]['title'] = ucfirst($xmlname);
            if(isset($xml->functions)) $components[$xmlname]['functions'] = (string)$xml->functions;
            if(isset($xml->block)) $components[$xmlname]['block'] = (string)$xml->block;
            if(isset($xml->title)) $components[$xmlname]['blocktitle'] = (string)$xml->title;
            if(isset($xml->icon)) $components[$xmlname]['icon'] = '<span class="dashicons dashicons-'. (string)$xml->icon .'"></span>';
        }
        if($components_json['fields'][0]['layouts']) foreach($components_json['fields'][0]['layouts'] as $comp)
            $components[$comp["name"]] = isset($components[$comp["name"]]) ? array_merge($components[$comp["name"]],$comp) : $comp;
        ?>
        <ul class="subsubsub">
            <li><a href="edit.php?post_type=acf-field-group&amp;page=components" <?php if(!isset($_GET['filter'])) echo 'class="current"'; ?>>
                    All <span class="count">(<?php echo count($components); ?>)</span></a> |</li>
            <li><a href="edit.php?post_type=acf-field-group&amp;page=components&amp;filter=page"
                    <?php if(isset($_GET['filter']) && $_GET['filter']=='page') echo 'class="current"'; ?>>Page components</a> |</li>
            <li><a href="edit.php?post_type=acf-field-group&amp;page=components&amp;filter=block"
                    <?php if(isset($_GET['filter']) && $_GET['filter']=='block') echo 'class="current"'; ?>>Block components</a> |</li>
            <li><a href="edit.php?post_type=acf-field-group&amp;page=components&amp;filter=other"
                    <?php if(isset($_GET['filter']) && $_GET['filter']=='other') echo 'class="current"'; ?>>Other components</a> </li>
        </ul>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr>
                <td id="cb" class="manage-column column-cb check-column"></td>
                <th scope="col" id="name" class="manage-column column-name column-primary">Component</th>
                <th scope="col" id="frontend" class="manage-column">Files</th>
                <th scope="col" id="backend" class="manage-column">Backend</th>
            </tr></thead>
            <tbody id="the-list" class="acf-field-components-key">
            <?php
            if($components) foreach($components as $name => $data) {
                if(isset($_GET['filter']) && $_GET['filter']=='page' && !isset($data['key'])) continue;
                if(isset($_GET['filter']) && $_GET['filter']=='block' && !isset($data['block'])) continue;
                if(isset($_GET['filter']) && $_GET['filter']=='other' && (isset($data['key']) || isset($data['block']))) continue;
                if(isset($data["label"])) {
                    if(strpos($data["label"],' ')>0) list($data["icon"],$data["title"]) = explode(' ',$data["label"],2);
                    else list($data["icon"],$data["title"]) = array('X',$data["label"]);
                }
                $frontend = file_exists($dir.$name.'/'.$name.'.php') ? 'active' : false;
                $group = file_exists($dir.'group_'.str_replace('-','',$name).'.json') ? 'group_'.str_replace('-','',$name) : false;
                $display = array('block'=>'list-view','row'=>'exerpt-view','table'=>'grid-view');
                ?>
                <tr class="<?php echo $frontend; ?>">
                    <th class="acf-fc-layout-handle check-column num"><?php echo $data["icon"]; ?></th>
                    <td class="column-primary">
                        <strong><?php echo $data["title"]; ?></strong>
                        <div class="row-actions"><?php echo (isset($count)?++$count:$count=1).'. ' ?>
                            <span class="trash"><a href="admin.php?page=components&delete=<?php echo $name; ?>"><?php echo __('Delete'); ?></a></span>
                        </div>
                    </td>
                    <td><?php
                        if($frontend) echo '<span class="dashicons dashicons-yes"></span> View ';
                        elseif(file_exists($dir.$name.'/view.php')) echo '<a href="admin.php?page=components&amp;renameview='.$name.'" class="button">Rename view.php</a> ';
                        else echo '<a href="admin.php?page=components&amp;generate='.$name.'" class="button">Create view file</a> ';
                        if(!empty($data['functions'])) {
                            echo '<span class="dashicons dashicons-'.(file_exists($dir.$name.'/functions.php')?'yes':'no').'"></span> '.ucfirst($data['functions']);
                        } elseif(file_exists($dir.$name.'/functions.php')) {
                            $new = $components_manifest->addChild("component");
                            $new->addChild("name",$name);
                            $new->addChild("functions",'functions.php');
                            if($components_manifest->asXml($dir.'manifest.xml'))
                                echo '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Functions was added to the manifest.</strong>';
                            else echo '<strong style="color:brown"><span class="dashicons dashicons-no"></span> Adding functions to manifest failed.</strong>';
                        }
                        ?></td>
                    <td><?php
                        if(isset($data['key'])) {
                            // Exists in the backend, show actions
                            echo '<span class="dashicons dashicons-'.$display[$data["display"]].'" title="Layout key is '.$data["key"].'"></span> ';
                            if(isset($data['key'])) echo '<a href="admin.php?page=components&amp;export_fields='.$name.'" class="">Export</a> ';
                            if(file_exists($dir.$name.'/seed.json')) echo '<a href="admin.php?page=components&amp;import_fields='.$name.'" class="">Import</a> ';
                            if($data['key'] != 'key-'.$name) echo '<div class="row-actions">Random key name: please export and import!</div>';
                        } elseif($group) {
                            $group_info = get_posts(array('name'=>$group,'post_type'=>'acf-field-group'));
                            if(!empty($group_info)) {
                                echo '<a href="post.php?post='.($group_info&&$group_info[0] ? $group_info[0]->ID :'').'&action=edit">';
                            } else echo '<a href="edit.php?post_type=acf-field-group&post_status=sync" style="color: orange"><i class="acf-icon -sync grey small acf-js-tooltip"></i> ';
                            if(isset($data["block"])) {
                                echo '<span class="dashicons dashicons-format-aside"></span> Block: ';
                                echo (isset($data["blocktitle"]) ? $data["blocktitle"] : str_replace(array('-','_'),' ',ucfirst($name))).' </a>';
                            } else {
                                echo '<span class="dashicons dashicons-welcome-widgets-menus"></span> Group: ';
                                echo ($group_info&&$group_info[0] ? $group_info[0]->post_title :$group).' </a>';
                            }
                        } else {
                            // Not exists currently. If seed exists, import it
                            if(file_exists($dir.$name.'/seed.json')) {
                                array_push($components_json['fields'][0]['layouts'], json_decode(file_get_contents($dir.$name.'/seed.json'),true));
                                $components_json['modified'] = date('U');
                                if(file_put_contents($dir.'/group_components.json',json_encode($components_json, JSON_PRETTY_PRINT)))
                                    echo '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Seed was successfully imported.</strong>';
                                else echo '<strong style="color:brown"><span class="dashicons dashicons-no"></span> Seed import has failed.</strong>';
                            } elseif(file_exists($dir.$name.'/group_'.str_replace('-','',$name).'.json')) {
                                if(copy($dir.$name.'/group_'.str_replace('-','',$name).'.json' , $dir.'group_'.str_replace('-','',$name).'.json'))
                                    echo '<strong style="color:green"><span class="dashicons dashicons-yes"></span> Group of fields successfully imported.</strong>';
                                else echo '<strong style="color:brown"><span class="dashicons dashicons-no"></span> Group of fields could not be imported.</strong>';
                            } else echo '<em>Not in the backend</em>'
                                .'<div class="row-actions"><a href="admin.php?page=components&setblock='.$name.'">Set as block</a></div>';
                        }
                        ?></td>
                </tr>
            <?php } else echo '<tr></tr><td></td><td>No components found.</td></tr>'; ?>
            </tbody>
        </table>
    </div>
<?php }

/* 
 * php delete function that deals with directories recursively
 */
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        foreach( $files as $file ){
            delete_files( $file );      
        }
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}