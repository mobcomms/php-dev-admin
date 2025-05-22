document.addEventListener("DOMContentLoaded", function () {
  $(".tab-item").on("click", function () {
    var targetTab = $(this).attr("target-tab");

    $(".tab-item").removeClass("active");
    $(this).addClass("active");

    $(".tab_content").removeClass("active");
    $("#" + targetTab).addClass("active");
  });

  // -----

  $(".file_box").on("click", function () {
    $("#file_input").click();
  });
});

//textArea 글자수 막기
let textarea = document.getElementById("contact_textarea");
let maxLength = 1000;
function updateCharCount() {
  let charCount = textarea.value.length;
  document.getElementById("text_number").textContent = charCount + "/ 1000";
}
textarea.addEventListener("keyup", function () {
  if (textarea.value.length > maxLength) {
    textarea.value = textarea.value.slice(0, maxLength);
  }
  updateCharCount();
});

updateCharCount();
