<?php

/**
 * Choose which sites you want to check, some take more time than others and
 * will mess up your system. Filsonic takes a long time for many files!
 *
 * true: means you want to check it
 * false: means you dont want to check it
 */
define('CHECK_MEGAUPLOAD', true);
define('CHECK_HOTFILE', true);
define('CHECK_RAPIDSHARE', true);
define('CHECK_FILESONIC', true);
define('CHECK_FILESERVE', true);

//---------------------------------------------------------------------------------------------------------------------
//------------------The following are functions for the MU/RS/HF linkchecker modification------------------------------
//---------------------------------------------------------------------------------------------------------------------

$FLAGS = array();

//Sets all flags to default!
$FLAGS['array'] = array();
$FLAGS['dead'] = false;
$FLAGS['num_dead'] = 0;
$FLAGS['num_alive'] = 0;
$FLAGS['total_size'] = 0;
$FLAGS['total_links'] = 0;

function print_flags(&$FLAGS) {
    print $FLAGS['dead'] . "<br />";
    print $FLAGS['num_dead'] . "<br />";
    print $FLAGS['num_alive'] . "<br />";
    print $FLAGS['total_size'] . "<br />";
    print $FLAGS['total_links'] . "<br />";
}

function do_reg($text, $regex) {
    preg_match_all($regex, $text, $result, PREG_PATTERN_ORDER);
    return $result = $result[0];
}

function send_data($site, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}

function send_links($array_of_links) { //$array_of_links is an array of mu links
    $link_checking_site = "http://www.megaupload.com/mgr_linkcheck.php?";

    $string_links = "";
    for ($i = 0; isset($array_of_links[$i]); $i++) {
        if ($i == 0) {
            $string_links .= "id" . $i . "=" . str_replace("http://www.megaupload.com/?d=", "", $array_of_links[$i]);
        } else {
            $string_links .= "&id" . $i . "=" . str_replace("http://www.megaupload.com/?d=", "", $array_of_links[$i]);
        }
    }
    return send_data($link_checking_site, $string_links);
}

function parse_link_data($string_of_link_data) {
    $megaupload_return_string = "/(&id[0-9]+=[0-9])(&s=[0-9]+&d=[0-9]+&n=[_a-zA-Z0-9\.:\- ]+)?/";
    $array_of_link_data = do_reg($string_of_link_data, $megaupload_return_string);
    return $array_of_link_data;
}

function change_code($text, &$FLAGS) {
    $text = preg_replace("/\[code:(.*?)\](.*?)\[\/code:(.*?)\]/s",
                    '[code:$1]$2[/code:$3]',
                    $text);

    $text = preg_replace("/&#58;/", ":", $text);
    $text = preg_replace("/&#46;/", ".", $text);

    $text = replace_links($text, $FLAGS);

    $text = preg_replace("/:/", "&#58;", $text);
    $text = preg_replace("/\./", "&#46;", $text);

    return $text;
}

function byte_converter($numb) {
    $num = 0;
    $type = " B";
    if ($numb < 1024) {
        $type = " B";
    } else if ($numb < 1048576) {
        $type = " KB";
        $num = $numb / 1024;
    } else if ($numb < 1073741824) {
        $type = " MB";
        $num = $numb / 1048576;
    } else {
        $type = " GB";
        $num = $numb / 1073741824;
    }

    return "(" . number_format($num, 2, '.', '') . $type . ")";
}

function get_info($text, &$FLAGS) {

    $megaupload_link_regex = "/http:\/\/www\.megaupload\.com\/\?d=[a-zA-Z0-9]+/";
    $megaupload_return_string = "/(&id[0-9]+=[0-9])(&s=[0-9]+&d=[0-9]+&n=[_a-zA-Z0-9\.:\- ]+)?/";

    $array_of_links = do_reg($text, $megaupload_link_regex);
    $string_of_link_info = send_links($array_of_links);

    $array_of_link_data = parse_link_data($string_of_link_info);

    $return_array = array();
    $return_text_for_array = "";

    for ($i = 0; isset($array_of_link_data[$i]); $i++) {
        parse_str($array_of_link_data[$i], $valueArray);
        $id = "id" . $i;

        if ($valueArray[$id] == 0) {
            $Image = "<img src=\"http://rambo9.tk/checkmark_green.png\" width=10></img>";
            $Size = $valueArray['s'];
            $Name = "<span style='color:#000'>" . $valueArray['n'] . "</span>";

            //TESTING 12/25/2010
            $FLAGS['num_alive'] += 1;
            $FLAGS['total_size'] += $Size;
            $FLAGS['total_links'] += 1;
        } else {
            $Image = "<img src=\"http://rambo9.tk/DEAD.jpg\" width=10></img>";
            $Size = '';
            $Name = 'Invalid Link';

            //TESTING 12/25/2010
            array_push($FLAGS['array'], $array_of_links[$i]);
            $FLAGS['num_dead'] += 1;
            $FLAGS['total_links'] += 1;
            $FLAGS['dead'] = true;
        }

        if ($Name == "Invalid Link") {
            $return_text_for_array = $Image . " " . "<span style='color:red'>" . $array_of_links[$i] . " "
                    . $Name . " " . $Size . "</span>";
        } else {
            $return_text_for_array = $Image . " " . $array_of_links[$i] . " " . $Name . " " . byte_converter($Size)
                    . "";
        }
        $return_array[$i] = $return_text_for_array;
    }
    return $return_array;
}

function print_info($text) {
    $array_info = get_info($text);
    $array_text = "";
    for ($i = 0; isset($array_info[$i]); $i++) {
        $array_text .= $array_info[$i] . "<br />";
    }
    return $array_text;
}

function get_rs_data($text) {
    $site = "http://api.rapidshare.com/cgi-bin/rsapi.cgi?";
    $rapidshare_link = "/http:\/\/rapidshare\.com\/files\/[a-zA-Z0-9]+\/[a-zA-Z0-9\.\-_]+/";

    $files_pattern = '/\/files\/([^\/]*)\//';
    $filename_pattern = '/\/files\/.*\/(.*)/';

    $array_files = array();
    $array_filename = array();

    $array_of_links = do_reg($text, $rapidshare_link);

    //following sets filenames and file ids in 2 arrays
    for ($i = 0; isset($array_of_links[$i]); $i++) {
        preg_match($files_pattern, $array_of_links[$i], $matches_id);
        preg_match($filename_pattern, $array_of_links[$i], $matches_name);

        $array_files[$i] = $matches_id[1];
        $array_filename[$i] = $matches_name[1];
    }

    $count = 1;
    $id_data = "";
    $filename = "";

    $array_of_total_raw_data = array();
    $index_num = 0;

    for ($i = 0; isset($array_filename[$i]); $i++) {
        if ($count < 60) {
            if ($count == 1) {
                $id_data = $array_files[$i];
                $filename = $array_filename[$i];
            } else {
                $id_data .= "," . $array_files[$i];
                $filename .= "," . $array_filename[$i];
            }
            $count++;
        }

        if ($count == 60 || !isset($array_filename[$i + 1])) {
            $data = "sub=checkfiles_v1&files=" . $id_data . "&filenames=" . $filename;
            $raw_data = send_data($site, $data); //now have a huge list of raw data...
            $array_of_raw_data =
                    do_reg($raw_data, "/[0-9]+,[a-zA-Z0-9\.\-_]+,[0-9]+,[0-9]+,[0-9]+,[a-zA-Z0-9]+,[a-zA-Z0-9]+/");
            for ($j = 0; isset($array_of_raw_data[$j]); $j++) {
                //
                $array_of_total_raw_data[$index_num] = $array_of_raw_data[$j];
                $index_num++;
            }
            $count = 1;
        }
    }
    return $array_of_total_raw_data;
}

function parse_info($text, &$FLAGS) {
    $array_of_rs_data = get_rs_data($text);
    $array_of_parsed_data = array();
    for ($i = 0; isset($array_of_rs_data[$i]); $i++) {
        list($fId, $fName, $fSize, $fServerId, $fStatus, $fShortHost, $fmd5) = explode(',', $array_of_rs_data[$i]);

        if ($fSize == 0) {
            $array_of_parsed_data[$i] = "<img src=\"http://rambo9.tk/DEAD.jpg\" width=10></img>"
                    . "<span style='color:red'> http://rapidshare.com/files/" . $fId . "/" . $fName . " Invalid Link</span>";
            array_push($FLAGS['array'], "http://rapidshare.com/files/" . $fId . "/" . $fName);
            $FLAGS['num_dead'] += 1;
            $FLAGS['total_links'] += 1;
            $FLAGS['dead'] = true;
        } else {
            $array_of_parsed_data[$i] =
                    "<img src=\"http://rambo9.tk/checkmark_green.png\" width=10></img>" .
                    " http://rapidshare.com/files/" . $fId . "/" . $fName . " " . byte_converter($fSize);
            $FLAGS['num_alive'] += 1;
            $FLAGS['total_size'] += $fSize;
            $FLAGS['total_links'] += 1;
        }
    }
    return $array_of_parsed_data;
}

function replace_rs_links($text, &$FLAGS) {
    //replace www rs links
    $text = preg_replace("/http:\/\/www\.rapidshare\.com/", "http://rapidshare.com", $text);

    $rapidshare_link = "/http:\/\/rapidshare\.com\/files\/[a-zA-Z0-9]+\/[a-zA-Z0-9\.\-_]+/";
    $array_of_links = do_reg($text, $rapidshare_link);
    if (sizeof($array_of_links) == 0) {
        return $text;
    }

    for ($i = 0; isset($array_of_links[$i]); $i++) {
        $link = str_replace("http://rapidshare.com/files/", "http://rapidshare\.com/files/", $array_of_links[$i]);
        $link = str_replace("/", "\/", $link);
        $link = "/" . $link . "/";
        $array_of_links[$i] = $link; //array of links is now array of patterns
    }

    $array_of_info = parse_info($text, $FLAGS);

    $array_of_links = array_unique($array_of_links);
    $array_of_info = array_unique($array_of_info);

    $array_of_links = array_values($array_of_links);
    $array_of_info = array_values($array_of_info);

    ksort($array_of_links);
    ksort($array_of_info);
    return preg_replace($array_of_links, $array_of_info, $text);
}

function get_hf_data($text) {
    //http://hotfile.com/dl/4767914/7a8e406/The.Boondocks.S01E15.DVDRip.XviD-TOPAZ.avi.html

    $site = "api.hotfile.com";
    $hotfile_regex = "/http:\/\/hotfile.com\/dl\/[0-9]+\/[a-zA-Z0-9]+\//";

    $id_key_regex = "/\/dl\/([0-9]+)\/([a-zA-Z0-9]+)\//";

    $array_ids = array(); //1
    $array_keys = array(); //2

    $array_of_links = do_reg($text, $hotfile_regex);

    $array_of_links = array_unique($array_of_links);
    $array_of_links = array_values($array_of_links);

    for ($i = 0; isset($array_of_links[$i]); $i++) {
        preg_match($id_key_regex, $array_of_links[$i], $matches_ids_keys);

        $array_ids[$i] = $matches_ids_keys[1];
        $array_keys[$i] = $matches_ids_keys[2];
    }

    $count = 1;
    $ids = "";
    $keys = "";

    $array_of_total_raw_data = array();
    $index_num = 0;

    for ($i = 0; isset($array_ids[$i]); $i++) {
        if ($count < 25) {
            if ($count == 1) {
                $ids = $array_ids[$i];
                $keys = $array_keys[$i];
            } else {
                $ids .= "," . $array_ids[$i];
                $keys .= "," . $array_keys[$i];
            }
            $count++;
        }

        if ($count == 25 || !isset($array_ids[$i + 1])) {
            $data = "action=checklinks&keys=" . $keys . "&ids=" . $ids
                    . "&fields=id,status,name,size,md5,sha1";
            $raw_data = send_data($site, $data);

            $array_of_raw_data =
                    do_reg($raw_data, "/[0-9]+,[0-9],[a-zA-Z0-9\.\-_\[\]\(\) ]*,[0-9]*,[a-zA-Z0-9]*,[a-zA-Z0-9]*/");
            for ($j = 0; isset($array_of_raw_data[$j]); $j++) {
                $array_of_total_raw_data[$index_num] = $array_of_raw_data[$j];
                $index_num++;
            }
            $count = 1;
        }
    }
    return $array_of_total_raw_data;
}

function parse_hf_info($text, &$FLAGS) {
    $array_of_hf_data = get_hf_data($text);

    $hotfile_regex = "/http:\/\/hotfile.com\/dl\/[0-9]+\/[a-zA-Z0-9]+\//";
    $array_of_links = do_reg($text, $hotfile_regex);

    //print "Size of array of links is: " . sizeof($array_of_links) . "<br />";
    //print "Size of array of hf data is: " . sizeof($array_of_hf_data) . "<br />";

    $array_of_parsed_data = array();
    for ($i = 0; isset($array_of_hf_data[$i]); $i++) {
        list($fId, $fStatus, $fName, $fSize, $fMd5, $fSha1)
                = explode(',', $array_of_hf_data[$i]);
        if ($fStatus == 0) {
            $array_of_parsed_data[$i] = "<img src=\"http://rambo9.tk/DEAD.jpg\" width=10></img> "
                    . "<span style='color:red'>" . $array_of_links[$i] . " Invalid Link</span>";
            array_push($FLAGS['array'], $array_of_links[$i]);
            $FLAGS['num_dead'] += 1;
            $FLAGS['total_links'] += 1;
            $FLAGS['dead'] = true;
        } else {
            $array_of_parsed_data[$i] =
                    "<img src=\"http://rambo9.tk/checkmark_green.png\" width=10></img> " .
                    $array_of_links[$i] . "<span style='color:#000'>" . str_replace(' ', '_', $fName) . "</span> " . byte_converter($fSize);
            $FLAGS['num_alive'] += 1;
            $FLAGS['total_size'] += $fSize;
            $FLAGS['total_links'] += 1;
        }
    }
    return $array_of_parsed_data;
}

function replace_hf_links(&$text, &$FLAGS) {

    $text = preg_replace("/http:\/\/www\.hotfile\.com/", "http://hotfile.com", $text);
    $text = preg_replace("/http:\/\/hotfile\.com\/dl\/([0-9]+)\/([a-zA-Z0-9]+)(\/([a-zA-Z0-9\.\-_\[\]\(\)]+))/",
                    'http://hotfile.com/dl/$1/$2/',
                    $text);

    $hotfile_link = "/http:\/\/hotfile.com\/dl\/[0-9]+\/[a-zA-Z0-9]+\//";

    $array_of_links = do_reg($text, $hotfile_link);
    if (sizeof($array_of_links) == 0) {
        return $text;
    }

    for ($i = 0; isset($array_of_links[$i]); $i++) {
        $link = str_replace("http://hotfile.com/dl/", "http://hotfile\.com/dl/", $array_of_links[$i]);
        $link = str_replace("/", "\/", $link);
        $link = "/" . $link . "/";
        $array_of_links[$i] = $link; //array of links is now array of patterns
    }

    $array_of_info = parse_hf_info($text, $FLAGS);

    $array_of_links = array_unique($array_of_links);
    $array_of_info = array_unique($array_of_info);

    $array_of_links = array_values($array_of_links);
    $array_of_info = array_values($array_of_info);

    ksort($array_of_links);
    ksort($array_of_info);

    return preg_replace($array_of_links, $array_of_info, $text);
}

function get_fs_data(&$text) {

    $site = "http://fileserve.com/link-checker.php";
    $fileserve_regex = "/http:\/\/fileserve\.com\/file\/[a-zA-Z0-9]+/";

    $array_of_links = do_reg($text, $fileserve_regex);

    $array_of_links = array_unique($array_of_links);
    $array_of_links = array_values($array_of_links);

    $total_raw_data = "";
    $links = "";
    $count = 1;

    for ($i = 0; isset($array_of_links[$i]); $i++) {
        if ($count < 100) {
            if ($count == 1) {
                $links = $array_of_links[$i] . "%0D%0A";
            } else {
                $links .= $array_of_links[$i] . "%0D%0A";
            }
            $count++;
        }

        if ($count >= 100 || !isset($array_of_links[$i + 1])) {
            $data = "submit=Check+Urls&urls=" . $links;
            $data = preg_replace("/\//", "%2F", $data);
            $data = preg_replace("/:/", "%3A", $data);
            $raw_data = send_data($site, $data);
            $total_raw_data .= $raw_data . "\n";
            $count = 1;
        }
    }
    return $total_raw_data;
}

function parse_fs_info(&$text, &$FLAGS) {
    $total_raw_data = get_fs_data($text);
    $total_raw_data = preg_replace("/(\\t)|(\\r\\n)/", "", $total_raw_data);

    //The following clears the template to avoid errors in parsing!
    $title_fileserve_regex = "/<td>URLs<\/td>[ ]+<td>File Name<\/td><td>Size<\/td><td>Status<\/td><\/tr>/";
    $total_raw_data = preg_replace($title_fileserve_regex, "", $total_raw_data);

    //The following does the real work of super parsing the return data/page
    $fileserve_linkchecker_page_parsing_regex = "/<td>(http:\/\/fileserve\.com\/file\/[a-zA-Z0-9]+)\\r<\/td>[ ]+<td>([a-zA-Z0-9\.\-_\[\]\(\)$\'& ]+)<\/td><td>([a-zA-Z0-9\.\- ]+)<\/td><td>(Available&nbsp;<img src=\"((http:\/\/209\.222\.19\.4\/images\/green_alerts\.jpg)|(\/images\/green_alerts\.jpg))\"\/>|Not available)<\/td>/";

    $array_of_raw_data = do_reg($total_raw_data, $fileserve_linkchecker_page_parsing_regex);

    $array_of_parsed_data = array();
    for ($i = 0; isset($array_of_raw_data[$i]); $i++) {
        $array_of_single_parsed_data = do_reg($array_of_raw_data[$i], $fileserve_linkchecker_page_parsing_regex);
        $array_of_single_parsed_data[0] = preg_replace("/(<tr><td>)|(<\/tr><\/td>)|(<\/td><td>)|(\\r<\/td>[ ]+<td>)/", "%BREAK%", $array_of_single_parsed_data[0]);
        $array_of_single_parsed_data[0] = preg_replace("/<td>/", "", $array_of_single_parsed_data[0]);
        list($fLink, $fName, $fSize, $fStatus) = explode('%BREAK%', $array_of_single_parsed_data[0]);

        if ($fName == "--" || $fSize == "--") {
            $array_of_parsed_data[$i] = "<img src=\"http://rambo9.tk/DEAD.jpg\" width=10></img> "
                    . "<span style='color:red'>" . $fLink . "/ Invalid Link</span>";
            array_push($FLAGS['array'], $fLink);
            $FLAGS['num_dead'] += 1;
            $FLAGS['total_links'] += 1;
            $FLAGS['dead'] = true;
        } else {
            $array_of_parsed_data[$i] =
                    "<img src=\"http://rambo9.tk/checkmark_green.png\" width=10></img> " .
                    $fLink . "<span style='color:#000'>" . $fName . "</span> (" . ($fSize) . ")"; //SPACE
            $FLAGS['num_alive'] += 1;
            $FLAGS['total_links'] += 1;
        }

        $array_of_parsed_data['array_of_links'][$i] = $fLink;
    }
    return $array_of_parsed_data;
}

function replace_fs_links(&$text, &$FLAGS) {
    //clean up text
    $text = preg_replace("/http:\/\/www\.fileserve\.com/", "http://fileserve.com", $text);
    $text = preg_replace("/http:\/\/fileserve\.com\/file\/([a-zA-Z0-9]+)(\/([a-zA-Z0-9\.\-_]+))/",
                    'http://fileserve.com/file/$1',
                    $text);

    $fileserve_link = "/http:\/\/fileserve\.com\/file\/[a-zA-Z0-9]+/";

    $array_of_links = do_reg($text, $fileserve_link);
    if (sizeof($array_of_links) == 0) {
        return $text;
    }

    $array_of_info = parse_fs_info($text, $FLAGS);
    $array_of_links = $array_of_info['array_of_links'];
    unset($array_of_info['array_of_links']);


    for ($i = 0; isset($array_of_links[$i]); $i++) {
        $link = str_replace("http://fileserve.com/file/", "http:\/\/fileserve\.com\/file\/", $array_of_links[$i]);
        $link = "/" . $link . "/";
        $array_of_links[$i] = $link; //array of links is now array of patterns
    }

    $array_of_links = array_unique($array_of_links);
    $array_of_info = array_unique($array_of_info);

    $array_of_links = array_values($array_of_links);
    $array_of_info = array_values($array_of_info);

    ksort($array_of_links);
    ksort($array_of_info);

    $text = preg_replace($array_of_links, $array_of_info, $text);
    return $text;
}

function get_fso_data(&$text) {

    $site = "http://www.filesonic.com/link-checker";
    $filesonic_regex = "/http:\/\/filesonic\.com\/file\/[0-9]+/";

    $array_of_links = do_reg($text, $filesonic_regex);

    $array_of_links = array_unique($array_of_links);
    $array_of_links = array_values($array_of_links);

    $total_raw_data = "";
    $links = "";
    $count = 1;

    for ($i = 0; isset($array_of_links[$i]); $i++) {
        if ($count < 200) {
            if ($count == 1) {
                $links = $array_of_links[$i] . "%0D%0A";
            } else {
                $links .= $array_of_links[$i] . "%0D%0A";
            }
            $count++;
        }

        if ($count >= 200 || !isset($array_of_links[$i + 1])) {
            $data = "redirect=%2Fdashboard&links=" . $links . "&controls%5Bsubmit%5D=";
            $raw_data = send_data($site, $data);
            $total_raw_data .= $raw_data . "\n";
            $count = 1;
        }
    }
    return $total_raw_data;
}

function parse_fso_info(&$text, &$FLAGS) {
    $total_raw_data = get_fso_data($text);

    //The following does the real work of super parsing the return data/page
    $filesonic_linkchecker_page_parsing_regex = "/<td class=\"source\"><span>(http:\/\/filesonic\.com\/file\/[0-9]+)<\/span><\/td>\\r\\n[ ]+<td class=\"fileName\"><span>([a-zA-Z0-9\.\-_\[\]\(\)$\'& ]+)<\/span><\/td>\\r\\n[ ]+<td class=\"fileSize\"><span>([a-zA-Z0-9\.\- ]+)<\/span><\/td>\\r\\n[ ]+<td class=\"availability\"><span>\\r\\n[ ]+<strong style=\"[a-zA-Z0-9\:\;\- ]+\">([a-zA-Z0-9 ]+)<\/strong><br \/>\\r\\n[ ]+/";
    $array_of_raw_data = do_reg($total_raw_data, $filesonic_linkchecker_page_parsing_regex);

    $array_of_parsed_data = array();
    for ($i = 0; isset($array_of_raw_data[$i]); $i++) {
        $array_of_single_parsed_data = do_reg($array_of_raw_data[$i], $filesonic_linkchecker_page_parsing_regex);
        $array_of_single_parsed_data[0] = preg_replace("/(<\/span><\/td>\\r\\n[ ]+<td class=\"fileName\"><span>)|(<\/span><\/td>\\r\\n[ ]+<td class=\"fileSize\"><span>)|(<\/span><\/td>\\r\\n[ ]+<td class=\"availability\"><span>\\r\\n[ ]+<strong style=\"[a-zA-Z0-9\:\;\- ]+\">)/", "%BREAK%", $array_of_single_parsed_data[0]);
        $array_of_single_parsed_data[0] = preg_replace("/(<td class=\"source\"><span>)|(<\/strong><br \/>\\r\\n[ ]+)/", "", $array_of_single_parsed_data[0]);
        list($fLink, $fName, $fSize, $fStatus) = explode('%BREAK%', $array_of_single_parsed_data[0]);

        if ($fName == "-" || $fSize == "-") {
            $array_of_parsed_data[$i] = "<img src=\"http://rambo9.tk/DEAD.jpg\" width=10></img> "
                    . "<span style='color:red'>" . $fLink . "/ Invalid Link</span>";
            array_push($FLAGS['array'], $fLink);
            $FLAGS['num_dead'] += 1;
            $FLAGS['total_links'] += 1;
            $FLAGS['dead'] = true;
        } else {
            $array_of_parsed_data[$i] =
                    "<img src=\"http://rambo9.tk/checkmark_green.png\" width=10></img> " .
                    $fLink . "/<span style='color:#000'>" . $fName . "</span> (" . ($fSize) . ")"; //SPACE
            $FLAGS['num_alive'] += 1;
            $FLAGS['total_links'] += 1;
        }

        $array_of_parsed_data['array_of_links'][$i] = $fLink;
    }
    return $array_of_parsed_data;
}

function replace_fso_links(&$text, &$FLAGS) {
    //clean up text
    $text = preg_replace("/http:\/\/www\.filesonic\.com/", "http://filesonic.com", $text);
    $text = preg_replace("/http:\/\/filesonic\.com\/file\/([0-9]+)(\/([a-zA-Z0-9\.\-_]+))/",
                    'http://filesonic.com/file/$1',
                    $text);

    $filesonic_link = "/http:\/\/filesonic\.com\/file\/[0-9]+/";

    $array_of_links = do_reg($text, $filesonic_link);
    if (sizeof($array_of_links) == 0) {
        return $text;
    }

    $array_of_info = parse_fso_info($text, $FLAGS);
    $array_of_links = $array_of_info['array_of_links'];
    unset($array_of_info['array_of_links']);


    for ($i = 0; isset($array_of_links[$i]); $i++) {
        $link = str_replace("http://filesonic.com/file/", "http:\/\/filesonic\.com\/file\/", $array_of_links[$i]);
        $link = "/" . $link . "/";
        $array_of_links[$i] = $link; //array of links is now array of patterns
    }

    $array_of_links = array_unique($array_of_links);
    $array_of_info = array_unique($array_of_info);

    $array_of_links = array_values($array_of_links);
    $array_of_info = array_values($array_of_info);

    ksort($array_of_links);
    ksort($array_of_info);

    $text = preg_replace($array_of_links, $array_of_info, $text);
    return $text;
}

function replace_links($text, &$FLAGS) {

    if (CHECK_HOTFILE) {
        $text = replace_hf_links($text, $FLAGS);
    }

    if (CHECK_FILESONIC) {
        $text = replace_fso_links($text, $FLAGS);
    }

    if (CHECK_RAPIDSHARE) {
        $text = replace_rs_links($text, $FLAGS);
    }

    if (CHECK_FILESERVE) {
        $text = replace_fs_links($text, $FLAGS);
    }

    if (!CHECK_MEGAUPLOAD) {
        return $text;
    }

    $megaupload_link_regex = "/http:\/\/www\.megaupload\.com\/\?d=[a-zA-Z0-9]+/";
    $array_pattern = do_reg($text, $megaupload_link_regex);
    if (sizeof($array_pattern) == 0) {
        return $text;
    }

    for ($i = 0; isset($array_pattern[$i]); $i++) {
        $array_pattern[$i] = str_replace("http://www.megaupload.com/?d=", "/http:\/\/www\.megaupload\.com\/\?d=", $array_pattern[$i]) . "/";
    }

    $array_of_info = get_info($text, $FLAGS);

    $array_pattern = array_unique($array_pattern);
    $array_of_info = array_unique($array_of_info);

    ksort($array_pattern);
    ksort($array_of_info);

    return preg_replace($array_pattern, $array_of_info, $text);
}


?>
