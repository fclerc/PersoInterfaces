//function used to keep the colons always visible when scrolling and the element is reaching the bottom of the page.
//Not used anymore in this project...RIP
function alwaysVisible(elementId){
    var initialTop = $(elementId).offset().top;

    $(document).scroll(function(){
        var bottomWindowPosition = $(document).scrollTop() + $(window).height();
        
        if(bottomWindowPosition < $(document).height()){
            
            if($(elementId).height() < bottomWindowPosition){
                if($(elementId).height() > $(window).height()){
                    $(elementId).css('margin-top',initialTop + bottomWindowPosition - $(elementId).height());
                }
                else{
                    $(elementId).css('margin-top',initialTop + $(document).scrollTop());
                }
                
            }
            else{
                $(elementId).css('margin-top',initialTop);
            }
        }
    });
}
alwaysVisible('#Rules');
alwaysVisible('#Activities');
alwaysVisible('#ProfileAndContext');