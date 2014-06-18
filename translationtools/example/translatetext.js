$(function(){
    $('body').append('hello');
    $('body').append((new Date()));
    $('body').append(icu.getDateFormat('SHORT_PADDED_CENTURY').format((new Date())));
    
    $.ajax({//loading the strategy file, which contains all the required informations (including other files names)
            type: "GET",
            url: 'test.json',
            success: function(data){//get the xml document
                _.setTranslation(data)
                alert(_('June'));
            },
            cache:false
    });
    
    //console.log(icu.getDateFormat);
    //var t=icu.getDateFormat('SHORT_PADDED_CENTURY');
    //console.log(t.format(new Date()));
    //alert(_("Today is {0}", icu.getDateFormat('SHORT_CENTURY').format(new Date())));
});