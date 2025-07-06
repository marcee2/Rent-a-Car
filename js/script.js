document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("top-vehicles");

    if (container) {
        fetch("api/get_top_vehicles.php")
            .then(res => res.json())
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = "<p class='text-muted'>Trenutno nema popularnih vozila.</p>";
                    return;
                }
                container.innerHTML = "";  //deletes old cards

                data.forEach((vehicle, index) => {
                    const card = document.createElement("div");

                    // Dodaj osnovnu klasu
                    card.className = "col-md-4 mb-4";

                    // Ako je poslednji red i samo 1 element u redu (npr. 10. vozilo)
                    if ((data.length % 3 === 1) && index === data.length - 1) {
                        card.className += " mx-auto"; // centrira ga horizontalno
                    }

                    card.innerHTML = `
        <div class="card h-100 shadow-sm">
            <a href="vehicle.php?id=${vehicle.id}">
            <img src="${vehicle.image}" class="card-img-top" alt="${vehicle.model}"> 
            </a>

            <div class="card-body">
                <h5 class="card-title">
                    <a href="vehicle.php?id=${vehicle.id}" class="text-decoration-none text-dark">
                        ${vehicle.name} ${vehicle.model}
                    </a>
                </h5>
                <p class="card-text">
                    <strong>Godište:</strong> ${vehicle.year}<br>
                    <strong>Gorivo:</strong> ${vehicle.fuel_type}<br>
                    <strong>Menjač:</strong> ${vehicle.gearbox}<br>
                    <strong>Sedišta:</strong> ${vehicle.seats}<br>
                    <strong>Cena:</strong> ${vehicle.price_per_day} € / dan
                </p>
            </div>
        </div>
    `;
                    container.appendChild(card);
                });

            })
            .catch(error => {
                container.innerHTML = "<p class='text-danger'>Greška pri učitavanju vozila.</p>";
                console.error("Greška:", error);
            });
    }
});
