<!-- includes/footer.php -->
</main> <!-- closes <main> from header.php -->

<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">About Us</h5>
                <p class="text-white-50">Your trusted online shopping destination for quality products at great prices.</p>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                    <li><a href="products.php" class="text-white-50 text-decoration-none">Products</a></li>
                    <li><a href="pages/cart.php" class="text-white-50 text-decoration-none">Shopping Cart</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none">Contact Us</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Shipping Info</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Returns Policy</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-uppercase mb-3">Connect</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none"><i class="fab fa-facebook me-2"></i>Facebook</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none"><i class="fab fa-instagram me-2"></i>Instagram</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none"><i class="fab fa-twitter me-2"></i>Twitter</a></li>
                </ul>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-white-50">
            <small>&copy; <?= date('Y') ?> MyStore. All rights reserved.</small>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS (if any) -->
<script src="assets/js/cart.js"></script>
<script>
    (function() {
        const themeToggle = document.getElementById('themeToggle');
        if (!themeToggle) return;
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            html.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            if (html.classList.contains('dark')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
    })();
</script>
</body>

</html>