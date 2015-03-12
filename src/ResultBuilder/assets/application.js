$(function(){
    $(".diff-item").on("click", ".summary", function() {
        $('.diff-detail', $(this).parent()).slideToggle();
    });
});
