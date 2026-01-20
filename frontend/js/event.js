fetch("http://localhost/tiket-event/backend/api/events.php")
    .then(response => response.json())
    .then(result => {
        const container = document.getElementById("event-list");
        container.innerHTML = "";

        if (result.data.length === 0) {
            container.innerHTML = "<p>Belum ada event.</p>";
            return;
        }

        result.data.forEach(event => {
            const div = document.createElement("div");
            div.innerHTML = `
                <h3>${event.title}</h3>
                <p>Kategori: ${event.category}</p>
                <p>Tanggal: ${event.event_date}</p>
                <p>Harga: Rp ${event.price}</p>
                <hr>
            `;
            container.appendChild(div);
        });
    })
    .catch(error => {
        document.getElementById("event-list").innerHTML = "Gagal load data";
        console.error(error);
    });
