<?php
include 'includes/db_config.php';
include 'includes/header.php';
?>
<header class="hero-section" style="height: 500px; position: relative;">
    <div class="hero-controls">
        <button id="prev-btn" aria-label="Previous image">&#10094;</button>
        <button id="next-btn" aria-label="Next image">&#10095;</button>
    </div>
    <div class="container bg-overlay text-center">
        <h1 class="fw-bolder">Dobrodošli na Rent a Car SU!</h1>
        <p class="lead">Brzo, jednostavno i pouzdano iznajmljivanje automobila</p>
        <a class="btn btn-lg btn-light mt-3" href="register.php">Registruj se</a>
    </div>
</header>

<!-- Information section -->
<section class="py-5">
    <div class="container px-4">
        <div class="row gx-4">
            <div class="col-md-4 mb-4">
                <h2>🚗 Veliki izbor vozila</h2>
                <p>Od ekonomičnih gradskih automobila do luksuznih SUV i sportskih modela – imamo sve.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h2>🕒 Brza rezervacija</h2>
                <p>Rezervišite vozilo online za manje od 2 minuta – jednostavno i efikasno.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h2>🛡️ Sigurna vožnja</h2>
                <p>Sva vozila su servisirana i osigurana kako biste putovali bez brige.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-light py-5">
    <div class="container text-center">
        <h2 class="fw-bold">Spremni za sledeće putovanje?</h2>
        <p class="mb-4">Pogledajte našu ponudu i rezervišite vozilo odmah!</p>
        <a href="vehicles.php" class="btn btn-primary btn-lg">Pogledaj najpopularnija vozila</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
    "use strict";

    document.addEventListener("DOMContentLoaded", function () { //code executes when page fully loads html/php
        const heroSection = document.querySelector('.hero-section'); //looks for element with class hero.section
        const images = [
            'images/beli.jpg',
            'images/plavi.jpg',
            'images/crni.jpg',
            'images/svetli.jpg'
        ];
        let currentIndex = 0;        //index of first image is 0, named beli.jpg
        let intervalId;

        const imgElement = document.createElement('img');
        imgElement.style.position = 'absolute';
        imgElement.style.top = '0';
        imgElement.style.left = '0';
        imgElement.style.width = '100%';
        imgElement.style.height = '100%';
        imgElement.style.objectFit = 'cover';
        imgElement.style.zIndex = '0';
        heroSection.appendChild(imgElement);

        function showImage(index) {
            imgElement.src = images[index];
        }

        function showNextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        }

        function showPreviousImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        }

        function startSlider() {
            intervalId = setInterval(showNextImage, 3000);
        }

        function resetSlider() {
            clearInterval(intervalId);
            startSlider();
        }

        showImage(currentIndex);
        startSlider();

        document.getElementById('next-btn').addEventListener('click', function () {
            showNextImage();
            resetSlider();
        });

        document.getElementById('prev-btn').addEventListener('click', function () {
            showPreviousImage();
            resetSlider();
        });
    });
</script>
