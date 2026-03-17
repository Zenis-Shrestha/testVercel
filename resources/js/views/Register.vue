<template>
    <div class="card">
        <h2>Register</h2>

        <form @submit.prevent="register">
            <label>Name</label>
            <input v-model.trim="form.name" type="text" placeholder="Your name" />

            <label>Email</label>
            <input v-model.trim="form.email" type="email" placeholder="you@email.com" />

            <label>Password</label>
            <input v-model="form.password" type="password" placeholder="Min 8 characters" />

            <label>Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" placeholder="Repeat password" />

            <button type="submit" :disabled="loading">
                {{ loading ? "Creating..." : "Register" }}
            </button>

            <p v-if="error" class="error">{{ error }}</p>
            <p v-if="success" class="success">{{ success }}</p>
        </form>
    </div>
</template>

<script setup>
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";
import { reactive, ref } from "vue";
import api from "../services/api";
const toast = useToast();
const router = useRouter();
const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const loading = ref(false);
const error = ref("");
const success = ref("");

function getErrorMessage(e) {
    const data = e?.response?.data;
    if (data?.message) return data.message;
    if (data?.errors) {
        const k = Object.keys(data.errors)[0];
        return data.errors[k][0];
    }
    return "Registration failed";
}

async function register() {
    loading.value = true;
    error.value = "";
    success.value = "";

    try {
        const res = await api.post("/register", form);

        // If backend returns token but you DON'T want auto login:
        // just don't store it
        // localStorage.setItem("token", res.data.token);

        toast.success("Registration successful! Please login.");

        // Reset form
        form.name = "";
        form.email = "";
        form.password = "";
        form.password_confirmation = "";

        // Redirect after short delay (optional)
        setTimeout(() => {
            router.push("/login");
        }, 1000);

    } catch (e) {
        const msg = getErrorMessage(e);
        error.value = msg;
        toast.error(msg);
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.card {
    max-width: 420px;
    margin: 40px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
}

label {
    display: block;
    margin-top: 12px;
    margin-bottom: 6px;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

button {
    width: 100%;
    margin-top: 16px;
    padding: 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.error {
    color: #b91c1c;
    margin-top: 12px;
}

.success {
    color: #15803d;
    margin-top: 12px;
}
</style>
