<template>
    <div id="app">

        <!-- Navigation Bar -->
        <nav v-if="authStore.isAuthenticated" class="navbar">
            <div class="navbar-inner">

                <!-- Brand -->
                <router-link to="/dashboard" class="brand">
                    <div class="brand-logo">LVL</div>
                    <span class="brand-name">Learning Vue Laravel</span>
                </router-link>

                <!-- Center Links -->
                <div class="nav-links">
                    <router-link to="/dashboard" class="nav-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                        Dashboard
                    </router-link>
                    <router-link to="/users" class="nav-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Users
                    </router-link>
                    <router-link to="/about-me" class="nav-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        About Me
                    </router-link>

                           <router-link to="/chat" class="nav-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Chat
                    </router-link>


                    <router-link to="/activity-logs" class="nav-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M13 16h-1v-4h-1m1-4H8m7 4h-1v-4h-1m1-4h-4m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        Activity Log
                    </router-link>
                </div>

                <!-- Right: user + logout -->
                <div class="nav-right">
                    <div v-if="authStore.user" class="user-pill">
                        <div class="user-avatar">{{ initials(authStore.user.name) }}</div>
                        <span class="user-name">{{ authStore.user.name }}</span>
                    </div>
                    <button @click="handleLogout" class="logout-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </div>

                <!-- Mobile Hamburger -->
                <button class="hamburger" @click="menuOpen = !menuOpen">
                    <svg v-if="!menuOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div v-if="menuOpen" class="mobile-menu">
                <router-link to="/dashboard" class="mobile-link" @click="menuOpen = false">Dashboard</router-link>
                <router-link to="/users" class="mobile-link" @click="menuOpen = false">Users</router-link>
                <router-link to="/about-me" class="mobile-link" @click="menuOpen = false">About Me</router-link>
                <div class="mobile-divider"></div>
                <button @click="handleLogout" class="mobile-logout">Logout</button>
            </div>
        </nav>

        <!-- Logout confirm modal -->
        <div v-if="showLogoutModal" class="modal-backdrop" @click.self="showLogoutModal = false">
            <div class="modal">
                <div class="modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </div>
                <h3 class="modal-title">Sign out?</h3>
                <p class="modal-sub">You will be returned to the login page.</p>
                <div class="modal-actions">
                    <button class="modal-cancel" @click="showLogoutModal = false">Cancel</button>
                    <button class="modal-confirm" @click="confirmLogout">Yes, sign out</button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <router-view />
            <AppToast />
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import AppToast from './components/UI/AppToast.vue';

const authStore = useAuthStore();
const menuOpen = ref(false);
const showLogoutModal = ref(false);

onMounted(async () => {
    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }
});

function initials(name) {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function handleLogout() {
    menuOpen.value = false;
    showLogoutModal.value = true;
}

async function confirmLogout() {
    showLogoutModal.value = false;
    await authStore.logout();
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap');

* { box-sizing: border-box; }

#app {
    min-height: 100vh;
    background: #f0f4f8;
    font-family: 'DM Sans', sans-serif;
}

/* Navbar */
.navbar {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
    height: 60px;
    display: flex;
    align-items: center;
    gap: 32px;
}

/* Brand */
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    flex-shrink: 0;
}
.brand-logo {
    width: 34px; height: 34px;
    border-radius: 9px;
    background: #185FA5;
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    display: flex; align-items: center; justify-content: center;
    letter-spacing: 0.03em;
}
.brand-name {
    font-size: 15px;
    font-weight: 500;
    color: #1a202c;
}

/* Nav Links */
.nav-links {
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 1;
}
.nav-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #718096;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}
.nav-link:hover {
    background: #f7fafc;
    color: #2d3748;
}
.nav-link.router-link-active {
    background: #EBF4FF;
    color: #185FA5;
}

/* Right Side */
.nav-right {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.user-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 12px 5px 5px;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
}
.user-avatar {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: #185FA5;
    color: #fff;
    font-size: 10px;
    font-weight: 500;
    display: flex; align-items: center; justify-content: center;
}
.user-name {
    font-size: 13px;
    font-weight: 500;
    color: #2d3748;
}

.logout-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    color: #718096;
    cursor: pointer;
    transition: all 0.15s;
}
.logout-btn:hover {
    background: #fff5f5;
    border-color: #fed7d7;
    color: #e53e3e;
}

/* Hamburger */
.hamburger {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    color: #4a5568;
    padding: 4px;
    margin-left: auto;
}

/* Mobile Menu */
.mobile-menu {
    border-top: 1px solid #f0f4f8;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.mobile-link {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    color: #4a5568;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.12s;
}
.mobile-link:hover, .mobile-link.router-link-active {
    background: #EBF4FF;
    color: #185FA5;
}
.mobile-divider {
    height: 1px;
    background: #f0f4f8;
    margin: 6px 0;
}
.mobile-logout {
    padding: 10px 14px;
    background: none;
    border: none;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    color: #e53e3e;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.12s;
}
.mobile-logout:hover { background: #fff5f5; }

/* Logout Modal */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}
.modal {
    background: #fff;
    border-radius: 14px;
    padding: 32px 28px;
    max-width: 340px;
    width: 90%;
    text-align: center;
}
.modal-icon {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: #f7fafc;
    color: #185FA5;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.modal-title {
    font-size: 17px;
    font-weight: 500;
    color: #1a202c;
    margin: 0 0 8px;
}
.modal-sub {
    font-size: 13px;
    color: #718096;
    margin: 0 0 24px;
    line-height: 1.6;
}
.modal-actions {
    display: flex;
    gap: 10px;
}
.modal-cancel {
    flex: 1;
    padding: 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    color: #4a5568;
    cursor: pointer;
}
.modal-cancel:hover { background: #f7fafc; }
.modal-confirm {
    flex: 1;
    padding: 10px;
    background: #185FA5;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
}
.modal-confirm:hover { background: #0C447C; }

/* Main */
.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Responsive */
@media (max-width: 700px) {
    .nav-links, .nav-right { display: none; }
    .hamburger { display: flex; }
    .brand-name { display: none; }
}
</style>
