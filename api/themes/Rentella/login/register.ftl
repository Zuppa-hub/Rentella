<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Rentella</title>
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
                    <h1 class="auth-title">Register</h1>
                    <p class="auth-subtitle">Create an Account for FREE</p>

                    <#if message?has_content>
                        <div class="auth-message auth-message-${message.type}">${kcSanitize(message.summary)?no_esc}</div>
                    </#if>

                    <form id="kc-register-form" class="auth-form" action="${url.registrationAction}" method="post">
                        <div class="input-group">
                            <label for="fullName" class="input-label">Full Name</label>
                            <input
                                id="fullName"
                                name="fullName"
                                type="text"
                                class="input-field"
                                placeholder="e.g. John Doe"
                                autocomplete="name"
                                value="${((register.formData.firstName!'') + ' ' + (register.formData.lastName!''))?trim}"
                                autofocus
                            />
                        </div>

                        <input type="hidden" id="firstName" name="firstName" value="${(register.formData.firstName!'')}" />
                        <input type="hidden" id="lastName" name="lastName" value="${(register.formData.lastName!'')}" />

                        <div class="input-group">
                            <label for="email" class="input-label">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="input-field"
                                placeholder="e.g. example@gmail.com"
                                autocomplete="email"
                                value="${(register.formData.email!'')}"
                            />
                        </div>

                        <input type="hidden" id="username" name="username" value="${(register.formData.username!'')}" />

                        <div class="input-group">
                            <label for="password" class="input-label">Password</label>
                            <div class="password-wrap">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="input-field input-field-password"
                                    placeholder="********"
                                    autocomplete="new-password"
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

                        <div class="input-group">
                            <label for="password-confirm" class="input-label">Repeat Password</label>
                            <div class="password-wrap">
                                <input
                                    id="password-confirm"
                                    name="password-confirm"
                                    type="password"
                                    class="input-field input-field-password"
                                    placeholder="********"
                                    autocomplete="new-password"
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

                        <button type="submit" class="btn-primary">Register</button>

                        <div class="register-section">
                            <a href="${url.loginUrl}" class="register-link">Back to Login</a>
                        </div>
                    </form>
                </div>
            </main>
        </section>

        <aside class="auth-visual" aria-hidden="true">
            <img src="${url.resourcesPath}/assets/BeachFromAbove.jpeg" alt="" class="auth-visual-image" />
        </aside>
    </div>

    <script>
        (function () {
            var fullNameInput = document.getElementById('fullName');
            var firstNameInput = document.getElementById('firstName');
            var lastNameInput = document.getElementById('lastName');
            var emailInput = document.getElementById('email');
            var usernameInput = document.getElementById('username');
            var toggleButtons = document.querySelectorAll('.password-eye');

            function splitFullName() {
                if (!fullNameInput || !firstNameInput || !lastNameInput) return;
                var parts = String(fullNameInput.value || '').trim().split(/\s+/).filter(Boolean);

                if (parts.length === 0) {
                    firstNameInput.value = '';
                    lastNameInput.value = '';
                    return;
                }

                if (parts.length === 1) {
                    firstNameInput.value = parts[0];
                    lastNameInput.value = '';
                    return;
                }

                firstNameInput.value = parts.shift();
                lastNameInput.value = parts.join(' ');
            }

            function syncUsernameFromEmail() {
                if (!emailInput || !usernameInput) return;
                usernameInput.value = String(emailInput.value || '').trim();
            }

            if (fullNameInput) {
                fullNameInput.addEventListener('input', splitFullName);
                splitFullName();
            }

            if (emailInput) {
                emailInput.addEventListener('input', syncUsernameFromEmail);
                syncUsernameFromEmail();
            }

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var wrapper = button.closest('.password-wrap');
                    if (!wrapper) return;

                    var input = wrapper.querySelector('input');
                    if (!input) return;

                    var isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    button.classList.toggle('is-visible', isPassword);
                    button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                });
            });
        })();
    </script>
</body>
</html>