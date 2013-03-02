<?php
// work in the app root directory instead of /res
chdir('..');

// Note: APC must be installed to enable the cache
define('USE_CACHE', false);
define('NAV_TTL', 3600); // cache for one hour

// this is PHP Markdown Extra, from http://michelf.ca/projects/php-markdown/
// you can replace it with ordinary PHP Markdown if you want
require 'res/markdown.php';

function get_title($page){
    $re = "/<h1>(.+)<\/h1>/i";
    if(preg_match($re, $page, $matches)){
        return $matches[1];
    } else{
        return false;
    }
}

function list_pages($dir = "."){
    $list = array();
    foreach(scandir($dir) as $filename){
        if($filename == "." or $filename == "..") continue;
        $filepath = "$dir/$filename";
        if(is_file($filepath) and substr($filename, -3) == ".md"){
            // if it's a page, remove the ".md"
            $list[substr($filename, 0, -3)] = substr($filepath, 2, -3);
        } elseif(is_dir($filepath) and list_pages($filepath) != false){
            // if it's a directory, recurse into it to return a nested array
            $list[$filename] = list_pages($filepath);
        }
    }
    return $list;
}

function build_nav_ul($pages, $current_path = false){
    $output = "<ul>\n";
    foreach($pages as $name => $value){
        if(is_array($value) and empty($value))
            continue;
        if($name == "index")
            continue;
        
        if(is_array($value)){
            // really hackish way to get the directory path out of its first item
            $path = "/".substr(reset($value), 0, strrpos(reset($value), "/"));
        } else{
            $path = "/".$value;
        }

        if($path == $current_path){
            $output .= "<li class=\"selected\">";
        } else{
            $output .= "<li>";
        }
        $output .= "<a href=\"$path\">$name</a>";
        if(is_array($value)){
            $output .= build_nav_ul($value, $current_path);
        }
        $output .= "</li>\n";
    }
    return $output."</ul>\n";
}

// get request path, with preceding slash
// for this to work, all requests that aren't files should be routed like so:
//   RewriteCond %{REQUEST_URI} !-f
//   RewriteRule ^ res/render.php
$path = $_SERVER['REQUEST_URI'];
if(substr($path, -1) == "/" and $path != "/"){
    // remove trailing slash if present, for consistency
    $path = substr($path, 0, -1);
}

// filepaths are relative to the app root (ends up as ./path/to/file)
$filepath = ".".$path;
if(is_dir(realpath($filepath))){
	if($path != "/") $filepath .= "/"; // directories get a trailing slash
    // if this is a directory, look for a directory index file
	$filepath .= "index.md";
} else{
    if(file_exists($filepath.".redirect")){
        // if a .redirect file exists for this path, follow it
        $target = file_get_contents($filepath.".redirect");
        header("Location: $target");
        die();
    }
	// the path is a clean url, so append .md to look for the page file
    $filepath .= ".md";
}

// clean and resolve filepath (it's absolute now)
$filepath = realpath($filepath);
if(strpos($filepath, getcwd()) === false){
    // the path must contain the path to the app root, ie. be within it
    // this foils attackers who try something like example.com/../../../../etc/passwd
    $filepath = false;
}

if($filepath){
    // if all went well, load the page file
    $raw = file_get_contents($filepath);
    // and render it.
    $content = Markdown($raw);
} else{
	// the page doesn't exist or is somehow invalid
	header("HTTP/1.1 404 Not Found");
	$content = "<h1>Page not found</h1>\n";
	if(is_dir(realpath(".".$path))){
		// if it's a directory, make a helpful index page
		$folder_contents = list_pages(".$path");
		
		if($folder_contents){
		    $content .= "<p>No index page has been provided for this folder. It contains the following pages:</p>\n";
		    $content .= build_nav_ul($folder_contents);
		    $content .= "<p>To remove this message, create a file here named index.md.</p>";
		} else{
		    $content .= "<p>No index page has been provided for this folder. "
		              . "To remove this message, create a file here named index.md.</p>";
		}
	} else{
		// otherwise, just a normal 404 page
		$content .= "<p>The page you requested does not exist.</p>";
	}
}

// find page title
$page_title = get_title($content);
if(!$page_title) $page_title = "Untitled page";

// make a copy of the content, but with the first h1 removed
$content_notitle = preg_replace('/<h1>.+<\/h1>/i', '', $content, 1);

if(USE_CACHE and apc_exists('nav')){
	$nav_output = apc_fetch('nav');
} else{
    // build the navigation list
	$nav_output = build_nav_ul(list_pages(), $path);
	if(USE_CACHE) apc_store('nav', $nav_output, NAV_TTL);
}

// get the template...
$template = file_get_contents('res/layout.htm');
// render it...
$template_keys = array('{nav}', '{pageurl}', '{title}');
$template_insert = array($nav_output, htmlspecialchars($path), htmlentities($page_title));
$template = str_ireplace($template_keys, $template_insert, $template);
if(strpos($template, '{contentbody}')){
	$template = str_ireplace('{contentbody}', $content_notitle, $template);
} else{
	$template = str_ireplace('{content}', $content, $template);
}
// and output the finished page
echo $template;
?>
