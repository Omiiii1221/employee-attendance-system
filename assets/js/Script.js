document.querySelector("form").addEventListener("submit", function (e) {
    const username = document.querySelector("input[name='username']").value.trim();
    const password = document.querySelector("input[name='password']").value.trim();

    if (username === "" || password === "") {
        alert("Both fields are required!");
        e.preventDefault();
    }
});
