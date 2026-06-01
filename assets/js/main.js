document.addEventListener("DOMContentLoaded", function () {
    // Các logic JS chung khác của web (nếu có)
});

function closeModal() {
    document.getElementById("successModal").style.display = "none";
}
function openEditProduct(btn){
    const data = JSON.parse(btn.dataset.product);
    console.log(data);
}