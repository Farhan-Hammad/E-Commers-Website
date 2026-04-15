<?php
// MUST BE FIRST - No output before this!
require_once 'classes/User.php';

$user = new User();
if ($user->isLoggedIn()) {
    header('Location: /E-Commers-Website/index.php');
    exit;
}

$pageTitle = 'Register - MyStore';
$message = '';
$messageType = '';

// Process form BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->register([
        'email'       => $_POST['email'] ?? '',
        'password'    => $_POST['password'] ?? '',
        'first_name'  => $_POST['first_name'] ?? '',
        'last_name'   => $_POST['last_name'] ?? '',
        'phone'       => $_POST['phone'] ?? '',
        'address'     => $_POST['address'] ?? '',
        'city'        => $_POST['city'] ?? '',
        'country'     => $_POST['country'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? ''
    ]);

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';

    // Redirect immediately on success (before any HTML)
    if ($result['success']) {
        header("Location: login.php?registered=1");
        exit;
    }
}

// NOW include header (after all PHP processing)
require_once 'includes/header.php';
?>

<!-- Premium Particle Canvas Background -->
<canvas id="particle-canvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none;"></canvas>

<div class="auth-container">
    <h2>📝 Create Your Account</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType === 'error' ? 'danger' : 'success' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="first_name" class="form-label">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password * (min 6 characters)</label>
            <input type="password" id="password" name="password" class="form-control" minlength="6" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" class="form-control" rows="2"></textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="city" class="form-label">City</label>
                <input type="text" id="city" name="city" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" id="country" name="country" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<script>
    // Premium Particle System – exactly as in login.php
    (function() {
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        let mouse = {
            x: null,
            y: null,
            radius: 140
        };

        const PARTICLE_COUNT = 80;
        const CONNECT_DISTANCE = 180;
        const SHAPES = ['circle', 'triangle', 'diamond', 'star'];

        function getThemeColors() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                primary: isDark ? '#8b5cf6' : '#d97706',
                secondary: isDark ? '#6366f1' : '#f59e0b',
                accent: isDark ? '#a78bfa' : '#fbbf24',
                bgStart: isDark ? '#0b0f19' : '#fef3c7',
                bgEnd: isDark ? '#1e1b4b' : '#fde68a',
                particleBase: isDark ? 'rgba(139, 92, 246, ' : 'rgba(217, 119, 6, ',
                connectionBase: isDark ? 'rgba(99, 102, 241, ' : 'rgba(245, 158, 11, '
            };
        }

        function random(min, max) {
            return Math.random() * (max - min) + min;
        }

        function createParticle() {
            return {
                x: Math.random() * width,
                y: Math.random() * height,
                vx: random(-0.8, 0.8),
                vy: random(-0.8, 0.8),
                size: random(2.5, 7),
                shape: SHAPES[Math.floor(Math.random() * SHAPES.length)],
                opacity: random(0.4, 1.0),
                rotation: Math.random() * Math.PI * 2,
                rotSpeed: random(-0.02, 0.02)
            };
        }

        function initParticles() {
            particles = [];
            for (let i = 0; i < PARTICLE_COUNT; i++) {
                particles.push(createParticle());
            }
        }

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
            initParticles();
        }

        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });
        window.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        function drawShape(ctx, x, y, size, shape, rotation, opacity, color) {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rotation);
            ctx.fillStyle = color;
            ctx.shadowColor = color;
            ctx.shadowBlur = 12;

            switch (shape) {
                case 'triangle':
                    ctx.beginPath();
                    ctx.moveTo(0, -size);
                    ctx.lineTo(size * 0.866, size * 0.5);
                    ctx.lineTo(-size * 0.866, size * 0.5);
                    ctx.closePath();
                    break;
                case 'diamond':
                    ctx.beginPath();
                    ctx.moveTo(0, -size);
                    ctx.lineTo(size, 0);
                    ctx.lineTo(0, size);
                    ctx.lineTo(-size, 0);
                    ctx.closePath();
                    break;
                case 'star':
                    ctx.beginPath();
                    for (let i = 0; i < 5; i++) {
                        const angle = (i * 72 - 90) * Math.PI / 180;
                        const x1 = Math.cos(angle) * size;
                        const y1 = Math.sin(angle) * size;
                        const x2 = Math.cos(angle + 36 * Math.PI / 180) * (size * 0.5);
                        const y2 = Math.sin(angle + 36 * Math.PI / 180) * (size * 0.5);
                        if (i === 0) ctx.moveTo(x1, y1);
                        else ctx.lineTo(x1, y1);
                        ctx.lineTo(x2, y2);
                    }
                    ctx.closePath();
                    break;
                default:
                    ctx.beginPath();
                    ctx.arc(0, 0, size, 0, Math.PI * 2);
                    break;
            }
            ctx.fill();
            ctx.shadowBlur = 0;
            ctx.restore();
        }

        function draw() {
            const colors = getThemeColors();
            const isDark = document.documentElement.classList.contains('dark');

            const gradient = ctx.createLinearGradient(0, 0, width, height);
            gradient.addColorStop(0, colors.bgStart);
            gradient.addColorStop(1, colors.bgEnd);
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, width, height);

            for (let p of particles) {
                if (mouse.x !== null && mouse.y !== null) {
                    const dx = p.x - mouse.x;
                    const dy = p.y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < mouse.radius) {
                        const force = (mouse.radius - dist) / mouse.radius;
                        const angle = Math.atan2(dy, dx);
                        p.vx += Math.cos(angle) * force * 0.8;
                        p.vy += Math.sin(angle) * force * 0.8;
                    }
                }

                p.x += p.vx;
                p.y += p.vy;
                p.vx *= 0.995;
                p.vy *= 0.995;
                p.rotation += p.rotSpeed;

                if (p.x < 0 || p.x > width) p.vx *= -0.9;
                if (p.y < 0 || p.y > height) p.vy *= -0.9;

                p.x = Math.max(0, Math.min(width, p.x));
                p.y = Math.max(0, Math.min(height, p.y));

                if (Math.random() < 0.02) {
                    p.vx += random(-0.15, 0.15);
                    p.vy += random(-0.15, 0.15);
                }
            }

            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < CONNECT_DISTANCE) {
                        const opacity = (1 - distance / CONNECT_DISTANCE) * 0.5;
                        const gradient = ctx.createLinearGradient(
                            particles[i].x, particles[i].y,
                            particles[j].x, particles[j].y
                        );
                        gradient.addColorStop(0, colors.connectionBase + (opacity * 0.9) + ')');
                        gradient.addColorStop(1, colors.connectionBase + opacity + ')');

                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = gradient;
                        ctx.lineWidth = 1.8;
                        ctx.stroke();
                    }
                }
            }

            for (let p of particles) {
                const color = p.shape === 'star' ? colors.accent :
                    (p.shape === 'triangle' ? colors.secondary : colors.primary);
                drawShape(ctx, p.x, p.y, p.size, p.shape, p.rotation, p.opacity,
                    colors.particleBase + p.opacity + ')');
            }

            requestAnimationFrame(draw);
        }

        let animationId;

        function startAnimation() {
            if (animationId) cancelAnimationFrame(animationId);
            animationId = requestAnimationFrame(draw);
        }

        function stopAnimation() {
            if (animationId) {
                cancelAnimationFrame(animationId);
                animationId = null;
            }
        }

        document.addEventListener('visibilitychange', () => {
            document.hidden ? stopAnimation() : startAnimation();
        });

        window.addEventListener('resize', resizeCanvas);

        resizeCanvas();
        startAnimation();
    })();
</script>

<?php require_once 'includes/footer.php'; ?>