<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Restore - Rentella</title>
    <link rel="stylesheet" href="${url.resourcesPath}/styles.css">
</head>
<body>
    <div class="auth-shell">
        <section class="auth-panel">
            <header class="auth-topbar">
                <a href="${url.loginUrl}" class="auth-back-link" aria-label="Back to login">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <img class="auth-logo" src="${url.resourcesPath}/assets/LogoDark.svg" alt="Rentella" />
            </header>

            <main class="auth-main">
                <div class="auth-main-inner">
                    <h1 class="auth-title">Forgot your Password ?</h1>
                    <p class="auth-subtitle-secondary">Don't worry, we can restore it</p>

                    <#if message?has_content>
                        <div class="auth-message auth-message-${message.type}">${kcSanitize(message.summary)?no_esc}</div>
                    </#if>

                    <form id="kc-reset-password-form" class="auth-form" action="${url.loginAction}" method="post">
                        <div class="input-group">
                            <label for="username" class="input-label">Email</label>
                            <input
                                id="username"
                                name="username"
                                type="email"
                                class="input-field"
                                placeholder="Type Email here"
                                autofocus
                                autocomplete="username"
                                value="${(auth.attemptedUsername!'')}"
                            />
                        </div>

                        <button type="submit" class="btn-primary">Send Code to Email</button>

                        <div class="register-section">
                            <p class="register-text">Not what you were looking for?</p>
                            <a href="mailto:support@rentella.com" class="register-link">Write Us</a>
                        </div>
                    </form>
                </div>
            </main>
        </section>

        <aside class="auth-visual" aria-hidden="true">
            <img src="${url.resourcesPath}/assets/BeachFromAbove.jpeg" alt="" class="auth-visual-image" />
        </aside>
    </div>
</body>
</html>
