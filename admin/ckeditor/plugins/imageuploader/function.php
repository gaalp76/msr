<?php
if (!isset($_SESSION["menu"])) session_start();
if(isset($_GET["f"])){
    $f = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING);
    if($f = "loadImages") {
        loadImages();
    }
}

function loadImages() {
    require(__DIR__ . '/pluginconfig.php');
    if(file_exists($useruploadpath)){
        
        $filesizefinal = 0;
        $count = 0;
        
        $dir = $useruploadpath;
        //echo "useruploadpath: ".$useruploadpath;
        if($stmt = $db->prepare("SELECT uploadmanager_folder.id, uploadmanager_folder.name, uploadmanager_folder.folder_name  from uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id  WHERE uploadmanager_folder.linked_to = ? AND folder_language.lang = ? "))
        {
            $stmt->bind_param("ss", $_SESSION["linkedTo"], $config->LANG_DEFAULT);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc())
            {
                $files = glob($dir.$row["folder_name"]."/*.{jpg,jpe,jpeg,png,gif,ico}", GLOB_BRACE);
                if(count($files))
                {
                    
                    echo "<div class='file-container'>";
                    echo "<div class='news-album' style='cursor:pointer; font-size: calc(0.6rem + 0.8vw); width:100%; clear:both;' folder_id='".$row["id"]."' folder_name='".$row["folder_name"]."'><span class='open_close'>+ </span>".$row["name"]."</div>";
                    echo "<div class='file-list' style='display:none'>";
                    
                    usort($files, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
                    for($i=count($files)-1; $i >= 0; $i--):
                        $image = $files[$i];
                        $image_pathinfo = pathinfo($image);
                        $image_extension = $image_pathinfo['extension'];
                        $image_filename = $image_pathinfo['filename']; //ADATBÁZISBÓL
                        $image_basename = $image_pathinfo['basename'];
                        
                        if(strpos($image_filename, "thumb_") !== false) continue;
                       
                       
                        $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
                        $site = $protocol. $_SERVER['SERVER_NAME'] .'/';
                        $image_url = $site.$useruploadfolder.$row["folder_name"]."/".$image_basename;
                        $size = getimagesize($image);
                        $image_height = $size[0];
                        $file_size_byte = filesize($image);
                        $file_size_kilobyte = ($file_size_byte/1024);
                        $file_size_kilobyte_rounded = round($file_size_kilobyte,1);
                        $filesizetemp = $file_size_kilobyte_rounded;

                        $filesizefinal = round((float)$filesizefinal + $filesizetemp);
                        $calcsize = round($filesizefinal + $filesizetemp);
                        $filesizefinal .= " KB";
                        $count = ++$count;
                        
                        if($file_style == "block") { ?>
                            <div class="fileDiv"
                                 onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo $image_basename; ?>');"
                                 ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $image_basename; ?>');"
                                 data-imgid="<?php echo $count; ?>">
                                <div class="imgDiv"><img class="fileImg lazy" src="<?php echo $image_url; ?>" ></div>
                                <p class="fileDescription"><span class="fileMime"><?php echo $image_extension; ?></span> <?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                                <p class="fileTime"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                                <p class="fileTime"><?php echo $filesizetemp; ?> KB</p>
                            </div>
                        <?php } elseif($file_style == "list") { ?>
                            <div class="fullWidthFileDiv"
                                 onclick="showEditBar('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $count; ?>','<?php echo $image_basename; ?>');"
                                 ondblclick="showImage('<?php echo $image_url; ?>','<?php echo $image_height; ?>','<?php echo $image_basename; ?>');"
                                 data-imgid="<?php echo $count; ?>">
                                <div class="fullWidthimgDiv"><img class="fullWidthfileImg lazy" data-original="<?php echo $image_url; ?>"></div>
                                <p class="fullWidthfileDescription"><?php echo $image_filename; ?><?php if($file_extens == "yes"){echo ".$image_extension";} ?></p>
                                
                                <div class="qEditIconsDiv">
                                    <img title="Delete File" src="img/cd-icon-qtrash.png" class="qEditIconsImg" onclick="window.location.href = 'imgdelete.php?img=<?php echo $image_basename; ?>'">
                                </div>
                                
                                <p class="fullWidthfileTime fullWidthfileMime fullWidthlastChild"><?php echo $image_extension; ?></p>
                                <p class="fullWidthfileTime"><?php echo $filesizetemp; ?> KB</p>
                                <p class="fullWidthfileTime fullWidth30percent"><?php echo date ("F d Y H:i", filemtime($image)); ?></p>
                            </div>
                        <?php }
                        

                    endfor;
                    if($count == 0){
                        echo "<div class='fileDiv' style='display:none;'></div>";
                        $calcsize = 0;
                    }
                    if($calcsize == 0){
                        $filesizefinal = "0 KB";
                    }
                    if($calcsize >= 1024){
                        $filesizefinal = round($filesizefinal/1024,1) . " MB";
                    }

                echo "</div>";
                echo "</div>";
            }

            };
            echo "
                <script>
                    $('.news-album').click(function(e) {
                        $('.file-list').not( $(this).siblings('.file-list')).hide();
                        $(this).siblings('.file-list').toggle();
                        $('.open_close').html('+ '); 
                        if($(this).siblings('.file-list').is(':visible'))
                        {
                            $(this).find('.open_close').html('- ');
                        }
                        else
                        {
                            $(this).find('.open_close').html('+ '); 
                        }
                    });
                    $( '#finalsize' ).html('$filesizefinal');
                    $( '#finalcount' ).html('$count');
                </script>
                ";
        }


        
    } else {
        echo '<div id="folderError">'.$alerts9.' <b>'.$useruploadfolder.'</b> '.$alerts10;
    } 
}

function pathHistory() {
    require(__DIR__ . '/pluginconfig.php');
    $latestpathes = array_slice($foldershistory, -3);
    $latestpathes = array_reverse($latestpathes);
    foreach($latestpathes as $folder) {
        echo '<p class="pathHistory" onclick="useHistoryPath(\''.$folder.'\');">'.$folder.'</p>';
    }
}