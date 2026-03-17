import { defineStore } from 'pinia';
import api from '../services/api';
import router from '../router';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user,
    isLoading: (state) => state.loading,
  },

  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.post('/login', credentials);
        this.token = response.data.token;
        localStorage.setItem('token', response.data.token);
        await this.fetchUser();
        router.push('/dashboard');
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async register(userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.post('/register', userData);
        this.token = response.data.token;
        localStorage.setItem('token', response.data.token);
        await this.fetchUser();
        router.push('/dashboard');
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        await api.post('/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.token = null;
        this.error = null;
        localStorage.removeItem('token');
        this.loading = false;
        router.push('/login');
      }
    },

    async fetchUser() {
      if (!this.token) return;
      try {
        const response = await api.get('/me');
        this.user = response.data;
        return response.data;
      } catch (error) {
        console.error('Failed to fetch user:', error);
        this.logout();
      }
    },

    clearError() {
      this.error = null;
    },
  },
});
