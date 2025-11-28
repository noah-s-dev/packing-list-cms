<?php
/**
 * Landing Page / Index
 */

require_once 'includes/auth.php';

// Redirect logged-in users to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing List CMS - Organize Your Trips</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>Packing List CMS</h1>
            </div>
            <div class="nav-menu">
                <a href="login.php" class="btn btn-secondary">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="landing-hero">
            <div class="hero-content">
                <h1>Organize Your Trips with Ease</h1>
                <p>Create, manage, and organize packing lists for all your trips and events. Never forget an essential item again!</p>
                
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary">Get Started Free</a>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                </div>
            </div>
        </div>

        <div class="features-section">
            <h2>Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>📝 Easy List Creation</h3>
                    <p>Create packing lists quickly with an intuitive interface. Add items, set quantities, and organize by categories.</p>
                </div>
                
                <div class="feature-card">
                    <h3>📱 Mobile Friendly</h3>
                    <p>Access your packing lists on any device. Perfect for checking items off while you're packing or traveling.</p>
                </div>
                
                <div class="feature-card">
                    <h3>🏷️ Category Organization</h3>
                    <p>Organize items by categories like clothes, electronics, toiletries, and more for better organization.</p>
                </div>
                
                <div class="feature-card">
                    <h3>✅ Progress Tracking</h3>
                    <p>Track your packing progress with visual indicators. See at a glance what's packed and what's left to do.</p>
                </div>
                
                <div class="feature-card">
                    <h3>🔒 Secure & Private</h3>
                    <p>Your packing lists are private and secure. Only you can access your personal travel information.</p>
                </div>
                
                <div class="feature-card">
                    <h3>⚡ Quick & Simple</h3>
                    <p>No complicated features or overwhelming interfaces. Just simple, effective packing list management.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="text-center my-2">
                <div>
                    <span>© 2025 .  </span>
                    <span class="text- ">Developed by </span>
                    <a href="https://rivertheme.com" class="fw-bold text-decoration-none" target="_blank" rel="noopener">RiverTheme</a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .landing-hero {
            text-align: center;
            padding: 4rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            margin-bottom: 3rem;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .features-section {
            margin-bottom: 3rem;
        }

        .features-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            color: #333;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        .demo-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 3rem;
        }

        .demo-section h2 {
            margin-bottom: 1rem;
            color: #333;
        }

        .demo-credentials {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            display: inline-block;
        }

        .demo-credentials p {
            margin: 0.5rem 0;
            font-family: monospace;
            font-size: 1.1rem;
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>

