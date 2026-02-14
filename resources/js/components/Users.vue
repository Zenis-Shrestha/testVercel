<template>
    <div class="card">
        <div class="header">
            <h2>All Users</h2>
            <button class="btn" @click="fetchUsers" :disabled="loading">
                {{ loading ? "Refreshing..." : "Refresh" }}
            </button>

            <button class="btn" @click="createUsers" :disabled="loading">
                {{ loading ? "Refreshing..." : "Create User" }}
            </button>
        </div>

        <p v-if="loading">Loading users...</p>
        <p v-if="error" class="error">{{ error }}</p>

        <div v-if="!loading && users.length === 0 && !error">
            No users found.
        </div>

        <table v-if="!loading && users.length" class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in users" :key="u.id">
                    <td>{{ u.id }}</td>
                    <td>{{ u.name }}</td>
                    <td>{{ u.email }}</td>
                    <td>{{ formatDate(u.created_at) }}</td>
                    <td>
                        <button class="btn" @click="viewDetails(u)">Details</button>
                        <button class="btn danger" @click="deleteUser(u.id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";

const users = ref([]);
const loading = ref(true);
const error = ref("");

const emit = defineEmits(["go-create"]);

function createUsers() {
    emit("go-create");
}

function formatDate(value) {
    if (!value) return "-";
    // simple formatting
    return new Date(value).toLocaleString();
}

async function fetchUsers() {
    loading.value = true;
    error.value = "";

    try {
        const res = await api.get("/users");
        users.value = res.data;
    } catch (e) {
        const status = e?.response?.status;
        if (status === 401) {
            error.value = "Unauthorized. Please login again.";
        } else {
            error.value = "Failed to load users.";
        }
    } finally {
        loading.value = false;
    }
}
function viewDetails(user) {
    alert(`
ID: ${user.id}
Name: ${user.name}
Email: ${user.email}
`);
}

async function deleteUser(id) {
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
        await api.delete(`/users/${id}`);
        fetchUsers(); // refresh list automatically
    } catch (e) {
        alert("Failed to delete user");
    }
}



onMounted(fetchUsers);
</script>

<style scoped>
.card {
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: white;
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.btn {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    background: #f8fafc;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

.table th,
.table td {
    border-bottom: 1px solid #eee;
    padding: 10px;
    text-align: left;
}

.table th {
    background: #f8fafc;
    font-weight: 700;
}

.error {
    color: #b91c1c;
    margin-top: 10px;
}
.danger {
    background: #ef4444;
    color: white;
}
</style>
