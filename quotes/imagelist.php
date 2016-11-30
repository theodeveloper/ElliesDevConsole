// This list may be created by a server logic page PHP/ASP/ASPX/JSP in some backend system.
// There images will be displayed as a dropdown in all image dialogs if the "external_link_image_url"
// option is defined in TinyMCE init.

var tinyMCEImageList = new Array(
	// Name, URL
        <?php 
$icount = -1;
if ($handle = opendir('media/')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $icount += 1;
            if ($icount > 0) { print ",\n"; }
            print "[\"".$entry."\", \"quotes/media/".$entry."\"]";
        }
    }
    closedir($handle);
}
        ?>
);