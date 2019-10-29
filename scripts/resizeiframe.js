function autoResize(id){
    var newheight;
    var newwidth;

    if(document.getElementById){
        newheight=document.getElementById(id).contentWindow.document .body.scrollHeight;
        newwidth=document.getElementById(id).contentWindow.document .body.scrollWidth;
    }

    //if(newheight<580){
	//newheight=580;
    //}

    document.getElementById(id).height= (newheight) + 30 + "px";
    document.getElementById(id).width= (newwidth) + 20 + "px";
}
