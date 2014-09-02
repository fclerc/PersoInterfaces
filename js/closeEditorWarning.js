//when user leaves the page, asking him whether he is sure

function closeEditorWarning(){
    return 'It looks like you have been editing something -- if you leave before submitting your changes will be lost.'
}

window.onbeforeunload = closeEditorWarning