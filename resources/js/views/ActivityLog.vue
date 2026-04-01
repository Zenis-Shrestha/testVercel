<template>
    <div class="page">

        <div class="page-header">
            <div>
                <h1 class="page-title">Activity Log</h1>
                <p class="page-sub">Track everything that happens in the system</p>
            </div>
            <button class="btn-refresh" @click="fetchLogs" :disabled="loading">
                {{ loading ? 'Refreshing...' : 'Refresh' }}
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="table-card">
            <div v-for="i in 5" :key="i" class="skeleton-row">
                <div class="skel skel-sm"></div>
                <div class="skel skel-lg"></div>
                <div class="skel skel-md"></div>
            </div>
        </div>

        <!-- Empty -->
        <div v-else-if="logs.length === 0" class="empty-state">
            <p>No activity recorded yet.</p>
        </div>

        <!-- Logs Table -->
        <div v-else class="table-card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id" class="table-row">
                        <td>
                            <span class="badge" :class="badgeClass(log.action)">
                                {{ log.action }}
                            </span>
                        </td>
                        <td class="desc-cell">{{ log.description }}</td>
                        <td class="user-cell">{{ log.user_name }}</td>
                        <td class="ip-cell">{{ log.ip_address }}</td>
                        <td class="time-cell">{{ log.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const logs = ref([]);
const loading = ref(true);

const badgeClass = (action) => ({
    'created' : 'badge-green',
    'updated' : 'badge-blue',
    'deleted' : 'badge-red',
    'login'   : 'badge-gray',
    'logout'  : 'badge-gray',
}[action] ?? 'badge-gray');

async function fetchLogs() {
    loading.value = true;
    try {
        const res = await api.get('/activity-logs');
        logs.value = res.data;
    } finally {
        loading.value = false;
    }
}

onMounted(fetchLogs);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500&display=swap');
* { box-sizing: border-box; }

.page {
    max-width: 960px;
    margin: 0 auto;
    padding: 36px 24px;
    font-family: 'DM Sans', sans-serif;
}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}
.page-title { font-size: 22px; font-weight: 500; color: #1a202c; margin: 0 0 4px; }
.page-sub { font-size: 13px; color: #718096; margin: 0; }

.btn-refresh {
    padding: 8px 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: #4a5568;
    cursor: pointer;
}
.btn-refresh:hover:not(:disabled) { background: #f7fafc; }
.btn-refresh:disabled { opacity: 0.6; cursor: not-allowed; }

.table-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.table thead tr {
    background: #f7fafc;
    border-bottom: 1px solid #e2e8f0;
}
.table th {
    padding: 12px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #718096;
}
.table-row { border-bottom: 1px solid #f0f4f8; }
.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f7fafc; }
.table td { padding: 14px 20px; color: #2d3748; vertical-align: middle; }

.desc-cell { color: #4a5568; max-width: 300px; }
.user-cell { font-weight: 500; color: #1a202c; }
.ip-cell   { color: #a0aec0; font-size: 13px; font-family: monospace; }
.time-cell { color: #a0aec0; font-size: 13px; white-space: nowrap; }

/* Badges */
.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}
.badge-green  { background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; }
.badge-blue   { background: #EBF4FF; color: #185FA5; border: 1px solid #bee3f8; }
.badge-red    { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }
.badge-gray   { background: #f7fafc; color: #718096; border: 1px solid #e2e8f0; }

/* Skeleton */
.skeleton-row {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
    border-bottom: 1px solid #f7fafc;
}
.skel {
    height: 12px;
    background: #edf2f7;
    border-radius: 6px;
    animation: pulse 1.4s ease-in-out infinite;
}
.skel-sm  { width: 60px; }
.skel-md  { width: 120px; }
.skel-lg  { width: 240px; }
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

.empty-state {
    text-align: center;
    padding: 48px;
    color: #a0aec0;
    font-size: 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
</style>
