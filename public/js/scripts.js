document.addEventListener('DOMContentLoaded', function(){
    /****** Note *******/
    let selectMark = document.getElementById("mark_mark");
    let submitMark = document.getElementById("mark_submit");
    let cancelMark = document.getElementById("mark_cancel");
    selectMark.addEventListener("click", (event) =>
    {
        event.preventDefault();   
        submitMark.hidden = false;
        cancelMark.hidden = false;
        cancelMark.addEventListener("click", (event) =>{
            event.preventDefault();   
            submitMark.hidden = true;
            cancelMark.hidden = true;
        })
    })
    /***** Commentaire *****/
    let areaComment = document.getElementById("comment_content");
    let addCommentSubmit = document.getElementById("comment_submit");
    let cancelComment = document.getElementById("comment_cancel");
    areaComment.addEventListener("input", (event) =>
    {  let content = areaComment.value;
        if (content !== '') {
            event.preventDefault();   
            addCommentSubmit.hidden = false;
            cancelComment.hidden = false;
            cancelComment.addEventListener("click", (event) =>{
                event.preventDefault();   
                areaComment.value = '';
                addCommentSubmit.hidden = true;
                cancelComment.hidden = true;
            })
       } else {
        event.preventDefault();   
        addCommentSubmit.hidden = true;
        cancelComment.hidden = true;
    }
    })
})