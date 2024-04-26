const onClick = () => {
    fetch("http://localhost:8089/storage/uploads/2023/08/04/Bot-Development-Best-Practices_uid_64ccdf3784e1e.pdf").then((response) => {
        console.log("Success", response.body);
        return response.blob();
    }).catch((error) => { alert(error.message); });
}