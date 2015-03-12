$(function(){
    $(".diff-item").on("click", ".has-diff", function() {
        $('.diff-detail', $(this).parent()).slideToggle();
    });
});
