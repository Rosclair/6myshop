function confirmCheck(event) {
    const url = this.value;
    if (this.checked) {
        $.ajax({
            url : url,
            type : "POST",
        })
    }else {
        const url = this.value;
        $.ajax({
            url : url,
            type : "POST",
        })
    }
}
document.querySelectorAll('input.js-onSale').forEach(function(checkbox){
    checkbox.addEventListener('change', confirmCheck);
})