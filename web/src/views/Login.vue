<template>
    <body class="h-screen">
        <div class="flex flex-row h-full">
            <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full">
                <!-- Contenuto della prima colonna -->
                <div class="grid grid-rows-4 gap-4 content-center">
                    <div class="bg-LogoLight dark:bg-LogoDark mx-2 mt-1" style="background-repeat: no-repeat;"></div>
                    <hr class="border-b border-gray-300 dark:border-gray-600 mb-2" />
                    <!-- Testo centrato sotto la linea nera -->
                    <div class="flex items-center justify-center">
                        <div class="text-zinc-800 text-3xl font-bold dark:text-zinc-300 text-center" v-show="!isLoginFormVisible">
                            Register</div>
                        <div class="text-zinc-800 text-3xl font-bold dark:text-zinc-300 text-center" v-show="isLoginFormVisible">Welcome
                            to Rentella</div>
                    </div>
                    <p class="flex items-center justify-center text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400 text-center"
                        v-show="!isLoginFormVisible">
                        Create an Account for FREE</p>
                    <p class="flex items-center justify-center text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400 text-center"
                        v-show="isLoginFormVisible">
                        The easiest way to rent an Umbrella on the beach.</p>
                    <form class="mx-6 " >
                        <div class="mb-6" v-show="!isLoginFormVisible">
                            <label for="first-name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Firt
                                Name</label>

                            <input type="text" id="first-name" v-model="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Jhon" required>
                        </div>
                        <div class="mb-6" v-show="!isLoginFormVisible">
                            <label for="Last-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Last name</label>

                            <input type="text" id="last-name" v-model="surname"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Doe" required>
                        </div>
                        <div class="mb-6">
                            <label for="Email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="Email" id="email" v-model="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="john.doe@company.com" required>
                        </div>
                        <div class="mb-6">
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="Password" id="password" v-model="password"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="•••••••••" required>
                        </div>
                        <div class="mb-6" v-show="!isLoginFormVisible">
                            <label for="confirm_password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm
                                password</label>
                            <input type="Repeat password" id="confirm_password" v-model="password_confirm"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="•••••••••" required>
                        </div>
                        <button @click="handleRegistration" 
                            class="w-full h-12 mb-6 px-6 text-indigo-100 transition-colors duration-150 bg-primary rounded-lg focus:shadow-outline hover:bg-hover" v-show="!isLoginFormVisible">
                            Register
                        </button>
                        <button @click="handleLogin"
                            class="w-full h-12 mb-6 px-6 text-indigo-100 transition-colors duration-150 bg-primary rounded-lg focus:shadow-outline hover:bg-hover" v-show="isLoginFormVisible">
                            Login
                        </button>
                    </form>
                    <p class="text-center text-gray-500 dark:text-gray-400" v-show="!isLoginFormVisible">
                    Already have an account? <a class="font-bold" href="#" @click="showLoginForm"><br>Login</a>
                    </p>
                    <p class="text-center text-gray-500 dark:text-gray-400" v-show="isLoginFormVisible">
                        Don’t have an Account? <a class="font-bold" href="#" @click="showLoginForm"><br>Register for free</a>
                    </p>
                </div>
            </div>
            <div class="hidden md:flex flex-3 md:w-4/5 bg-cover bg-center bg-backgroundImage"></div>
        </div>
    </body>
</template>
  
<script lang="ts">

export default {
    components: {
  },
    data() {
        return {
            name: "",
            surname: "",
            email: "",
            password: "",
            password_confirm: "",
            isLoginFormVisible: true,
            modalVisible: false,
        };
    },
    methods: {
        handleLogin() {
            console.log("login");
            //do login stuff
        },
        handleRegistration() {
            console.log("registration");
            //do registratio stuff
        },
        showLoginForm() {
            // Mostra la form di login e nasconde quella di registrazione
            this.isLoginFormVisible = !this.isLoginFormVisible;
            this.email = "";
            this.password = "";
        },
    },
};
</script>
  
<style scoped></style>
  