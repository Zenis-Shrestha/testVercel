<template>
  <div id="app">
    <!-- Navigation Bar (only shown when authenticated) -->
    <nav v-if="authStore.isAuthenticated" class="navbar">
      <div class="brand">My Laravel Vue App</div>

      <div class="nav-right">
        <router-link to="/dashboard" class="nav-btn">Dashboard</router-link>
        <router-link to="/users" class="nav-btn">Users</router-link>
        <router-link to="/about-me" class="nav-btn">About Me</router-link>
        <span v-if="authStore.user" class="nav-btn ghost">
          {{ authStore.user.name }}
        </span>
        <button @click="handleLogout" class="nav-btn danger">Logout</button>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();

onMounted(async () => {
  if (authStore.isAuthenticated && !authStore.user) {
    await authStore.fetchUser();
  }
});

const handleLogout = async () => {
  if (confirm('Are you sure you want to logout?')) {
    await authStore.logout();
  }
};
</script>

<style scoped>
#app {
  min-height: 100vh;
  background: #f5f5f5;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Navbar Styles */
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(90deg, #2563eb, #1d4ed8);
  padding: 12px 24px;
  box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
  position: sticky;
  top: 0;
  z-index: 100;
}

.brand {
  color: #ffffff;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

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
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}

.nav-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-1px);
}

/* Active route styling */
.nav-btn.router-link-active {
  background: rgba(255, 255, 255, 0.35);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.nav-btn.ghost {
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.5);
  cursor: default;
}

.nav-btn.ghost:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: none;
}

.nav-btn.danger {
  background: #ef4444;
}

.nav-btn.danger:hover {
  background: #dc2626;
}

/* Main Content */
.main-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* Responsive */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    gap: 10px;
    padding: 15px;
  }

  .nav-right {
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }

  .brand {
    margin-bottom: 10px;
  }
}
</style>
