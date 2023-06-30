console.log("hello from js");
document.addEventListener('DOMContentLoaded', function(){

    let btnMark = document.getElementById("comment_submit");
    let selectMark = document.getElementById("comment_content");
    
    console.log(btnMark);
    console.log(selectMark);
    selectMark.addEventListener("click", (event) =>
    {
        event.preventDefault();   
        btnMark.hidden = false;
    })
})