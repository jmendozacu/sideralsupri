Event.observe(window, 'message', function(e){
    if (e.data.action == 'setHeight')
    {
        var height = e.data.height;
        $('amasty_store').setStyle({height: height+'px'});
    }
});

document.observe("dom:loaded", function() {
    /* Open information tab by default*/
    $$('[id*="_amasty_information-head"').each(function(element){
        var parent = element.parentNode;
        if (parent) {
            var fieldSet = parent.next('fieldset');
            if (fieldSet) {
                fieldSet.show();
                parent.removeClassName('collapseable');
            }
        }
    });
});
