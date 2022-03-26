<?php

function get_dir_structure($route){
    if (is_dir($route)){
        $handle = opendir($route);
        
        $arr_files = [];
        $directory = '';
        $ignore = false;
        if(file_exists('./phprefresh.json')){
            $config = file_get_contents("./phprefresh.json");
            $ignore = json_decode($config, true)['ignore'];
        }
        while (($file = readdir($handle)) !== false)  {
                
            $complete_route = $route . "/" . $file;

            if ($file != "." && $file != "..") {
                if (is_dir($complete_route)) {
                    $directory = $file;
                    if($directory === 'vendor'){
                        continue;
                    }
                    if($ignore){
                        if(is_integer(array_search($directory, $ignore))){
                            continue;
                        }
                    }
                    $files = get_dir_structure($complete_route);
                    $filesNuevos = [];
                    foreach ($files as $arch){
                        array_push($filesNuevos, $directory . '/'. $arch);
                    }
                    array_push($arr_files, ...$filesNuevos);
                } else {
                    if($ignore){
                        if(is_integer(array_search($file, $ignore))){
                            continue;
                        }
                    }
                    array_push($arr_files, $file);
                }
            }
        }
        
        closedir($handle);
        return $arr_files;
    } else {
        throw "Not a valid directory path";
    }
}
function get_refresh(){
    $route = './';
    $files = get_dir_structure($route);
    
    $refresh_total = 0;
    foreach($files as $file) {
        $file_name = $route.$file;
        if (file_exists($file_name)) {
            $refresh_total += filemtime($file_name);
        }
    }
    $last_refresh = 0;
    if( isset($_COOKIE['refresh_php'])){
        $last_refresh = $_COOKIE['refresh_php'];
    }else{
        if( isset($_GET['refresh_php']) ){
            $last_refresh = $_GET['refresh_php'];
        }
    }
    
    $refresh = 'false';
    if($last_refresh != 0 && $last_refresh != $refresh_total){
        $refresh = 'true';
    }
    setcookie('refresh_php', $refresh_total);

    return $refresh;
}
get_refresh();
header('refresh: '.get_refresh());


echo "
<script type=\"text/javascript\"> 

    async function getRefresh(){
        const refreshCokie = document.cookie.replace(/(?:(?:^|.*;\s*)refresh_php\s*\=\s*([^;]*).*$)|^.*$/, '$1');
        const res = await fetch('/index.php?refresh_php='+refreshCokie)
        const refreshRes = await res.headers.get('refresh')
        return refreshRes;
    }

    const intervalN = setInterval(() => {
        getRefresh()
            .then(res => {
                if(JSON.parse(res)){
                    clearInterval(intervalN)
                    window.location.reload()
                }
            })
    }, 1000);
</script>
";
