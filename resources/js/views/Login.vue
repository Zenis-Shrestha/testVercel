<template>
    <div style="max-width: 350px;">
        <h1>Login</h1>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <input v-model="email" type="email" placeholder="Email" />
            <input v-model="password" type="password" placeholder="Password" />
            <button id="test" @click="login">Login</button>
        </div>
        <p v-if="error" style="color:red; margin-top:10px;">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
const router = useRouter();

const email = ref("");
const password = ref("");
const error = ref("");

async function login() {
  error.value = "";

  try {
    const res = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
    });
    localStorage.setItem("token", res.data.token);
    // 🔥 redirect to dashboard
    router.push("/users");

  } catch (err) {
    error.value = "Invalid credentials";
  }
}
</script>>


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

#test {
    width: 40%;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-left: 70px;
    background: linear-gradient(90deg, #2563eb, #1d4ed8);
}

</style>
