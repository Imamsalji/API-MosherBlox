<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #0f0f0f;
            color: #e0e0e0;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: linear-gradient(135deg, #1a1a1a 0%, #242424 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .email-header h1 {
            font-size: 28px;
            color: white;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        /* Content */
        .email-content {
            padding: 40px 30px;
        }

        .email-content h2 {
            color: #ffffff;
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .email-content p {
            color: #b0b0b0;
            margin-bottom: 20px;
            font-size: 15px;
        }

        /* Button */
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin: 20px 0;
            border: none;
            cursor: pointer;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Features Section */
        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .feature-item {
            background: rgba(102, 126, 234, 0.1);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .feature-item h3 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .feature-item p {
            color: #909090;
            font-size: 14px;
            margin: 0;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #404040, transparent);
            margin: 30px 0;
        }

        /* Footer */
        .email-footer {
            background: rgba(0, 0, 0, 0.3);
            padding: 30px;
            text-align: center;
            border-top: 1px solid #404040;
        }

        .social-links {
            margin-bottom: 20px;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(102, 126, 234, 0.2);
            border-radius: 50%;
            color: #667eea;
            text-decoration: none;
            text-align: center;
            line-height: 40px;
            margin: 0 8px;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .social-links a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .footer-text {
            color: #707070;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .unsubscribe {
            color: #667eea;
            text-decoration: none;
            font-size: 12px;
        }

        .unsubscribe:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-content {
                padding: 30px 20px;
            }

            .email-footer {
                padding: 20px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .email-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>✨ Selamat Datang di Mosherblox!</h1>
            <p>Kami senang Anda bergabung dengan kami</p>
        </div>

        <!-- Main Content -->
        <div class="email-content">
            <h2>Halo, Pengguna!</h2>
            <p>Terima kasih telah mendaftar dan mempercayai layanan kami. Kami sangat senang memiliki Anda di komunitas
                kami.</p>

            <p>Dengan bergabung bersama kami, Anda sekarang dapat mengakses berbagai fitur menarik yang akan membantu
                meningkatkan produktivitas dan pengalaman digital Anda.</p>

            <!-- CTA Button -->
            <button class="cta-button">Mulai Sekarang →</button>

            <div class="divider"></div>

            <!-- Features -->
            <h2>Apa yang Anda Dapatkan?</h2>
            <div class="features">
                <div class="feature-item">
                    <h3>⚡ Performa Cepat</h3>
                    <p>Pengalaman loading super cepat dan responsif</p>
                </div>
                <div class="feature-item">
                    <h3>🔒 Keamanan Terjamin</h3>
                    <p>Enkripsi end-to-end untuk privasi Anda</p>
                </div>
                <div class="feature-item">
                    <h3>📊 Analytics Canggih</h3>
                    <p>Dashboard insights yang mendalam dan actionable</p>
                </div>
                <div class="feature-item">
                    <h3>🎨 Customizable</h3>
                    <p>Personalisasi sesuai kebutuhan Anda</p>
                </div>
            </div>

            <div class="divider"></div>

            <p>Jika Anda memiliki pertanyaan atau butuh bantuan, jangan ragu untuk menghubungi tim support kami. Kami
                siap membantu 24/7.</p>

            <p style="color: #808080; font-size: 14px;">
                Salam hangat,<br>
                <strong>Tim Kami</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="social-links">
                <a href="#" title="Facebook">f</a>
                <a href="#" title="Twitter">𝕏</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="LinkedIn">in</a>
            </div>

            <div class="footer-text">
                © 2024 Your Company Name. All rights reserved.
            </div>

            <div class="footer-text">
                <a href="#" class="unsubscribe">Berhenti berlangganan</a> |
                <a href="#" class="unsubscribe">Preferensi email</a>
            </div>
        </div>
    </div>
</body>

</html>
