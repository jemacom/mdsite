$(function(){
    // make navigation folders collapse intelligently until needed
    // collapse all the folders first
    $("header ul li > ul").each(function(){
        $(this).parent("li").addClass("folder").addClass("collapsed");
    });
    if($("header ul li.selected").length > 0){
        // expand a path to the selected item
        $("header ul li.selected").parents("li.folder.collapsed").removeClass("collapsed");
    } else{
        // on the homepage, just expand the entire first level of folders
        $("header > ul > li.folder.collapsed").removeClass("collapsed");
    }
    // allow the user to toggle folders
    $("header ul li.folder > a").click(function(){
        $(this).parent("li").toggleClass("collapsed");
        return false;
    });
});