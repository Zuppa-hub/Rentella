<#import "template.ftl" as layout>

<@layout.registrationLayout; section>
    <#if section == "title">
        ${msg("loginTitle")}
    <#elseif section == "header">
        ${msg("loginTitle")}
    <#elseif section == "form">
        <form id="kc-form-login" class="form" action="${url.loginAction}" method="post">
            <div class="form-group">
                <label for="username" class="form-label">${msg("username")}</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    class="form-input"
                    autofocus
                    autocomplete="username"
                    value="${(login.username!'')}"
                />
            </div>

            <div class="form-group">
                <label for="password" class="form-label">${msg("password")}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-input"
                    autocomplete="current-password"
                />
            </div>

            <#if realm.rememberMe?? && realm.rememberMe>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input id="rememberMe" name="rememberMe" type="checkbox" />
                        <span>${msg("rememberMe")}</span>
                    </label>
                </div>
            </#if>

            <div class="form-group">
                <button type="submit" class="form-button">${msg("doLogIn")}</button>
            </div>
        </form>
    </#if>
</@layout.registrationLayout>