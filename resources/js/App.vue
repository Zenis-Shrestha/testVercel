<template>
    <div style="padding:20px">

        <!-- NAVBAR -->
        <div class="navbar">
            <div class="nav-left">
                <span class="brand">Auth Demo</span>
            </div>

            <div class="nav-right">

                <!-- Logged OUT -->
                <template v-if="!isAuthenticated">
                    <button class="nav-btn" @click="page = 'login'">Login</button>
                    <button class="nav-btn" @click="page = 'register'">Register</button>
                </template>

                <!-- Logged IN -->
                <template v-else>
                    <button class="nav-btn" @click="page = 'dashboard'">Dashboard</button>
                    <button class="nav-btn" @click="me">Me</button>
                    <button class="nav-btn" @click="page = 'users'">Users</button>
                    <button class="nav-btn danger" @click="logout">Logout</button>
                </template>

                <button class="nav-btn ghost" @click="page = 'about'">About</button>
            </div>
        </div>

        <!-- PAGES RENDER HERE -->

        <Login v-if="page === 'login'" @login-success="goToDashboard" />

        <Register v-else-if="page === 'register'" />

        <Dashboard v-else-if="page === 'dashboard'" />

        <!-- USERS LIST -->
        <Users
            v-else-if="page === 'users'"
            @go-create="page = 'users-create'"
        />

        <!-- CREATE USER PAGE -->
        <UserCreate
            v-else-if="page === 'users-create'"
            @done="page = 'users'"
        />

        <Aboutme v-else />

    </div>
</template>

<script setup>
import axios from "axios";
import { ref, computed, onMounted } from "vue";

import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import Aboutme from "./components/Aboutme.vue";
import Dashboard from "./components/Dashboard.vue";
import UserCreate from "./components/UserCreate.vue";
import Users from "./components/Users.vue";

// page navigation
const page = ref("login");

const token = ref(localStorage.getItem("token") || null);
const isAuthenticated = computed(() => !!token.value);

function goToDashboard() {
    page.value = "dashboard";
    token.value = localStorage.getItem("token");
}

function setAxiosToken() {
    const t = localStorage.getItem("token");
    if (t) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
        token.value = t;
    } else {
        delete axios.defaults.headers.common["Authorization"];
        token.value = null;
    }
}

async function me() {
    try {
        setAxiosToken();
        const res = await axios.get("/api/me");
        alert(JSON.stringify(res.data, null, 2));
    } catch (e) {
        alert("Not logged in or token invalid");
    }
}

async function logout() {
    localStorage.removeItem("token");
    setAxiosToken();
    page.value = "login";
}

onMounted(() => {
    setAxiosToken();
    if (token.value) {
        page.value = "dashboard";
    }
});
</script>
<style scoped>
/* NAVBAR */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(90deg, #2563eb, #1d4ed8);
    padding: 12px 24px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
}

.brand {
    color: #ffffff;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* RIGHT SIDE BUTTONS */
.nav-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* BUTTON BASE */
.nav-btn {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
}

/* HOVER EFFECT */
.nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

/* GHOST (About) */
.nav-btn.ghost {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.nav-btn.ghost:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* LOGOUT */
.nav-btn.danger {
    background: #ef4444;
}

.nav-btn.danger:hover {
    background: #dc2626;
}
</style>
