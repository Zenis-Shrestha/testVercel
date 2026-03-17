<template>
  <div class="card">
    <h2>About</h2>

    <p v-if="loading">Loading...</p>
    <p v-else>{{ message }}</p>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";

const message = ref("");
const loading = ref(true);
const error = ref("");

onMounted(async () => {
  try {
    const res = await api.get("/about");
    message.value = res.data.message;
  } catch (e) {
    error.value = "Failed to load about info";
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.card {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
}
.error {
  color: #b91c1c;
}
</style>
