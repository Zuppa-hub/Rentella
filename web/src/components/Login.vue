<template>
    <div class="login-page">
        <div class="login-form">
            <h2>Benvenuto</h2>
            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" v-model="username" id="username" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" v-model="password" id="password" required />
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
        <div class="login-image">
            <img src="../assets/img/BeachFromAbove.jpg" alt="background" />
        </div>
    </div>
</template>
  
<script lang="ts">
export default {
    data() {
        return {
            username: '',
            password: ''
        };
    },
    methods: {
        handleLogin() {
            console.log(this.username)
        }
    }
};
</script>
  
<style scoped>
.login-page {
    display: flex;
    height: 100vh;
}

.login-form,
.login-image {
    flex: 1;
}

.login-form {
    padding: 20px;
}

.login-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-group {
    margin-bottom: 10px;
}

label {
    display: block;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 5px;
}

button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0056b3;
}
</style>
  