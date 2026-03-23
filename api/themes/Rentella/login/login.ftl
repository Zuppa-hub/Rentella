<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Rentella</title>
    <link rel="stylesheet" href="${url.resourcesPath}/styles.css">
</head>
<body>
    <div class="auth-shell">
        <section class="auth-panel">
            <header class="auth-topbar">
                <img class="auth-logo" src="${url.resourcesPath}/assets/LogoDark.svg" alt="Rentella" />
            </header>

            <main class="auth-main">
                <div class="auth-main-inner">
                    <h1 class="auth-title">Welcome to Rentella</h1>
                    <p class="auth-subtitle">The easiest way to rent an Umbrella on the beach.</p>

                    <#if message?has_content>
                        <div class="auth-message auth-message-${message.type}">${kcSanitize(message.summary)?no_esc}</div>
                    </#if>

                    <form id="kc-form-login" class="auth-form" action="${url.loginAction}" method="post">
                        <div class="input-group">
                            <label for="username" class="input-label">Username</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                class="input-field"
                                placeholder="Type Email here"
                                autofocus
                                autocomplete="username"
                                value="${(login.username!'')}"
                            />
                        </div>

                        <div class="input-group">
                            <label for="password" class="input-label">Password</label>
                            <div class="password-wrap">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="input-field input-field-password"
                                    placeholder="Type Password here"
                                    autocomplete="current-password"
                                />
                                <button type="button" class="password-eye" aria-label="Show password">
                                    <svg class="eye-icon eye-off" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M2 12C3.9 8.7 7.2 6.5 11 6.1M15.8 6.8C18.4 7.9 20.4 9.9 22 12C19.7 16 16.2 18 12 18C10.6 18 9.3 17.8 8.1 17.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M2 2L22 22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <svg class="eye-icon eye-on" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M2 12C3.9 8.2 7.1 6 12 6C16.9 6 20.1 8.2 22 12C20.1 15.8 16.9 18 12 18C7.1 18 3.9 15.8 2 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="forgot-wrap">
                            <a href="${(url.loginResetCredentialsUrl!'#')}" class="forgot-link">Forgot your Password</a>
                        </div>

                        <button type="submit" class="btn-primary">Login</button>

                        <div class="register-section">
                            <p class="register-text">Don't have an Account?</p>
                            <a href="${(url.registrationUrl!'#')}" class="register-link">Register for FREE</a>
                        </div>
                    </form>
                </div>
            </main>

            <div id="geo-modal" class="geo-modal" role="dialog" aria-modal="true" aria-labelledby="geo-modal-title" aria-describedby="geo-modal-description">
                <div class="geo-modal-card">
                    <button type="button" class="geo-close" data-geo-close aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h2 id="geo-modal-title" class="geo-title">Location is needed</h2>
                    <p id="geo-modal-description" class="geo-text">In order to provide you with the best experience and to ensure accurate results, we need to know your location. You can use your Current Location or set it Manually</p>
                    <div class="geo-actions">
                        <button type="button" class="geo-action-secondary" data-geo-close>Set Location</button>
                        <button type="button" id="geo-use-current" class="geo-action-primary">Use Current</button>
                    </div>
                </div>
            </div>
        </section>

        <aside class="auth-visual" aria-hidden="true">
            <img src="${url.resourcesPath}/assets/BeachFromAbove.jpeg" alt="" class="auth-visual-image" />
        </aside>
    </div>

    <script>
        (function () {
            // Configuration constants for geo-location cookie
            var GEO_COOKIE_CONFIG = {
                NAME: 'rentella_geo_after_auth',
                VALUE: '1',
                MAX_AGE: 900, // 15 minutes
            };

            var form = document.getElementById('kc-form-login');
            var passwordInput = document.getElementById('password');
            var toggleButton = document.querySelector('.password-eye');

            function markGeoPromptAfterAuth() {
                if (!form) return;
                // Share flag across Keycloak/app ports via cookie on localhost
                var cookieStr = GEO_COOKIE_CONFIG.NAME + '=' + GEO_COOKIE_CONFIG.VALUE + 
                                '; Path=/; Max-Age=' + GEO_COOKIE_CONFIG.MAX_AGE + 
                                '; SameSite=Lax';
                document.cookie = cookieStr;
            }

            if (passwordInput && toggleButton) {
                toggleButton.addEventListener('click', function () {
                    var isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleButton.classList.toggle('is-visible', isPassword);
                    toggleButton.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                });
            }

            if (form) {
                form.addEventListener('submit', function () {
                    markGeoPromptAfterAuth();
                });
            }
        })();
    </script>
</body>
</html>