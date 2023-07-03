[...document.getElementsByClassName("password-addon")].forEach(
    function(item) {
        item.addEventListener("click", function() {
            var e = $(this).parent().find(".password-input");
            "password" === e[0].type ? e[0].type = "text" : e[0].type = "password"
        })
    }
);
